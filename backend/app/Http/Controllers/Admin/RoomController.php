<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoomStatus;
use App\Enums\RoomType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Rooms\StoreRequest;
use App\Http\Requests\Admin\Rooms\UpdateRequest;
use App\Http\Resources\Admin\AdminRoomResource;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class RoomController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Room::class);

        $rooms = Room::query()
            ->withCount(['bookings', 'reviews'])
            ->when(
                $request->string('q')->toString(),
                fn ($q, string $term) => $q->where('name', 'like', "%{$term}%")
            )
            ->when(
                $request->string('status')->toString(),
                fn ($q, string $status) => $q->where('status', $status)
            )
            ->when(
                $request->string('type')->toString(),
                fn ($q, string $type) => $q->where('type', $type)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Rooms/Index', [
            'rooms' => AdminRoomResource::collection($rooms),
            'filters' => [
                'q' => $request->string('q')->toString() ?: null,
                'status' => $request->string('status')->toString() ?: null,
                'type' => $request->string('type')->toString() ?: null,
            ],
            'options' => $this->options(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Room::class);

        return Inertia::render('Admin/Rooms/Form', [
            'room' => null,
            'options' => $this->options(),
        ]);
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $thumbnail = $request->file('thumbnail');
        $images = $request->file('images') ?? [];
        $amenities = $data['amenities'] ?? [];

        $room = DB::transaction(function () use ($data, $thumbnail, $images, $amenities) {
            $room = new Room;
            $room->fill([
                'name' => $data['name'],
                'description' => $data['description'],
                'type' => $data['type'],
                'price_per_night' => $data['price_per_night'],
                'capacity' => $data['capacity'],
                'floor' => $data['floor'],
                'status' => $data['status'],
            ]);
            $room->slug = $this->uniqueSlug($data['name']);
            $room->thumbnail = $thumbnail ? $this->storeImage($thumbnail) : null;
            $room->images = collect($images)
                ->map(fn (UploadedFile $file) => $this->storeImage($file))
                ->values()
                ->all();
            $room->save();

            $this->syncAmenities($room, $amenities);

            return $room;
        });

        return redirect()
            ->route('admin.rooms.index')
            ->with('toast', [
                'type' => 'success',
                'message' => "Room \"{$room->name}\" created.",
            ]);
    }

    public function edit(Room $room): Response
    {
        $this->authorize('update', $room);

        $room->load('amenities');

        return Inertia::render('Admin/Rooms/Form', [
            'room' => new AdminRoomResource($room),
            'options' => $this->options(),
        ]);
    }

    public function update(UpdateRequest $request, Room $room): RedirectResponse
    {
        $data = $request->validated();
        $thumbnail = $request->file('thumbnail');
        $newImages = $request->file('images') ?? [];
        $keptImages = $data['kept_images'] ?? [];
        $amenities = $data['amenities'] ?? [];

        DB::transaction(function () use ($room, $data, $thumbnail, $newImages, $keptImages, $amenities, $request) {
            $room->fill([
                'name' => $data['name'],
                'description' => $data['description'],
                'type' => $data['type'],
                'price_per_night' => $data['price_per_night'],
                'capacity' => $data['capacity'],
                'floor' => $data['floor'],
                'status' => $data['status'],
            ]);

            if ($room->isDirty('name')) {
                $room->slug = $this->uniqueSlug($data['name'], $room->id);
            }

            if ($thumbnail) {
                $this->deleteImage($room->thumbnail);
                $room->thumbnail = $this->storeImage($thumbnail);
            } elseif ($request->boolean('remove_thumbnail') && $room->thumbnail) {
                $this->deleteImage($room->thumbnail);
                $room->thumbnail = null;
            }

            $existing = $room->images ?? [];
            $removed = array_values(array_diff($existing, $keptImages));
            foreach ($removed as $path) {
                $this->deleteImage($path);
            }
            $newPaths = collect($newImages)
                ->map(fn (UploadedFile $file) => $this->storeImage($file))
                ->values()
                ->all();
            $room->images = array_values(array_merge($keptImages, $newPaths));

            $room->save();

            $this->syncAmenities($room, $amenities);
        });

        return redirect()
            ->route('admin.rooms.index')
            ->with('toast', [
                'type' => 'success',
                'message' => "Room \"{$room->name}\" updated.",
            ]);
    }

    public function destroy(Room $room): RedirectResponse
    {
        $this->authorize('delete', $room);

        DB::transaction(function () use ($room) {
            foreach (array_filter([$room->thumbnail, ...($room->images ?? [])]) as $path) {
                $this->deleteImage($path);
            }
            $room->delete();
        });

        return redirect()
            ->route('admin.rooms.index')
            ->with('toast', [
                'type' => 'success',
                'message' => "Room \"{$room->name}\" deleted.",
            ]);
    }

    /**
     * @param  array<int, string>  $names
     */
    protected function syncAmenities(Room $room, array $names): void
    {
        $room->amenities()->delete();

        $unique = collect($names)
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->unique()
            ->values();

        foreach ($unique as $name) {
            $room->amenities()->create(['name' => $name]);
        }
    }

    protected function storeImage(UploadedFile $file): string
    {
        return $file->store('rooms', 'public');
    }

    protected function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;

        while (
            Room::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /**
     * @return array<string, mixed>
     */
    protected function options(): array
    {
        return [
            'types' => collect(RoomType::cases())
                ->map(fn (RoomType $t) => ['value' => $t->value, 'label' => $t->label()])
                ->values(),
            'statuses' => collect(RoomStatus::cases())
                ->map(fn (RoomStatus $s) => ['value' => $s->value, 'label' => $s->label()])
                ->values(),
        ];
    }
}
