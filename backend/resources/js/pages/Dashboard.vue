<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    ArrowRight,
    BedDouble,
    CalendarCheck,
    CalendarDays,
    Hotel,
    MapPin,
    MessageSquare,
    ShieldCheck,
    Sparkles,
    Star,
    XCircle,
} from 'lucide-vue-next';
import StatusBadge from '@/components/bookings/StatusBadge.vue';
import { dashboard } from '@/routes';
import { index as roomsIndex, show as roomShow } from '@/routes/rooms';
import {
    index as bookingsIndex,
    show as bookingShow,
} from '@/actions/App/Http/Controllers/BookingController';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import type { Booking, BookingStatus, RoomSummary } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: dashboard().url }],
    },
});

defineProps<{
    stats: {
        upcoming: number;
        completed: number;
        cancelled: number;
        reviews: number;
    };
    nextStay: { data: Booking } | null;
    recent: { data: Booking[] };
    featured: { data: RoomSummary[] };
    isAdmin: boolean;
}>();

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'Guest');
const firstName = computed(() => userName.value.split(' ')[0]);

const formatPrice = (value: number) =>
    new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(value);

const formatDate = (iso: string | null | undefined) =>
    iso
        ? new Date(iso).toLocaleDateString('en-US', {
              weekday: 'short',
              month: 'short',
              day: 'numeric',
              year: 'numeric',
          })
        : '—';

const formatShortDate = (iso: string | null | undefined) =>
    iso
        ? new Date(iso).toLocaleDateString('en-US', {
              month: 'short',
              day: 'numeric',
          })
        : '—';

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Good morning';
    if (hour < 18) return 'Good afternoon';
    return 'Good evening';
});

const daysUntil = (iso: string) => {
    const target = new Date(iso);
    target.setHours(0, 0, 0, 0);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const diff = Math.round(
        (target.getTime() - today.getTime()) / (1000 * 60 * 60 * 24),
    );
    if (diff <= 0) return 'Starting today';
    if (diff === 1) return 'Tomorrow';
    return `In ${diff} days`;
};
</script>

<template>
    <Head title="Dashboard" />

    <div class="space-y-8 p-4 md:p-8">
        <!-- Hero greeting -->
        <header
            class="animate-fade-up relative overflow-hidden rounded-2xl border border-[#1a2744]/10 bg-gradient-to-br from-[#1a2744] via-[#22314f] to-[#0e1830] p-8 text-white shadow-sm"
        >
            <div
                aria-hidden="true"
                class="pointer-events-none absolute -right-20 -top-24 size-72 rounded-full bg-[#c9a84c]/20 blur-3xl"
            />
            <div
                aria-hidden="true"
                class="pointer-events-none absolute -bottom-16 right-32 size-48 rounded-full bg-[#c9a84c]/10 blur-3xl"
            />

            <div class="relative flex flex-wrap items-center justify-between gap-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#c9a84c]">
                        {{ greeting }}
                    </p>
                    <h1 class="mt-2 font-serif text-3xl md:text-4xl">
                        Welcome back, {{ firstName }}.
                    </h1>
                    <p class="mt-2 max-w-md text-sm text-white/70">
                        Here's a glance at your stays, recent reservations and rooms
                        worth exploring.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <Link
                        v-if="isAdmin"
                        :href="adminDashboard().url"
                        class="group inline-flex cursor-pointer items-center gap-2 rounded-md border border-[#c9a84c]/40 px-4 py-2 text-sm font-medium text-[#c9a84c] transition-colors duration-200 hover:bg-[#c9a84c] hover:text-[#1a2744]"
                    >
                        <ShieldCheck class="size-4" />
                        Admin panel
                        <ArrowRight class="size-4 transition-transform duration-200 group-hover:translate-x-0.5" />
                    </Link>
                    <Link
                        :href="roomsIndex().url"
                        class="group inline-flex cursor-pointer items-center gap-2 rounded-md bg-[#c9a84c] px-4 py-2 text-sm font-medium text-[#1a2744] transition-colors duration-200 hover:bg-[#dab867]"
                    >
                        <Sparkles class="size-4" />
                        Plan a new stay
                    </Link>
                </div>
            </div>
        </header>

        <!-- Stats -->
        <section
            class="animate-fade-up-delayed-1 grid gap-4 sm:grid-cols-2 lg:grid-cols-4"
        >
            <article
                class="group cursor-default rounded-xl border border-slate-200 bg-white p-5 transition-all duration-200 hover:-translate-y-0.5 hover:border-[#c9a84c]/40 hover:shadow-md"
            >
                <div class="flex items-start justify-between">
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Upcoming
                    </p>
                    <span
                        class="flex size-9 items-center justify-center rounded-lg bg-[#1a2744]/5 text-[#1a2744] transition-colors duration-200 group-hover:bg-[#c9a84c]/15 group-hover:text-[#8a6d20]"
                    >
                        <CalendarCheck class="size-4" />
                    </span>
                </div>
                <p class="mt-3 font-serif text-3xl text-[#1a2744]">{{ stats.upcoming }}</p>
                <p class="mt-1 text-xs text-slate-400">Stays on the way</p>
            </article>

            <article
                class="group cursor-default rounded-xl border border-slate-200 bg-white p-5 transition-all duration-200 hover:-translate-y-0.5 hover:border-[#c9a84c]/40 hover:shadow-md"
            >
                <div class="flex items-start justify-between">
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Completed
                    </p>
                    <span
                        class="flex size-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700"
                    >
                        <BedDouble class="size-4" />
                    </span>
                </div>
                <p class="mt-3 font-serif text-3xl text-[#1a2744]">{{ stats.completed }}</p>
                <p class="mt-1 text-xs text-slate-400">Stays enjoyed</p>
            </article>

            <article
                class="group cursor-default rounded-xl border border-slate-200 bg-white p-5 transition-all duration-200 hover:-translate-y-0.5 hover:border-[#c9a84c]/40 hover:shadow-md"
            >
                <div class="flex items-start justify-between">
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Reviews
                    </p>
                    <span
                        class="flex size-9 items-center justify-center rounded-lg bg-[#c9a84c]/15 text-[#8a6d20]"
                    >
                        <Star class="size-4" />
                    </span>
                </div>
                <p class="mt-3 font-serif text-3xl text-[#1a2744]">{{ stats.reviews }}</p>
                <p class="mt-1 text-xs text-slate-400">Stories shared</p>
            </article>

            <article
                class="group cursor-default rounded-xl border border-slate-200 bg-white p-5 transition-all duration-200 hover:-translate-y-0.5 hover:border-[#c9a84c]/40 hover:shadow-md"
            >
                <div class="flex items-start justify-between">
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Cancelled
                    </p>
                    <span
                        class="flex size-9 items-center justify-center rounded-lg bg-rose-50 text-rose-700"
                    >
                        <XCircle class="size-4" />
                    </span>
                </div>
                <p class="mt-3 font-serif text-3xl text-[#1a2744]">{{ stats.cancelled }}</p>
                <p class="mt-1 text-xs text-slate-400">Reservations released</p>
            </article>
        </section>

        <!-- Next stay + Recent bookings -->
        <section
            class="animate-fade-up-delayed-2 grid gap-6 lg:grid-cols-3"
        >
            <!-- Next stay -->
            <div class="lg:col-span-2">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                            Next stay
                        </p>
                        <h2 class="mt-1 font-serif text-2xl text-[#1a2744]">
                            Your upcoming reservation
                        </h2>
                    </div>
                </div>

                <article
                    v-if="nextStay"
                    class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-shadow duration-300 hover:shadow-lg"
                >
                    <div class="grid gap-0 md:grid-cols-[2fr_3fr]">
                        <div class="relative h-56 md:h-auto">
                            <img
                                v-if="nextStay.data.room?.thumbnail"
                                :src="nextStay.data.room.thumbnail"
                                :alt="nextStay.data.room?.name ?? ''"
                                class="size-full object-cover"
                            />
                            <div
                                v-else
                                class="flex size-full items-center justify-center bg-slate-100 text-slate-300"
                            >
                                <Hotel class="size-12" />
                            </div>
                            <span
                                class="absolute left-4 top-4 inline-flex items-center gap-1 rounded-full bg-white/95 px-3 py-1 text-xs font-medium text-[#1a2744] shadow"
                            >
                                <CalendarDays class="size-3" />
                                {{ daysUntil(nextStay.data.check_in) }}
                            </span>
                        </div>
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs uppercase tracking-wider text-slate-400">
                                        {{ nextStay.data.room?.type_label }}
                                    </p>
                                    <h3 class="mt-1 font-serif text-2xl text-[#1a2744]">
                                        {{ nextStay.data.room?.name }}
                                    </h3>
                                </div>
                                <StatusBadge
                                    :status="nextStay.data.status"
                                    :label="nextStay.data.status_label"
                                />
                            </div>

                            <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                                <div>
                                    <dt class="text-xs uppercase tracking-wider text-slate-400">
                                        Check-in
                                    </dt>
                                    <dd class="mt-1 text-sm text-slate-700">
                                        {{ formatDate(nextStay.data.check_in) }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase tracking-wider text-slate-400">
                                        Check-out
                                    </dt>
                                    <dd class="mt-1 text-sm text-slate-700">
                                        {{ formatDate(nextStay.data.check_out) }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase tracking-wider text-slate-400">
                                        Nights
                                    </dt>
                                    <dd class="mt-1 text-sm text-slate-700">
                                        {{ nextStay.data.nights }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase tracking-wider text-slate-400">
                                        Total
                                    </dt>
                                    <dd class="mt-1 font-serif text-base text-[#1a2744]">
                                        {{ formatPrice(nextStay.data.total_price) }}
                                    </dd>
                                </div>
                            </dl>

                            <div class="mt-6 flex flex-wrap items-center gap-3">
                                <Link
                                    :href="bookingShow({ booking: nextStay.data.reference }).url"
                                    class="group inline-flex cursor-pointer items-center gap-2 rounded-md bg-[#1a2744] px-4 py-2 text-sm font-medium text-white transition-colors duration-200 hover:bg-[#243558]"
                                >
                                    View reservation
                                    <ArrowRight class="size-4 transition-transform duration-200 group-hover:translate-x-0.5" />
                                </Link>
                                <Link
                                    v-if="nextStay.data.room"
                                    :href="roomShow({ slug: nextStay.data.room.slug }).url"
                                    class="text-sm text-[#1a2744] underline-offset-4 hover:underline"
                                >
                                    Room details
                                </Link>
                            </div>
                        </div>
                    </div>
                </article>

                <article
                    v-else
                    class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-white/60 p-10 text-center"
                >
                    <span
                        class="mx-auto flex size-12 items-center justify-center rounded-full bg-[#c9a84c]/15 text-[#8a6d20]"
                    >
                        <CalendarDays class="size-5" />
                    </span>
                    <h3 class="mt-4 font-serif text-xl text-[#1a2744]">
                        No upcoming stays
                    </h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Browse our suites and book your next escape.
                    </p>
                    <Link
                        :href="roomsIndex().url"
                        class="mt-5 inline-flex cursor-pointer items-center gap-2 rounded-md bg-[#1a2744] px-4 py-2 text-sm font-medium text-white transition-colors duration-200 hover:bg-[#243558]"
                    >
                        <MapPin class="size-4" />
                        Explore rooms
                    </Link>
                </article>
            </div>

            <!-- Recent bookings -->
            <aside>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                            Activity
                        </p>
                        <h2 class="mt-1 font-serif text-2xl text-[#1a2744]">Recent</h2>
                    </div>
                    <Link
                        :href="bookingsIndex().url"
                        class="text-xs font-medium text-[#1a2744] underline-offset-4 hover:underline"
                    >
                        View all
                    </Link>
                </div>

                <div
                    class="mt-4 divide-y divide-slate-100 overflow-hidden rounded-xl border border-slate-200 bg-white"
                >
                    <p
                        v-if="!recent.data.length"
                        class="px-4 py-8 text-center text-sm text-slate-400"
                    >
                        Your reservations will appear here.
                    </p>
                    <Link
                        v-for="booking in recent.data"
                        :key="booking.id"
                        :href="bookingShow({ booking: booking.reference }).url"
                        class="group flex cursor-pointer items-center gap-3 px-4 py-3 transition-colors duration-200 hover:bg-slate-50"
                    >
                        <span
                            class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-[#1a2744]/5 text-[#1a2744] transition-colors duration-200 group-hover:bg-[#c9a84c]/15 group-hover:text-[#8a6d20]"
                        >
                            <Hotel class="size-4" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-800">
                                {{ booking.room?.name ?? 'Room removed' }}
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ formatShortDate(booking.check_in) }} →
                                {{ formatShortDate(booking.check_out) }}
                            </p>
                        </div>
                        <StatusBadge
                            :status="(booking.status as BookingStatus)"
                            :label="booking.status_label"
                        />
                    </Link>
                </div>
            </aside>
        </section>

        <!-- Featured rooms + tips -->
        <section
            class="animate-fade-up-delayed-3 grid gap-6 lg:grid-cols-3"
        >
            <div class="lg:col-span-2">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                            Inspiration
                        </p>
                        <h2 class="mt-1 font-serif text-2xl text-[#1a2744]">
                            Suites you might love
                        </h2>
                    </div>
                    <Link
                        :href="roomsIndex().url"
                        class="text-xs font-medium text-[#1a2744] underline-offset-4 hover:underline"
                    >
                        Browse all
                    </Link>
                </div>

                <div class="mt-4 grid gap-4 md:grid-cols-3">
                    <Link
                        v-for="room in featured.data"
                        :key="room.id"
                        :href="roomShow({ slug: room.slug }).url"
                        class="group cursor-pointer overflow-hidden rounded-xl border border-slate-200 bg-white transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                    >
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <img
                                v-if="room.thumbnail"
                                :src="room.thumbnail"
                                :alt="room.name"
                                class="size-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                            <div
                                v-else
                                class="flex size-full items-center justify-center bg-slate-100 text-slate-300"
                            >
                                <Hotel class="size-8" />
                            </div>
                        </div>
                        <div class="p-4">
                            <p class="text-xs uppercase tracking-wider text-slate-400">
                                {{ room.type_label }}
                            </p>
                            <h3 class="mt-1 font-serif text-base text-[#1a2744]">
                                {{ room.name }}
                            </h3>
                            <p class="mt-2 text-sm text-slate-600">
                                {{ formatPrice(room.price_per_night) }}
                                <span class="text-xs text-slate-400">/ night</span>
                            </p>
                        </div>
                    </Link>
                </div>
            </div>

            <aside class="rounded-2xl border border-[#c9a84c]/30 bg-gradient-to-br from-[#fdf8ec] to-[#f8f1dc] p-6">
                <span
                    class="inline-flex size-9 items-center justify-center rounded-lg bg-[#1a2744] text-[#c9a84c]"
                >
                    <MessageSquare class="size-4" />
                </span>
                <h3 class="mt-4 font-serif text-xl text-[#1a2744]">Concierge tip</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-700">
                    Reservations confirmed at least seven days in advance unlock our
                    welcome amenity — fresh flowers in your suite on arrival.
                </p>
                <p class="mt-4 text-xs text-slate-500">
                    Need help with anything? Reach the concierge any time at
                    <a
                        href="mailto:concierge@aurelia.example"
                        class="font-medium text-[#1a2744] underline underline-offset-4 hover:text-[#c9a84c]"
                    >
                        concierge@aurelia.example
                    </a>.
                </p>
            </aside>
        </section>
    </div>
</template>
