<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    BarChart3,
    BedDouble,
    CalendarCheck,
    DollarSign,
    Star,
    Users,
} from 'lucide-vue-next';
import {
    BarElement,
    CategoryScale,
    Chart as ChartJS,
    Filler,
    Legend,
    LinearScale,
    LineElement,
    PointElement,
    Title,
    Tooltip,
} from 'chart.js';
import { Bar, Line } from 'vue-chartjs';
import StatsCard from '@/components/admin/StatsCard.vue';
import DataTable from '@/components/admin/DataTable.vue';
import StatusBadge from '@/components/bookings/StatusBadge.vue';
import type { BookingStatus } from '@/types';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import { show as adminBookingShow } from '@/actions/App/Http/Controllers/Admin/BookingController';

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    LineElement,
    PointElement,
    Filler,
    Title,
    Tooltip,
    Legend,
);

type StatusBreakdown = {
    status: string;
    label: string;
    color: string;
    total: number;
};

type TrendPoint = { date: string; total: number; revenue: number };

type TopRoom = {
    id: number;
    name: string;
    slug: string;
    thumbnail: string | null;
    bookings_count: number;
    revenue: number;
};

type RecentBooking = {
    reference: string;
    guest: string | null;
    room: string | null;
    check_in: string | null;
    check_out: string | null;
    total_price: number;
    status: string;
    status_label: string;
    status_color: string;
    created_at: string | null;
};

const props = defineProps<{
    totals: {
        rooms: number;
        rooms_available: number;
        users: number;
        bookings: number;
        reviews: number;
    };
    revenue: {
        this_month: number;
        last_month: number;
        year_to_date: number;
    };
    bookings_by_status: StatusBreakdown[];
    bookings_trend: TrendPoint[];
    top_rooms: TopRoom[];
    recent_bookings: RecentBooking[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: adminDashboard().url },
            { title: 'Overview', href: adminDashboard().url },
        ],
    },
});

const formatCurrency = (value: number) =>
    new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(value);

const formatDate = (iso: string | null) =>
    iso
        ? new Date(iso).toLocaleDateString('en-US', {
              month: 'short',
              day: 'numeric',
          })
        : '—';

const trendData = computed(() => ({
    labels: props.bookings_trend.map((point) => formatDate(point.date)),
    datasets: [
        {
            label: 'Bookings',
            data: props.bookings_trend.map((p) => p.total),
            borderColor: '#1a2744',
            backgroundColor: 'rgba(26, 39, 68, 0.12)',
            tension: 0.35,
            fill: true,
            pointRadius: 0,
            pointHoverRadius: 5,
        },
    ],
}));

const trendOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: { mode: 'index' as const, intersect: false },
    },
    scales: {
        x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
        y: {
            beginAtZero: true,
            grid: { color: 'rgba(148, 163, 184, 0.15)' },
            ticks: { color: '#94a3b8', precision: 0 },
        },
    },
};

const statusData = computed(() => ({
    labels: props.bookings_by_status.map((s) => s.label),
    datasets: [
        {
            label: 'Bookings',
            data: props.bookings_by_status.map((s) => s.total),
            backgroundColor: ['#facc15', '#10b981', '#f43f5e', '#1a2744'],
            borderRadius: 6,
        },
    ],
}));

const statusOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
        y: {
            beginAtZero: true,
            grid: { color: 'rgba(148, 163, 184, 0.15)' },
            ticks: { color: '#94a3b8', precision: 0 },
        },
    },
};

const revenueDelta = computed(() => {
    const last = props.revenue.last_month;
    if (last <= 0) return null;
    const delta = ((props.revenue.this_month - last) / last) * 100;
    return delta;
});

const recentColumns = [
    { key: 'reference', label: 'Reference' },
    { key: 'guest', label: 'Guest' },
    { key: 'room', label: 'Room' },
    { key: 'stay', label: 'Stay' },
    { key: 'total_price', label: 'Total', align: 'right' as const },
    { key: 'status', label: 'Status' },
];
</script>

<template>
    <Head title="Admin · Overview" />

    <div class="space-y-6 p-4 md:p-6">
        <header>
            <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                Administration
            </p>
            <h1 class="mt-1 font-serif text-3xl text-[#1a2744]">Overview</h1>
        </header>

        <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <StatsCard
                label="Rooms"
                :value="totals.rooms"
                :helper="`${totals.rooms_available} available`"
                :icon="BedDouble"
                accent="navy"
            />
            <StatsCard
                label="Bookings"
                :value="totals.bookings"
                :icon="CalendarCheck"
                accent="gold"
            />
            <StatsCard
                label="Guests"
                :value="totals.users"
                :icon="Users"
                accent="emerald"
            />
            <StatsCard
                label="Reviews"
                :value="totals.reviews"
                :icon="Star"
                accent="rose"
            />
        </section>

        <section class="grid gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                            Bookings · last 30 days
                        </p>
                        <h2 class="mt-1 font-serif text-xl text-[#1a2744]">
                            Reservation activity
                        </h2>
                    </div>
                    <BarChart3 class="size-5 text-slate-300" />
                </div>
                <div class="mt-5 h-64">
                    <Line :data="trendData" :options="trendOptions" />
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                            Revenue
                        </p>
                        <h2 class="mt-1 font-serif text-xl text-[#1a2744]">This month</h2>
                    </div>
                    <DollarSign class="size-5 text-slate-300" />
                </div>
                <p class="mt-3 font-serif text-3xl text-[#1a2744]">
                    {{ formatCurrency(revenue.this_month) }}
                </p>
                <p
                    v-if="revenueDelta !== null"
                    class="mt-1 text-xs"
                    :class="revenueDelta >= 0 ? 'text-emerald-600' : 'text-rose-600'"
                >
                    {{ revenueDelta >= 0 ? '+' : '' }}{{ revenueDelta.toFixed(1) }}% vs last month
                </p>
                <p v-else class="mt-1 text-xs text-slate-400">No prior month data</p>

                <dl class="mt-5 space-y-2 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <dt>Last month</dt>
                        <dd>{{ formatCurrency(revenue.last_month) }}</dd>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <dt>Year to date</dt>
                        <dd>{{ formatCurrency(revenue.year_to_date) }}</dd>
                    </div>
                </dl>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                    Bookings by status
                </p>
                <h2 class="mt-1 font-serif text-xl text-[#1a2744]">Distribution</h2>
                <div class="mt-5 h-56">
                    <Bar :data="statusData" :options="statusOptions" />
                </div>
            </div>

            <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white p-5">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                    Top rooms
                </p>
                <h2 class="mt-1 font-serif text-xl text-[#1a2744]">By revenue</h2>
                <ul class="mt-4 divide-y divide-slate-100">
                    <li
                        v-for="room in top_rooms"
                        :key="room.id"
                        class="flex items-center justify-between gap-4 py-3"
                    >
                        <div class="flex items-center gap-3">
                            <img
                                v-if="room.thumbnail"
                                :src="room.thumbnail"
                                :alt="room.name"
                                class="size-12 rounded-lg object-cover"
                            />
                            <div>
                                <p class="font-medium text-slate-800">{{ room.name }}</p>
                                <p class="text-xs text-slate-400">
                                    {{ room.bookings_count }} bookings
                                </p>
                            </div>
                        </div>
                        <p class="font-serif text-sm text-[#1a2744]">
                            {{ formatCurrency(room.revenue) }}
                        </p>
                    </li>
                    <li v-if="!top_rooms.length" class="py-6 text-center text-sm text-slate-400">
                        No room data yet.
                    </li>
                </ul>
            </div>
        </section>

        <section>
            <div class="mb-3 flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                        Activity
                    </p>
                    <h2 class="mt-1 font-serif text-xl text-[#1a2744]">Recent bookings</h2>
                </div>
            </div>

            <DataTable
                :columns="recentColumns"
                :rows="recent_bookings"
                empty-state="No bookings to show."
                :row-key="(row) => row.reference"
            >
                <template #cell-reference="{ row }">
                    <Link
                        :href="adminBookingShow({ booking: row.reference }).url"
                        class="font-mono text-xs text-[#1a2744] hover:text-[#c9a84c]"
                    >
                        {{ row.reference }}
                    </Link>
                </template>
                <template #cell-stay="{ row }">
                    <span class="text-xs text-slate-500">
                        {{ formatDate(row.check_in) }} → {{ formatDate(row.check_out) }}
                    </span>
                </template>
                <template #cell-total_price="{ row }">
                    <span class="font-medium text-slate-800">
                        {{ formatCurrency(row.total_price) }}
                    </span>
                </template>
                <template #cell-status="{ row }">
                    <StatusBadge
                        :status="(row.status as BookingStatus)"
                        :label="row.status_label"
                    />
                </template>
            </DataTable>
        </section>
    </div>
</template>
