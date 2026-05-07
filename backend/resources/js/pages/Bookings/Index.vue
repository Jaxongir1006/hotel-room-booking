<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CalendarDays, ChevronRight } from 'lucide-vue-next';
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
        <header class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                    Your stay
                </p>
                <h1 class="mt-1 font-serif text-2xl text-[#1a2744]">My reservations</h1>
            </div>
            <Link
                :href="roomsIndex().url"
                class="rounded-md bg-[#1a2744] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#243558]"
            >
                Book another room
            </Link>
        </header>

        <div v-if="bookings.data.length" class="mt-6 space-y-3">
            <Link
                v-for="booking in bookings.data"
                :key="booking.id"
                :href="bookingShow({ booking: booking.reference }).url"
                class="group flex flex-wrap items-center gap-4 rounded-xl border border-slate-200 bg-white p-4 transition hover:border-[#c9a84c]/40 hover:shadow-md"
            >
                <img
                    v-if="booking.room?.thumbnail"
                    :src="booking.room.thumbnail"
                    :alt="booking.room?.name ?? ''"
                    class="size-20 rounded-lg object-cover"
                />
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="font-serif text-base text-[#1a2744]">
                            {{ booking.room?.name ?? 'Reservation' }}
                        </h2>
                        <StatusBadge :status="booking.status" :label="booking.status_label" />
                    </div>
                    <p class="mt-1 flex items-center gap-1.5 text-sm text-slate-500">
                        <CalendarDays class="size-4 text-slate-400" />
                        {{ formatDate(booking.check_in) }} → {{ formatDate(booking.check_out) }}
                        ·
                        {{ booking.nights }} {{ booking.nights === 1 ? 'night' : 'nights' }}
                    </p>
                    <p class="mt-1 text-xs text-slate-400">Ref · {{ booking.reference }}</p>
                </div>
                <div class="text-right">
                    <p class="font-serif text-lg text-[#1a2744]">{{ formatPrice(booking.total_price) }}</p>
                    <ChevronRight class="ml-auto mt-1 size-4 text-slate-300 transition group-hover:text-[#c9a84c]" />
                </div>
            </Link>
        </div>

        <div
            v-else
            class="mt-10 rounded-xl border border-dashed border-slate-200 bg-white p-12 text-center"
        >
            <p class="font-serif text-lg text-[#1a2744]">No reservations yet</p>
            <p class="mt-2 text-sm text-slate-500">
                Once you book, your stays will appear here.
            </p>
            <Link
                :href="roomsIndex().url"
                class="mt-6 inline-block rounded-md bg-[#1a2744] px-5 py-2 text-sm font-medium text-white transition hover:bg-[#243558]"
            >
                Browse rooms
            </Link>
        </div>

        <nav
            v-if="bookings.meta.last_page > 1"
            class="mt-8 flex flex-wrap items-center justify-center gap-1"
        >
            <template v-for="link in bookings.links" :key="link.label">
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
