<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';
import { Search, SlidersHorizontal } from 'lucide-vue-next';
import GuestLayout from '@/layouts/GuestLayout.vue';
import RoomCard from '@/components/rooms/RoomCard.vue';
import { index as roomsIndex } from '@/routes/rooms';
import type {
    Paginated,
    RoomFilters,
    RoomSummary,
    RoomTypeOption,
} from '@/types';

const props = defineProps<{
    rooms: Paginated<RoomSummary>;
    filters: RoomFilters;
    roomTypes: RoomTypeOption[];
}>();

const local = reactive<{
    q: string;
    type: string;
    min_price: string;
    max_price: string;
    capacity: string;
    sort: string;
}>({
    q: props.filters.q ?? '',
    type: props.filters.type ?? '',
    min_price: props.filters.min_price?.toString() ?? '',
    max_price: props.filters.max_price?.toString() ?? '',
    capacity: props.filters.capacity?.toString() ?? '',
    sort: props.filters.sort ?? 'newest',
});

const buildQuery = () => {
    const params: Record<string, string> = {};
    if (local.q) params.q = local.q;
    if (local.type) params.type = local.type;
    if (local.min_price) params.min_price = local.min_price;
    if (local.max_price) params.max_price = local.max_price;
    if (local.capacity) params.capacity = local.capacity;
    if (local.sort && local.sort !== 'newest') params.sort = local.sort;
    return params;
};

const apply = () => {
    router.get(roomsIndex().url, buildQuery(), {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

const reset = () => {
    local.q = '';
    local.type = '';
    local.min_price = '';
    local.max_price = '';
    local.capacity = '';
    local.sort = 'newest';
    apply();
};

let debounceTimer: ReturnType<typeof setTimeout> | null = null;
watch(
    () => local.q,
    () => {
        if (debounceTimer) clearTimeout(debounceTimer);
        debounceTimer = setTimeout(apply, 350);
    },
);
</script>

<template>
    <Head title="Rooms · Aurelia Stay" />

    <GuestLayout>
        <!-- Luxury Header Banner -->
        <section class="relative overflow-hidden bg-[#1a2744] py-16 text-white">
            <div
                aria-hidden="true"
                class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1600&q=80')] bg-cover bg-center opacity-15"
            />
            <div
                aria-hidden="true"
                class="absolute inset-0 bg-gradient-to-b from-[#1a2744]/30 to-[#1a2744]"
            />
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <p
                    class="animate-fade-up text-xs font-semibold tracking-[0.4em] text-[#c9a84c] uppercase"
                >
                    Our rooms
                </p>
                <h1
                    class="animate-fade-up-delayed-1 mt-3 font-serif text-4xl text-white md:text-5xl"
                >
                    Find your perfect
                    <span class="font-normal text-[#c9a84c] italic"
                        >retreat</span
                    >
                </h1>
                <p
                    class="animate-fade-up-delayed-2 mt-4 max-w-2xl text-sm leading-relaxed text-slate-300"
                >
                    Immerse yourself in our collection of thoughtfully curated
                    spaces, designed to be your private sanctuary of comfort and
                    quiet luxury.
                </p>
            </div>
        </section>

        <div
            class="animate-fade-up-delayed-1 mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:px-6 lg:grid-cols-[280px_1fr] lg:px-8"
        >
            <!-- Filters sidebar -->
            <aside class="space-y-6 self-start lg:sticky lg:top-8">
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-300 hover:shadow-md"
                >
                    <div
                        class="flex items-center gap-2 text-sm font-semibold text-[#1a2744]"
                    >
                        <SlidersHorizontal class="size-4" />
                        Refine Search
                    </div>

                    <form @submit.prevent="apply" class="mt-6 space-y-5">
                        <label class="block">
                            <span
                                class="text-xs font-semibold tracking-wider text-slate-400 uppercase"
                            >
                                Search Keywords
                            </span>
                            <div class="relative mt-2">
                                <Search
                                    class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-slate-400"
                                />
                                <input
                                    v-model="local.q"
                                    type="search"
                                    placeholder="Suite name or keyword..."
                                    class="w-full rounded-lg border border-slate-200 bg-slate-50/50 py-2.5 pr-3 pl-10 text-sm text-slate-800 placeholder-slate-400 transition-all duration-200 focus:border-[#c9a84c] focus:bg-white focus:ring-1 focus:ring-[#c9a84c] focus:outline-none"
                                />
                            </div>
                        </label>

                        <label class="block">
                            <span
                                class="text-xs font-semibold tracking-wider text-slate-400 uppercase"
                            >
                                Room Collection
                            </span>
                            <div class="relative mt-2">
                                <select
                                    v-model="local.type"
                                    class="w-full appearance-none rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 text-sm text-slate-800 transition-all duration-200 focus:border-[#c9a84c] focus:bg-white focus:ring-1 focus:ring-[#c9a84c] focus:outline-none"
                                >
                                    <option value="">All Collections</option>
                                    <option
                                        v-for="opt in roomTypes"
                                        :key="opt.value"
                                        :value="opt.value"
                                    >
                                        {{ opt.label }}
                                    </option>
                                </select>
                                <span
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-[10px] text-slate-400"
                                    >▼</span
                                >
                            </div>
                        </label>

                        <div class="grid grid-cols-2 gap-3">
                            <label class="block">
                                <span
                                    class="text-xs font-semibold tracking-wider text-slate-400 uppercase"
                                >
                                    Min Price
                                </span>
                                <input
                                    v-model="local.min_price"
                                    type="number"
                                    min="0"
                                    placeholder="$ Min"
                                    class="mt-2 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 text-sm text-slate-800 transition-all duration-200 focus:border-[#c9a84c] focus:bg-white focus:ring-1 focus:ring-[#c9a84c] focus:outline-none"
                                />
                            </label>
                            <label class="block">
                                <span
                                    class="text-xs font-semibold tracking-wider text-slate-400 uppercase"
                                >
                                    Max Price
                                </span>
                                <input
                                    v-model="local.max_price"
                                    type="number"
                                    min="0"
                                    placeholder="$ Max"
                                    class="mt-2 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 text-sm text-slate-800 transition-all duration-200 focus:border-[#c9a84c] focus:bg-white focus:ring-1 focus:ring-[#c9a84c] focus:outline-none"
                                />
                            </label>
                        </div>

                        <label class="block">
                            <span
                                class="text-xs font-semibold tracking-wider text-slate-400 uppercase"
                            >
                                Minimum Capacity
                            </span>
                            <input
                                v-model="local.capacity"
                                type="number"
                                min="1"
                                max="10"
                                placeholder="Number of guests"
                                class="mt-2 w-full rounded-lg border border-slate-200 bg-slate-50/50 px-3 py-2.5 text-sm text-slate-800 transition-all duration-200 focus:border-[#c9a84c] focus:bg-white focus:ring-1 focus:ring-[#c9a84c] focus:outline-none"
                            />
                        </label>

                        <div class="flex gap-3 pt-2">
                            <button
                                type="submit"
                                class="flex-1 cursor-pointer rounded-lg bg-[#1a2744] px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-[#1a2744]/10 transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#243558] hover:shadow-lg"
                            >
                                Apply
                            </button>
                            <button
                                type="button"
                                @click="reset"
                                class="cursor-pointer rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition-all duration-200 hover:bg-slate-50 hover:text-slate-800"
                            >
                                Reset
                            </button>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- Results -->
            <div>
                <div
                    class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-5"
                >
                    <p class="text-sm text-slate-500">
                        Showing
                        <span class="font-semibold text-[#1a2744]">{{
                            rooms.meta.total
                        }}</span>
                        curated rooms
                    </p>
                    <label
                        class="flex items-center gap-2.5 text-sm text-slate-500"
                    >
                        Sort by:
                        <div class="relative">
                            <select
                                v-model="local.sort"
                                @change="apply"
                                class="cursor-pointer appearance-none rounded-lg border border-slate-200 bg-white px-3 py-2 pr-8 text-sm transition-all duration-200 focus:border-[#c9a84c] focus:ring-1 focus:ring-[#c9a84c] focus:outline-none"
                            >
                                <option value="newest">Newest Additions</option>
                                <option value="price_asc">
                                    Price: Low to High
                                </option>
                                <option value="price_desc">
                                    Price: High to Low
                                </option>
                                <option value="rating">Guest Rating</option>
                            </select>
                            <span
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-[10px] text-slate-400"
                                >▼</span
                            >
                        </div>
                    </label>
                </div>

                <div
                    v-if="rooms.data.length"
                    class="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-3"
                >
                    <div
                        v-for="(room, idx) in rooms.data"
                        :key="room.id"
                        :style="`animation: aurelia-fade-up 520ms cubic-bezier(0.22, 1, 0.36, 1) ${30 * idx}ms both;`"
                    >
                        <RoomCard :room="room" />
                    </div>
                </div>

                <div
                    v-else
                    class="animate-fade-up mt-12 rounded-2xl border border-dashed border-[#c9a84c]/40 bg-gradient-to-br from-[#fdf8ec] to-white p-12 text-center"
                >
                    <p class="font-serif text-2xl text-[#1a2744]">
                        No matching sanctuaries found
                    </p>
                    <p class="mt-2 text-sm text-slate-500">
                        Try widening your price range or selecting a different
                        room type.
                    </p>
                    <button
                        type="button"
                        @click="reset"
                        class="group mt-6 inline-flex cursor-pointer items-center gap-2 rounded-md bg-[#1a2744] px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-[#1a2744]/10 transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#243558]"
                    >
                        Clear all filters
                    </button>
                </div>

                <!-- Pagination -->
                <nav
                    v-if="rooms.meta.last_page > 1"
                    class="mt-12 flex flex-wrap items-center justify-center gap-2"
                >
                    <template
                        v-for="link in rooms.meta.links"
                        :key="link.label"
                    >
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            v-html="link.label"
                            preserve-scroll
                            preserve-state
                            class="flex h-[42px] min-w-[42px] items-center justify-center rounded-full border border-slate-200 px-3 text-center text-sm font-semibold transition-all duration-200 hover:border-[#c9a84c] hover:text-[#c9a84c] hover:shadow-sm"
                            :class="
                                link.active
                                    ? 'border-[#1a2744] bg-[#1a2744] text-white hover:border-[#1a2744] hover:bg-[#1a2744] hover:text-white'
                                    : 'bg-white text-slate-600'
                            "
                        />
                        <span
                            v-else
                            v-html="link.label"
                            class="flex h-[42px] min-w-[42px] items-center justify-center px-3 text-center text-sm text-slate-300"
                        />
                    </template>
                </nav>
            </div>
        </div>
    </GuestLayout>
</template>
