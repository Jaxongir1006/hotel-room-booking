<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowRight, ShieldCheck, Sparkles, Star, Wifi } from 'lucide-vue-next';
import { reactive } from 'vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import RoomCard from '@/components/rooms/RoomCard.vue';
import { index as roomsIndex } from '@/routes/rooms';
import type { RoomSummary } from '@/types';
import { useThreeDTilt } from '@/composables/useThreeDTilt';

defineProps<{
    canRegister: boolean;
    featuredRooms: { data: RoomSummary[] };
}>();

const {
    targetEl: heroCardEl,
    transformStyle: heroCardTransform,
    transitionStyle: heroCardTransition,
} = useThreeDTilt({ max: 10, scale: 1.02 });

const {
    targetEl: searchBarEl,
    transformStyle: searchBarTransform,
    transitionStyle: searchBarTransition,
} = useThreeDTilt({ max: 4, scale: 1.01 });

const searchForm = reactive({
    q: '',
    type: '',
});

const handleSearch = () => {
    const params: Record<string, string> = {};
    if (searchForm.q) {
        params.q = searchForm.q;
    }
    if (searchForm.type) {
        params.type = searchForm.type;
    }
    router.get(roomsIndex().url, params);
};

const testimonials = [
    {
        name: 'Sienna M.',
        rating: 5,
        text: 'The most thoughtful service I have experienced. Every detail had been considered before we even arrived.',
    },
    {
        name: 'Adrian K.',
        rating: 5,
        text: 'A genuine sanctuary in the heart of the city. The suites are nothing short of cinematic.',
    },
    {
        name: 'Priya R.',
        rating: 4,
        text: 'Refined elegance, attentive staff, and the bath products alone are worth the booking.',
    },
];
</script>

<template>
    <Head title="Aurelia Stay — Refined hospitality" />

    <GuestLayout>
        <!-- 3D Parallax Hero Section -->
        <section class="relative overflow-hidden bg-[#1a2744] text-white">
            <!-- Floating 3D Ornaments -->
            <div
                class="pointer-events-none absolute top-12 left-10 size-48 animate-[pulse_6s_infinite] rounded-full bg-gradient-to-tr from-[#c9a84c]/20 to-transparent blur-2xl"
            />
            <div
                class="pointer-events-none absolute right-1/3 bottom-24 size-64 animate-[pulse_8s_infinite] rounded-full bg-gradient-to-bl from-[#c9a84c]/10 to-transparent blur-3xl"
            />

            <div
                aria-hidden="true"
                class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=2000&q=80')] bg-cover bg-center opacity-20"
                style="animation: aurelia-fade-in 1200ms ease-out both"
            />
            <div
                aria-hidden="true"
                class="absolute inset-0 bg-gradient-to-b from-[#1a2744]/40 via-[#1a2744]/75 to-[#1a2744]"
            />
            <div
                aria-hidden="true"
                class="pointer-events-none absolute top-1/4 -right-32 size-96 rounded-full bg-[#c9a84c]/10 blur-3xl"
            />

            <div
                class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-32"
            >
                <div class="grid items-center gap-12 lg:grid-cols-12">
                    <!-- Left Column: Content -->
                    <div class="flex flex-col justify-center lg:col-span-7">
                        <p
                            class="animate-fade-up text-xs font-semibold tracking-[0.4em] text-[#c9a84c] uppercase"
                        >
                            Aurelia Stay
                        </p>
                        <h1
                            class="animate-fade-up-delayed-1 mt-4 font-serif text-5xl leading-tight text-white md:text-6xl"
                        >
                            A retreat where every detail is
                            <span class="font-normal text-[#c9a84c] italic"
                                >curated</span
                            >
                            for you.
                        </h1>
                        <p
                            class="animate-fade-up-delayed-2 mt-6 max-w-xl text-base leading-relaxed text-slate-300"
                        >
                            Discover thoughtfully designed suites, anticipatory
                            service, and the kind of quiet luxury that lingers
                            long after check-out.
                        </p>
                        <div
                            class="animate-fade-up-delayed-3 mt-10 flex flex-wrap items-center gap-6"
                        >
                            <Link
                                :href="roomsIndex().url"
                                class="group inline-flex cursor-pointer items-center gap-2 rounded-md bg-[#c9a84c] px-6 py-3.5 text-sm font-semibold text-[#1a2744] shadow-lg shadow-[#c9a84c]/20 transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#dab867] hover:shadow-xl hover:shadow-[#c9a84c]/30"
                            >
                                Browse rooms
                                <ArrowRight
                                    class="size-4 transition-transform duration-200 group-hover:translate-x-0.5"
                                />
                            </Link>
                            <a
                                href="#featured"
                                class="flex cursor-pointer items-center gap-2 text-sm tracking-widest text-slate-300 uppercase transition-colors duration-200 hover:text-[#c9a84c]"
                            >
                                Discover the experience
                                <span class="animate-bounce">↓</span>
                            </a>
                        </div>
                    </div>

                    <!-- Right Column: Interactive 3D Showcase Card -->
                    <div
                        class="animate-fade-up-delayed-2 flex justify-center lg:col-span-5 lg:justify-end"
                    >
                        <div
                            class="perspective-1000 relative w-full max-w-[380px]"
                        >
                            <div
                                ref="heroCardEl"
                                :style="{
                                    transform: heroCardTransform,
                                    transition: heroCardTransition,
                                }"
                                class="w-full rounded-2xl border border-white/10 bg-white/5 p-4 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.5)] backdrop-blur-md transition-all duration-200 will-change-transform"
                            >
                                <!-- Image Slot -->
                                <div
                                    class="relative aspect-[4/5] overflow-hidden rounded-xl bg-slate-900 shadow-inner"
                                >
                                    <img
                                        src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800&q=80"
                                        alt="Aurelia Suite Showcase"
                                        class="h-full w-full object-cover opacity-90 transition-transform duration-700 hover:scale-105"
                                    />
                                    <!-- Badges -->
                                    <div
                                        class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-5"
                                    >
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <span
                                                class="rounded-full border border-[#c9a84c]/20 bg-[#1a2744]/80 px-2.5 py-1 text-xs font-semibold tracking-widest text-[#c9a84c] uppercase backdrop-blur-sm"
                                            >
                                                Featured Suite
                                            </span>
                                            <div
                                                class="flex items-center gap-1 text-sm text-amber-400"
                                            >
                                                <Star
                                                    class="size-4 fill-amber-400 text-amber-400"
                                                />
                                                <span
                                                    class="font-semibold text-white"
                                                    >4.9</span
                                                >
                                            </div>
                                        </div>
                                        <h3
                                            class="mt-2 font-serif text-xl text-white"
                                        >
                                            The Royal Penthouse
                                        </h3>
                                    </div>
                                </div>
                                <!-- Details info -->
                                <div
                                    class="mt-4 flex items-center justify-between px-1"
                                >
                                    <div>
                                        <p
                                            class="text-xs tracking-wider text-slate-400 uppercase"
                                        >
                                            Starting from
                                        </p>
                                        <p
                                            class="font-serif text-lg text-[#c9a84c]"
                                        >
                                            $380
                                            <span class="text-xs text-slate-400"
                                                >/ night</span
                                            >
                                        </p>
                                    </div>
                                    <Link
                                        :href="roomsIndex().url"
                                        class="border-b border-white/20 pb-0.5 text-xs font-semibold tracking-wider text-white uppercase transition-colors duration-200 hover:border-[#c9a84c] hover:text-[#c9a84c]"
                                    >
                                        View Details
                                    </Link>
                                </div>
                            </div>

                            <!-- Dynamic Shadow beneath the card -->
                            <div
                                class="absolute right-[10%] -bottom-6 left-[10%] -z-10 h-8 rounded-full bg-black/40 blur-xl transition-all duration-300"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3D Floating Quick Search Bar -->
        <div
            class="relative z-20 mx-auto -mt-10 max-w-5xl px-4 pb-12 sm:px-6 lg:px-8"
        >
            <div
                ref="searchBarEl"
                :style="{
                    transform: searchBarTransform,
                    transition: searchBarTransition,
                }"
                class="w-full rounded-2xl border border-white/10 bg-white/5 p-6 shadow-[0_20px_50px_rgba(0,0,0,0.3)] backdrop-blur-md transition-all duration-200"
            >
                <form
                    @submit.prevent="handleSearch"
                    class="grid items-end gap-4 md:grid-cols-4"
                >
                    <div>
                        <label
                            class="mb-2 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                            >Search Keyword</label
                        >
                        <input
                            v-model="searchForm.q"
                            type="text"
                            placeholder="e.g. Penthouse, Suite..."
                            class="w-full rounded-lg border border-white/10 bg-white/10 px-4 py-2.5 text-sm text-white placeholder-slate-400 transition-all duration-200 focus:border-[#c9a84c] focus:bg-white/20 focus:outline-none"
                        />
                    </div>
                    <div>
                        <label
                            class="mb-2 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                            >Room Type</label
                        >
                        <div class="relative">
                            <select
                                v-model="searchForm.type"
                                class="w-full appearance-none rounded-lg border border-white/10 bg-white/10 px-4 py-2.5 text-sm text-white transition-all duration-200 focus:border-[#c9a84c] focus:bg-white/20 focus:outline-none"
                            >
                                <option value="" class="bg-[#1a2744]">
                                    Any Type
                                </option>
                                <option value="single" class="bg-[#1a2744]">
                                    Single Room
                                </option>
                                <option value="double" class="bg-[#1a2744]">
                                    Double Room
                                </option>
                                <option value="suite" class="bg-[#1a2744]">
                                    Luxury Suite
                                </option>
                                <option value="deluxe" class="bg-[#1a2744]">
                                    Deluxe Room
                                </option>
                            </select>
                            <span
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400"
                                >▼</span
                            >
                        </div>
                    </div>
                    <div>
                        <label
                            class="mb-2 block text-xs font-semibold tracking-wider text-slate-300 uppercase"
                            >Check-in Date</label
                        >
                        <input
                            type="date"
                            class="w-full rounded-lg border border-white/10 bg-white/10 px-4 py-2.5 text-sm text-white transition-all duration-200 focus:border-[#c9a84c] focus:bg-white/20 focus:outline-none"
                        />
                    </div>
                    <button
                        type="submit"
                        class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg bg-[#c9a84c] py-3 text-sm font-semibold text-[#1a2744] shadow-md shadow-[#c9a84c]/20 transition-all duration-200 hover:bg-[#dab867] hover:shadow-lg hover:shadow-[#c9a84c]/30"
                    >
                        <span>Check Availability</span>
                        <ArrowRight class="size-4" />
                    </button>
                </form>
            </div>
        </div>

        <!-- Feature Cards -->
        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="grid gap-8 md:grid-cols-3">
                <div
                    class="group cursor-default rounded-2xl border border-slate-200/60 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-[#c9a84c]/50 hover:shadow-[0_15px_30px_rgba(26,39,68,0.05)]"
                >
                    <div
                        class="flex size-14 items-center justify-center rounded-2xl bg-[#1a2744] text-[#c9a84c] shadow-lg shadow-[#1a2744]/10 transition-all duration-300 group-hover:rotate-6 group-hover:bg-[#c9a84c] group-hover:text-[#1a2744]"
                    >
                        <Sparkles class="size-6" />
                    </div>
                    <h3
                        class="mt-6 font-serif text-xl font-semibold text-[#1a2744]"
                    >
                        Curated suites
                    </h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-500">
                        Twenty rooms across four collections, each with its own
                        unique layout, custom furnishings, and artistic
                        character.
                    </p>
                </div>

                <div
                    class="group cursor-default rounded-2xl border border-slate-200/60 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-[#c9a84c]/50 hover:shadow-[0_15px_30px_rgba(26,39,68,0.05)]"
                >
                    <div
                        class="flex size-14 items-center justify-center rounded-2xl bg-[#1a2744] text-[#c9a84c] shadow-lg shadow-[#1a2744]/10 transition-all duration-300 group-hover:rotate-6 group-hover:bg-[#c9a84c] group-hover:text-[#1a2744]"
                    >
                        <ShieldCheck class="size-6" />
                    </div>
                    <h3
                        class="mt-6 font-serif text-xl font-semibold text-[#1a2744]"
                    >
                        Confident booking
                    </h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-500">
                        Free cancellation on pending reservations. Secure
                        payments, direct communication, and no hidden booking
                        charges.
                    </p>
                </div>

                <div
                    class="group cursor-default rounded-2xl border border-slate-200/60 bg-white p-8 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-[#c9a84c]/50 hover:shadow-[0_15px_30px_rgba(26,39,68,0.05)]"
                >
                    <div
                        class="flex size-14 items-center justify-center rounded-2xl bg-[#1a2744] text-[#c9a84c] shadow-lg shadow-[#1a2744]/10 transition-all duration-300 group-hover:rotate-6 group-hover:bg-[#c9a84c] group-hover:text-[#1a2744]"
                    >
                        <Wifi class="size-6" />
                    </div>
                    <h3
                        class="mt-6 font-serif text-xl font-semibold text-[#1a2744]"
                    >
                        All amenities included
                    </h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-500">
                        Premium high-speed Wi-Fi, organic breakfast buffet, and
                        dedicated 24/7 concierge service included with every
                        stay.
                    </p>
                </div>
            </div>
        </section>

        <!-- Featured Rooms Section -->
        <section id="featured" class="bg-slate-50 py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between">
                    <div>
                        <p
                            class="text-xs font-semibold tracking-widest text-[#c9a84c] uppercase"
                        >
                            Featured
                        </p>
                        <h2
                            class="mt-2 font-serif text-3xl text-[#1a2744] md:text-4xl"
                        >
                            Our most cherished rooms
                        </h2>
                    </div>
                    <Link
                        :href="roomsIndex().url"
                        class="hidden text-sm font-medium text-[#1a2744] underline-offset-4 transition hover:text-[#c9a84c] hover:underline md:inline-flex"
                    >
                        View all rooms →
                    </Link>
                </div>

                <div
                    v-if="featuredRooms.data.length"
                    class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <div
                        v-for="(room, idx) in featuredRooms.data"
                        :key="room.id"
                        :style="`animation: aurelia-fade-up 540ms cubic-bezier(0.22, 1, 0.36, 1) ${60 * idx}ms both;`"
                    >
                        <RoomCard :room="room" />
                    </div>
                </div>
                <p v-else class="mt-10 text-sm text-slate-500">
                    No featured rooms yet. Please check back soon.
                </p>
            </div>
        </section>

        <!-- Testimonials/Guest Letters -->
        <section
            id="testimonials"
            class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8"
        >
            <div class="text-center">
                <p
                    class="text-xs font-semibold tracking-widest text-[#c9a84c] uppercase"
                >
                    Guest Letters
                </p>
                <h2 class="mt-2 font-serif text-3xl text-[#1a2744] md:text-4xl">
                    Words from those who have stayed
                </h2>
            </div>

            <div class="mt-12 grid gap-8 md:grid-cols-3">
                <figure
                    v-for="t in testimonials"
                    :key="t.name"
                    class="relative rounded-2xl border border-slate-200/60 bg-white p-8 shadow-sm transition-all duration-300 hover:border-[#c9a84c]/40 hover:shadow-[0_15px_30px_rgba(26,39,68,0.04)]"
                >
                    <!-- Quote Mark overlay -->
                    <div
                        class="pointer-events-none absolute top-6 right-6 font-serif text-7xl text-slate-100 select-none"
                    >
                        “
                    </div>
                    <div class="relative flex gap-1">
                        <Star
                            v-for="i in 5"
                            :key="i"
                            class="size-4"
                            :class="
                                i <= t.rating
                                    ? 'fill-[#c9a84c] text-[#c9a84c]'
                                    : 'text-slate-200'
                            "
                        />
                    </div>
                    <blockquote
                        class="relative mt-5 font-serif text-base leading-relaxed text-slate-700 italic"
                    >
                        "{{ t.text }}"
                    </blockquote>
                    <hr class="my-5 border-slate-100" />
                    <figcaption
                        class="text-sm font-semibold tracking-wider text-[#1a2744] uppercase"
                    >
                        — {{ t.name }}
                    </figcaption>
                </figure>
            </div>
        </section>
    </GuestLayout>
</template>
