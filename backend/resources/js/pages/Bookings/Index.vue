<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CalendarDays, ChevronRight, Hotel, MapPin } from 'lucide-vue-next';
import StatusBadge from '@/components/bookings/StatusBadge.vue';
import { dashboard } from '@/routes';
import { index as roomsIndex } from '@/routes/rooms';
import { show as bookingShow } from '@/actions/App/Http/Controllers/BookingController';
import type { Booking, Paginated } from '@/types';

defineProps<{
    bookings: Paginated<Booking>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard().url },
            { title: 'My bookings', href: '#' },
        ],
    },
});

const formatPrice = (price: number) =>
    new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(price);

const formatDate = (iso: string) =>
    new Date(iso).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
</script>

<template>
    <Head title="My reservations" />

    <div class="mx-auto w-full max-w-5xl p-6">
        <header class="animate-fade-up flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#c9a84c]">
                    Your stay
                </p>
                <h1 class="mt-1 font-serif text-3xl text-[#1a2744] md:text-4xl">
                    My reservations
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Past stays, upcoming check-ins and everything in between.
                </p>
            </div>
            <Link
                :href="roomsIndex().url"
                class="group inline-flex cursor-pointer items-center gap-2 rounded-md bg-[#1a2744] px-4 py-2 text-sm font-medium text-white shadow-md shadow-[#1a2744]/10 transition-colors duration-200 hover:bg-[#243558]"
            >
                <Hotel class="size-4 transition-transform duration-200 group-hover:scale-110" />
                Book another room
            </Link>
        </header>

        <div v-if="bookings.data.length" class="mt-8 space-y-3">
            <Link
                v-for="(booking, idx) in bookings.data"
                :key="booking.id"
                :href="bookingShow({ booking: booking.reference }).url"
                class="group flex cursor-pointer flex-wrap items-center gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-[#c9a84c]/40 hover:shadow-md"
                :style="`animation: aurelia-fade-up 480ms cubic-bezier(0.22, 1, 0.36, 1) ${40 * idx}ms both;`"
            >
                <div class="relative size-20 shrink-0 overflow-hidden rounded-lg bg-slate-100">
                    <img
                        v-if="booking.room?.thumbnail"
                        :src="booking.room.thumbnail"
                        :alt="booking.room?.name ?? ''"
                        class="size-full object-cover transition-transform duration-500 group-hover:scale-110"
                    />
                    <div
                        v-else
                        class="flex size-full items-center justify-center text-slate-300"
                    >
                        <Hotel class="size-6" />
                    </div>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="font-serif text-base text-[#1a2744] transition-colors duration-200 group-hover:text-[#c9a84c]">
                            {{ booking.room?.name ?? 'Reservation' }}
                        </h2>
                        <StatusBadge :status="booking.status" :label="booking.status_label" />
                    </div>
                    <p class="mt-1 flex items-center gap-1.5 text-sm text-slate-500">
                        <CalendarDays class="size-4 text-slate-400" />
                        {{ formatDate(booking.check_in) }} → {{ formatDate(booking.check_out) }}
                        <span class="text-slate-300">·</span>
                        {{ booking.nights }}
                        {{ booking.nights === 1 ? 'night' : 'nights' }}
                    </p>
                    <p class="mt-1 font-mono text-[11px] tracking-wider text-slate-400">
                        Ref · {{ booking.reference }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="font-serif text-lg text-[#1a2744]">
                        {{ formatPrice(booking.total_price) }}
                    </p>
                    <ChevronRight
                        class="ml-auto mt-1 size-4 text-slate-300 transition-all duration-200 group-hover:translate-x-0.5 group-hover:text-[#c9a84c]"
                    />
                </div>
            </Link>
        </div>

        <div
            v-else
            class="animate-fade-up mt-10 rounded-2xl border border-dashed border-[#c9a84c]/40 bg-gradient-to-br from-[#fdf8ec] to-white p-12 text-center"
        >
            <span
                class="mx-auto flex size-14 items-center justify-center rounded-full bg-[#1a2744] text-[#c9a84c] shadow-lg shadow-[#1a2744]/15"
            >
                <CalendarDays class="size-6" />
            </span>
            <h2 class="mt-5 font-serif text-2xl text-[#1a2744]">
                No reservations yet
            </h2>
            <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500">
                Discover our suites and book your first escape — every stay begins
                with one click.
            </p>
            <Link
                :href="roomsIndex().url"
                class="group mt-6 inline-flex cursor-pointer items-center gap-2 rounded-md bg-[#1a2744] px-5 py-2.5 text-sm font-medium text-white shadow-md shadow-[#1a2744]/15 transition-colors duration-200 hover:bg-[#243558]"
            >
                <MapPin class="size-4" />
                Browse rooms
            </Link>
        </div>

        <nav
            v-if="bookings.meta.last_page > 1"
            class="mt-8 flex flex-wrap items-center justify-center gap-1"
        >
            <template v-for="link in bookings.meta.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    v-html="link.label"
                    preserve-scroll
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
</template>
