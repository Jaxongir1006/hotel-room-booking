<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';
import { Search, SlidersHorizontal } from 'lucide-vue-next';
import GuestLayout from '@/layouts/GuestLayout.vue';
import RoomCard from '@/components/rooms/RoomCard.vue';
import { index as roomsIndex } from '@/routes/rooms';
import type { Paginated, RoomFilters, RoomSummary, RoomTypeOption } from '@/types';

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
        <section class="border-b border-slate-200 bg-white">
            <div class="animate-fade-up mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                    Our rooms
                </p>
                <h1 class="mt-2 font-serif text-3xl text-[#1a2744] md:text-4xl">
                    Find your perfect retreat
                </h1>
                <p class="mt-3 max-w-2xl text-sm text-slate-500">
                    Browse the full collection. Refine by type, price, capacity, and more.
                </p>
            </div>
        </section>

        <div class="animate-fade-up-delayed-1 mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:px-6 lg:grid-cols-[280px_1fr] lg:px-8">
            <!-- Filters sidebar -->
            <aside class="space-y-6">
                <div class="rounded-xl border border-slate-200 bg-white p-5">
                    <div class="flex items-center gap-2 text-sm font-medium text-[#1a2744]">
                        <SlidersHorizontal class="size-4" />
                        Filters
                    </div>

                    <form @submit.prevent="apply" class="mt-5 space-y-5">
                        <label class="block">
                            <span class="text-xs font-medium uppercase tracking-wider text-slate-500">
                                Search
                            </span>
                            <div class="relative mt-1">
                                <Search
                                    class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400"
                                />
                                <input
                                    v-model="local.q"
                                    type="search"
                                    placeholder="Suite name…"
                                    class="w-full rounded-md border border-slate-200 py-2 pl-9 pr-3 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                                />
                            </div>
                        </label>

                        <label class="block">
                            <span class="text-xs font-medium uppercase tracking-wider text-slate-500">
                                Room type
                            </span>
                            <select
                                v-model="local.type"
                                class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                            >
                                <option value="">Any</option>
                                <option v-for="opt in roomTypes" :key="opt.value" :value="opt.value">
                                    {{ opt.label }}
                                </option>
                            </select>
                        </label>

                        <div class="grid grid-cols-2 gap-3">
                            <label class="block">
                                <span class="text-xs font-medium uppercase tracking-wider text-slate-500">
                                    Min $
                                </span>
                                <input
                                    v-model="local.min_price"
                                    type="number"
                                    min="0"
                                    placeholder="0"
                                    class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                                />
                            </label>
                            <label class="block">
                                <span class="text-xs font-medium uppercase tracking-wider text-slate-500">
                                    Max $
                                </span>
                                <input
                                    v-model="local.max_price"
                                    type="number"
                                    min="0"
                                    placeholder="1000"
                                    class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                                />
                            </label>
                        </div>

                        <label class="block">
                            <span class="text-xs font-medium uppercase tracking-wider text-slate-500">
                                Min capacity
                            </span>
                            <input
                                v-model="local.capacity"
                                type="number"
                                min="1"
                                max="10"
                                placeholder="2"
                                class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                            />
                        </label>

                        <div class="flex gap-2">
                            <button
                                type="submit"
                                class="flex-1 rounded-md bg-[#1a2744] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#243558]"
                            >
                                Apply
                            </button>
                            <button
                                type="button"
                                @click="reset"
                                class="rounded-md border border-slate-200 px-4 py-2 text-sm text-slate-600 transition hover:border-slate-300 hover:text-slate-800"
                            >
                                Reset
                            </button>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- Results -->
            <div>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-slate-500">
                        <span class="font-medium text-[#1a2744]">{{ rooms.meta.total }}</span>
                        rooms available
                    </p>
                    <label class="flex items-center gap-2 text-sm text-slate-500">
                        Sort:
                        <select
                            v-model="local.sort"
                            @change="apply"
                            class="rounded-md border border-slate-200 px-2 py-1.5 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                        >
                            <option value="newest">Newest</option>
                            <option value="price_asc">Price (low → high)</option>
                            <option value="price_desc">Price (high → low)</option>
                            <option value="rating">Top rated</option>
                        </select>
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
                    <p class="font-serif text-xl text-[#1a2744]">
                        No rooms match those filters.
                    </p>
                    <p class="mt-2 text-sm text-slate-500">
                        Try widening your range or clearing some criteria.
                    </p>
                    <button
                        type="button"
                        @click="reset"
                        class="group mt-6 inline-flex cursor-pointer items-center gap-2 rounded-md bg-[#1a2744] px-5 py-2.5 text-sm font-medium text-white shadow-md shadow-[#1a2744]/10 transition-colors duration-200 hover:bg-[#243558]"
                    >
                        Reset filters
                    </button>
                </div>

                <!-- Pagination -->
                <nav
                    v-if="rooms.meta.last_page > 1"
                    class="mt-10 flex flex-wrap items-center justify-center gap-1"
                >
                    <template v-for="link in rooms.meta.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            v-html="link.label"
                            preserve-scroll
                            preserve-state
                            class="min-w-[40px] rounded-md border border-slate-200 px-3 py-1.5 text-center text-sm transition hover:border-[#c9a84c] hover:text-[#c9a84c]"
                            :class="
                                link.active
                                    ? 'border-[#1a2744] bg-[#1a2744] text-white hover:bg-[#1a2744] hover:text-white'
                                    : 'text-slate-600'
                            "
                        />
                        <span
                            v-else
                            v-html="link.label"
                            class="min-w-[40px] px-3 py-1.5 text-center text-sm text-slate-300"
                        />
                    </template>
                </nav>
            </div>
        </div>
    </GuestLayout>
</template>
