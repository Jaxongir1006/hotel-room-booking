<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, Search } from 'lucide-vue-next';
import { debounce } from '@/lib/debounce';
import DataTable from '@/components/admin/DataTable.vue';
import StatusBadge from '@/components/bookings/StatusBadge.vue';
import {
    index as adminBookingsIndex,
    show as adminBookingsShow,
} from '@/actions/App/Http/Controllers/Admin/BookingController';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import type { Booking, BookingStatus, Paginated } from '@/types';

type Option = { value: string; label: string };

const props = defineProps<{
    bookings: Paginated<Booking>;
    filters: { q: string | null; status: string | null };
    statuses: Option[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: adminDashboard().url },
            { title: 'Bookings', href: adminBookingsIndex().url },
        ],
    },
});

const search = ref(props.filters.q ?? '');
const status = ref(props.filters.status ?? '');

const apply = debounce(() => {
    router.get(
        adminBookingsIndex().url,
        {
            q: search.value || undefined,
            status: status.value || undefined,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}, 250);

watch([search, status], apply);

const formatPrice = (value: number) =>
    new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(value);

const formatDate = (iso: string | null) =>
    iso
        ? new Date(iso).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
        : '—';

const columns = [
    { key: 'reference', label: 'Reference' },
    { key: 'guest', label: 'Guest' },
    { key: 'room', label: 'Room' },
    { key: 'stay', label: 'Stay' },
    { key: 'total_price', label: 'Total', align: 'right' as const },
    { key: 'status', label: 'Status' },
    { key: 'actions', label: '', align: 'right' as const, width: '60px' },
];
</script>

<template>
    <Head title="Admin · Bookings" />

    <div class="space-y-5 p-4 md:p-6">
        <header>
            <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                Reservations
            </p>
            <h1 class="mt-1 font-serif text-3xl text-[#1a2744]">Bookings</h1>
        </header>

        <div class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:grid-cols-[2fr_1fr]">
            <label class="relative">
                <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                <input
                    v-model="search"
                    type="search"
                    placeholder="Search by reference, guest, room…"
                    class="w-full rounded-md border border-slate-200 bg-white py-2 pl-9 pr-3 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                />
            </label>
            <select
                v-model="status"
                class="rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
            >
                <option value="">All statuses</option>
                <option v-for="s in statuses" :key="s.value" :value="s.value">
                    {{ s.label }}
                </option>
            </select>
        </div>

        <DataTable
            :columns="columns"
            :rows="bookings.data"
            :row-key="(row) => row.id"
            empty-state="No bookings yet."
        >
            <template #cell-reference="{ row }">
                <Link
                    :href="adminBookingsShow({ booking: row.reference }).url"
                    class="font-mono text-xs text-[#1a2744] hover:text-[#c9a84c]"
                >
                    {{ row.reference }}
                </Link>
            </template>
            <template #cell-guest="{ row }">
                <div>
                    <p class="font-medium text-slate-800">{{ row.user?.name ?? '—' }}</p>
                    <p class="text-xs text-slate-400">{{ row.user?.email }}</p>
                </div>
            </template>
            <template #cell-room="{ row }">
                <span class="text-slate-700">{{ row.room?.name ?? '—' }}</span>
            </template>
            <template #cell-stay="{ row }">
                <span class="text-xs text-slate-500">
                    {{ formatDate(row.check_in) }} → {{ formatDate(row.check_out) }}
                </span>
            </template>
            <template #cell-total_price="{ row }">
                <span class="font-medium text-slate-800">
                    {{ formatPrice(row.total_price) }}
                </span>
            </template>
            <template #cell-status="{ row }">
                <StatusBadge
                    :status="row.status as BookingStatus"
                    :label="row.status_label"
                />
            </template>
            <template #cell-actions="{ row }">
                <Link
                    :href="adminBookingsShow({ booking: row.reference }).url"
                    class="inline-flex size-8 items-center justify-center rounded-md text-slate-500 transition hover:bg-slate-100 hover:text-[#1a2744]"
                    aria-label="View booking"
                >
                    <Eye class="size-4" />
                </Link>
            </template>
        </DataTable>

        <p class="text-xs text-slate-400">
            Showing {{ bookings.meta.from ?? 0 }}–{{ bookings.meta.to ?? 0 }} of
            {{ bookings.meta.total }} bookings.
        </p>
    </div>
</template>
