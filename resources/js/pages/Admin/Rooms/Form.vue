<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, ImagePlus, Loader2, Plus, X } from 'lucide-vue-next';
import {
    index as adminRoomsIndex,
    store as adminRoomsStore,
    update as adminRoomsUpdate,
} from '@/actions/App/Http/Controllers/Admin/RoomController';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';

type Option = { value: string; label: string };

type AdminRoom = {
    id: number;
    name: string;
    slug: string;
    description: string;
    type: string;
    price_per_night: number;
    capacity: number;
    floor: number;
    status: string;
    thumbnail: string | null;
    images: string[];
    amenities?: { id: number; name: string }[];
};

const props = defineProps<{
    room: { data: AdminRoom } | null;
    options: { types: Option[]; statuses: Option[] };
}>();

const isEdit = computed(() => Boolean(props.room));

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: adminDashboard().url },
            { title: 'Rooms', href: adminRoomsIndex().url },
            { title: 'Editor', href: '#' },
        ],
    },
});

const initial = props.room?.data;

const form = useForm<{
    name: string;
    description: string;
    type: string;
    price_per_night: string;
    capacity: number;
    floor: number;
    status: string;
    thumbnail: File | null;
    remove_thumbnail: boolean;
    images: File[];
    kept_images: string[];
    amenities: string[];
    _method?: 'PUT';
}>({
    name: initial?.name ?? '',
    description: initial?.description ?? '',
    type: initial?.type ?? props.options.types[0]?.value ?? '',
    price_per_night: initial ? String(initial.price_per_night) : '',
    capacity: initial?.capacity ?? 2,
    floor: initial?.floor ?? 1,
    status: initial?.status ?? props.options.statuses[0]?.value ?? '',
    thumbnail: null,
    remove_thumbnail: false,
    images: [],
    kept_images: initial?.images ?? [],
    amenities: initial?.amenities?.map((a) => a.name) ?? [],
});

const newAmenity = ref('');

const addAmenity = () => {
    const value = newAmenity.value.trim();
    if (!value) return;
    if (form.amenities.includes(value)) {
        newAmenity.value = '';
        return;
    }
    form.amenities = [...form.amenities, value];
    newAmenity.value = '';
};

const removeAmenity = (name: string) => {
    form.amenities = form.amenities.filter((a) => a !== name);
};

const handleThumbnail = (event: Event) => {
    const input = event.target as HTMLInputElement;
    form.thumbnail = input.files?.[0] ?? null;
    form.remove_thumbnail = false;
};

const handleImages = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (!input.files) return;
    form.images = [...form.images, ...Array.from(input.files)];
    input.value = '';
};

const removeNewImage = (index: number) => {
    form.images = form.images.filter((_, i) => i !== index);
};

const removeKeptImage = (path: string) => {
    form.kept_images = form.kept_images.filter((p) => p !== path);
};

const previewUrl = (file: File) => window.URL.createObjectURL(file);

const submit = () => {
    if (isEdit.value && initial) {
        form.transform((data) => ({ ...data, _method: 'PUT' as const })).post(
            adminRoomsUpdate({ room: initial.slug }).url,
            { forceFormData: true },
        );
    } else {
        form.post(adminRoomsStore().url, { forceFormData: true });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Admin · Edit room' : 'Admin · New room'" />

    <div class="mx-auto max-w-4xl space-y-6 p-4 md:p-6">
        <div>
            <Link
                :href="adminRoomsIndex().url"
                class="inline-flex items-center gap-1 text-sm text-slate-500 transition hover:text-[#1a2744]"
            >
                <ArrowLeft class="size-4" />
                Back to rooms
            </Link>
            <h1 class="mt-3 font-serif text-3xl text-[#1a2744]">
                {{ isEdit ? `Edit ${initial?.name}` : 'New room' }}
            </h1>
        </div>

        <form
            class="space-y-6 rounded-xl border border-slate-200 bg-white p-6"
            @submit.prevent="submit"
        >
            <div class="grid gap-5 sm:grid-cols-2">
                <label class="space-y-1.5">
                    <span class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Name
                    </span>
                    <input
                        v-model="form.name"
                        type="text"
                        class="block w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                    />
                    <p v-if="form.errors.name" class="text-xs text-rose-600">
                        {{ form.errors.name }}
                    </p>
                </label>

                <label class="space-y-1.5">
                    <span class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Type
                    </span>
                    <select
                        v-model="form.type"
                        class="block w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                    >
                        <option
                            v-for="t in options.types"
                            :key="t.value"
                            :value="t.value"
                        >
                            {{ t.label }}
                        </option>
                    </select>
                    <p v-if="form.errors.type" class="text-xs text-rose-600">
                        {{ form.errors.type }}
                    </p>
                </label>

                <label class="space-y-1.5">
                    <span class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Price per night ($)
                    </span>
                    <input
                        v-model="form.price_per_night"
                        type="number"
                        step="0.01"
                        min="0"
                        class="block w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                    />
                    <p v-if="form.errors.price_per_night" class="text-xs text-rose-600">
                        {{ form.errors.price_per_night }}
                    </p>
                </label>

                <label class="space-y-1.5">
                    <span class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Status
                    </span>
                    <select
                        v-model="form.status"
                        class="block w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                    >
                        <option
                            v-for="s in options.statuses"
                            :key="s.value"
                            :value="s.value"
                        >
                            {{ s.label }}
                        </option>
                    </select>
                    <p v-if="form.errors.status" class="text-xs text-rose-600">
                        {{ form.errors.status }}
                    </p>
                </label>

                <label class="space-y-1.5">
                    <span class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Capacity
                    </span>
                    <input
                        v-model.number="form.capacity"
                        type="number"
                        min="1"
                        max="20"
                        class="block w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                    />
                    <p v-if="form.errors.capacity" class="text-xs text-rose-600">
                        {{ form.errors.capacity }}
                    </p>
                </label>

                <label class="space-y-1.5">
                    <span class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Floor
                    </span>
                    <input
                        v-model.number="form.floor"
                        type="number"
                        min="0"
                        max="200"
                        class="block w-full rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                    />
                    <p v-if="form.errors.floor" class="text-xs text-rose-600">
                        {{ form.errors.floor }}
                    </p>
                </label>
            </div>

            <label class="block space-y-1.5">
                <span class="text-xs font-medium uppercase tracking-wider text-slate-500">
                    Description
                </span>
                <textarea
                    v-model="form.description"
                    rows="6"
                    class="block w-full resize-y rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                />
                <p v-if="form.errors.description" class="text-xs text-rose-600">
                    {{ form.errors.description }}
                </p>
            </label>

            <div class="space-y-3">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                    Amenities
                </p>
                <div class="flex flex-wrap gap-2">
                    <span
                        v-for="amenity in form.amenities"
                        :key="amenity"
                        class="inline-flex items-center gap-1 rounded-full bg-[#1a2744]/5 px-3 py-1 text-xs text-[#1a2744]"
                    >
                        {{ amenity }}
                        <button
                            type="button"
                            class="rounded-full p-0.5 transition hover:bg-rose-50 hover:text-rose-600"
                            @click="removeAmenity(amenity)"
                        >
                            <X class="size-3" />
                        </button>
                    </span>
                </div>
                <div class="flex gap-2">
                    <input
                        v-model="newAmenity"
                        type="text"
                        placeholder="Add an amenity (e.g. WiFi)"
                        class="flex-1 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                        @keydown.enter.prevent="addAmenity"
                    />
                    <button
                        type="button"
                        class="inline-flex items-center gap-1 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-50"
                        @click="addAmenity"
                    >
                        <Plus class="size-4" />
                        Add
                    </button>
                </div>
                <p v-if="form.errors.amenities" class="text-xs text-rose-600">
                    {{ form.errors.amenities }}
                </p>
            </div>

            <div class="space-y-3">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                    Thumbnail
                </p>
                <div class="flex items-center gap-4">
                    <div
                        class="flex size-24 items-center justify-center overflow-hidden rounded-lg border border-dashed border-slate-300 bg-slate-50 text-slate-400"
                    >
                        <img
                            v-if="form.thumbnail"
                            :src="previewUrl(form.thumbnail)"
                            class="size-full object-cover"
                            alt="New thumbnail"
                        />
                        <img
                            v-else-if="initial?.thumbnail && !form.remove_thumbnail"
                            :src="initial.thumbnail"
                            class="size-full object-cover"
                            alt="Existing thumbnail"
                        />
                        <ImagePlus v-else class="size-6" />
                    </div>
                    <div class="space-y-2">
                        <label
                            class="inline-flex cursor-pointer items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-600 transition hover:bg-slate-50"
                        >
                            <ImagePlus class="size-4" />
                            Choose file
                            <input
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="handleThumbnail"
                            />
                        </label>
                        <button
                            v-if="(initial?.thumbnail && !form.remove_thumbnail) || form.thumbnail"
                            type="button"
                            class="block text-xs text-rose-600 hover:underline"
                            @click="
                                form.thumbnail = null;
                                form.remove_thumbnail = true;
                            "
                        >
                            Remove thumbnail
                        </button>
                    </div>
                </div>
                <p v-if="form.errors.thumbnail" class="text-xs text-rose-600">
                    {{ form.errors.thumbnail }}
                </p>
            </div>

            <div class="space-y-3">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                    Gallery
                </p>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div
                        v-for="path in form.kept_images"
                        :key="`kept-${path}`"
                        class="group relative aspect-square overflow-hidden rounded-lg border border-slate-200"
                    >
                        <img :src="path" class="size-full object-cover" alt="" />
                        <button
                            type="button"
                            class="absolute right-1.5 top-1.5 rounded-full bg-white/90 p-1 text-rose-600 opacity-0 transition group-hover:opacity-100"
                            @click="removeKeptImage(path)"
                        >
                            <X class="size-3.5" />
                        </button>
                    </div>
                    <div
                        v-for="(file, i) in form.images"
                        :key="`new-${i}`"
                        class="group relative aspect-square overflow-hidden rounded-lg border border-emerald-200"
                    >
                        <img
                            :src="previewUrl(file)"
                            class="size-full object-cover"
                            alt=""
                        />
                        <button
                            type="button"
                            class="absolute right-1.5 top-1.5 rounded-full bg-white/90 p-1 text-rose-600 opacity-0 transition group-hover:opacity-100"
                            @click="removeNewImage(i)"
                        >
                            <X class="size-3.5" />
                        </button>
                    </div>
                    <label
                        class="flex aspect-square cursor-pointer items-center justify-center rounded-lg border border-dashed border-slate-300 bg-slate-50 text-slate-400 transition hover:border-[#c9a84c] hover:text-[#c9a84c]"
                    >
                        <ImagePlus class="size-6" />
                        <input
                            type="file"
                            accept="image/*"
                            multiple
                            class="hidden"
                            @change="handleImages"
                        />
                    </label>
                </div>
                <p v-if="form.errors.images" class="text-xs text-rose-600">
                    {{ form.errors.images }}
                </p>
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                <Link
                    :href="adminRoomsIndex().url"
                    class="rounded-md border border-slate-200 px-4 py-2 text-sm text-slate-600 transition hover:bg-slate-50"
                >
                    Cancel
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center gap-2 rounded-md bg-[#1a2744] px-5 py-2 text-sm font-medium text-white transition hover:bg-[#243558] disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                    {{ isEdit ? 'Save changes' : 'Create room' }}
                </button>
            </div>
        </form>
    </div>
</template>
