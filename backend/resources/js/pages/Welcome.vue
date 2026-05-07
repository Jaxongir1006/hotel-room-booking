<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, ShieldCheck, Sparkles, Star, Wifi } from 'lucide-vue-next';
import GuestLayout from '@/layouts/GuestLayout.vue';
import RoomCard from '@/components/rooms/RoomCard.vue';
import { index as roomsIndex } from '@/routes/rooms';
import type { RoomSummary } from '@/types';

defineProps<{
    canRegister: boolean;
    featuredRooms: { data: RoomSummary[] };
}>();

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
        <section class="relative overflow-hidden bg-[#1a2744] text-white">
            <div
                class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=2000&q=80')] bg-cover bg-center opacity-30"
            />
            <div
                class="absolute inset-0 bg-gradient-to-b from-[#1a2744]/60 via-[#1a2744]/70 to-[#1a2744]"
            />
            <div class="relative mx-auto max-w-7xl px-4 py-28 sm:px-6 lg:px-8 lg:py-40">
                <p class="text-xs font-semibold uppercase tracking-[0.4em] text-[#c9a84c]">
                    Aurelia Stay
                </p>
                <h1 class="mt-4 max-w-3xl font-serif text-5xl leading-tight md:text-6xl">
                    A retreat where every detail is curated for you.
                </h1>
                <p class="mt-6 max-w-xl text-base leading-relaxed text-white/80">
                    Discover thoughtfully designed suites, anticipatory service, and
                    the kind of quiet luxury that lingers long after check-out.
                </p>
                <div class="mt-10 flex flex-wrap items-center gap-4">
                    <Link
                        :href="roomsIndex().url"
                        class="inline-flex items-center gap-2 rounded-md bg-[#c9a84c] px-6 py-3 text-sm font-medium text-[#1a2744] transition hover:bg-[#dab867]"
                    >
                        Browse rooms
                        <ArrowRight class="size-4" />
                    </Link>
                    <a
                        href="#featured"
                        class="text-sm uppercase tracking-widest text-white/70 transition hover:text-[#c9a84c]"
                    >
                        Discover the experience ↓
                    </a>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="grid gap-8 md:grid-cols-3">
                <div class="text-center">
                    <div
                        class="mx-auto flex size-12 items-center justify-center rounded-full bg-[#1a2744] text-[#c9a84c]"
                    >
                        <Sparkles class="size-5" />
                    </div>
                    <h3 class="mt-4 font-serif text-lg text-[#1a2744]">Curated suites</h3>
                    <p class="mt-2 text-sm text-slate-500">
                        Twenty rooms across four collections, each with its own character.
                    </p>
                </div>
                <div class="text-center">
                    <div
                        class="mx-auto flex size-12 items-center justify-center rounded-full bg-[#1a2744] text-[#c9a84c]"
                    >
                        <ShieldCheck class="size-5" />
                    </div>
                    <h3 class="mt-4 font-serif text-lg text-[#1a2744]">Confident booking</h3>
                    <p class="mt-2 text-sm text-slate-500">
                        Free cancellation on pending reservations. No hidden charges.
                    </p>
                </div>
                <div class="text-center">
                    <div
                        class="mx-auto flex size-12 items-center justify-center rounded-full bg-[#1a2744] text-[#c9a84c]"
                    >
                        <Wifi class="size-5" />
                    </div>
                    <h3 class="mt-4 font-serif text-lg text-[#1a2744]">All amenities included</h3>
                    <p class="mt-2 text-sm text-slate-500">
                        Premium Wi-Fi, breakfast, concierge — included in every stay.
                    </p>
                </div>
            </div>
        </section>

        <section id="featured" class="bg-slate-50 py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                            Featured
                        </p>
                        <h2 class="mt-2 font-serif text-3xl text-[#1a2744] md:text-4xl">
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
                    <RoomCard
                        v-for="room in featuredRooms.data"
                        :key="room.id"
                        :room="room"
                    />
                </div>
                <p v-else class="mt-10 text-sm text-slate-500">
                    No featured rooms yet. Please check back soon.
                </p>
            </div>
        </section>

        <section id="testimonials" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                    Guest Letters
                </p>
                <h2 class="mt-2 font-serif text-3xl text-[#1a2744] md:text-4xl">
                    Words from those who have stayed
                </h2>
            </div>
            <div class="mt-12 grid gap-6 md:grid-cols-3">
                <figure
                    v-for="t in testimonials"
                    :key="t.name"
                    class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"
                >
                    <div class="flex gap-0.5">
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
                    <blockquote class="mt-4 font-serif text-base leading-relaxed text-slate-700">
                        "{{ t.text }}"
                    </blockquote>
                    <figcaption class="mt-4 text-sm font-medium text-[#1a2744]">
                        — {{ t.name }}
                    </figcaption>
                </figure>
            </div>
        </section>
    </GuestLayout>
</template>
