<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::query()
            ->withCount(['bookings', 'reviews'])
            ->when(
                $request->string('q')->toString(),
                fn ($q, string $term) => $q->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                })
            )
            ->when(
                $request->string('role')->toString(),
                fn ($q, string $role) => $q->where('role', $role)
            )
            ->latest()
            ->paginate(20)
            ->withQueryString()
            ->through(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
                'role_label' => $user->role->name,
                'email_verified_at' => $user->email_verified_at?->toIso8601String(),
                'bookings_count' => $user->bookings_count,
                'reviews_count' => $user->reviews_count,
                'created_at' => $user->created_at?->toIso8601String(),
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => [
                'q' => $request->string('q')->toString() ?: null,
                'role' => $request->string('role')->toString() ?: null,
            ],
            'roles' => collect(UserRole::cases())
                ->map(fn (UserRole $r) => ['value' => $r->value, 'label' => $r->name])
                ->values(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::enum(UserRole::class)],
        ]);

        if ($request->user()->id === $user->id && $data['role'] !== UserRole::Admin->value) {
            return back()->withErrors([
                'role' => 'You cannot remove admin from your own account.',
            ]);
        }

        $user->update(['role' => $data['role']]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => "{$user->name} is now {$user->role->name}.",
        ]);
    }
}
