<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Search, Trash2 } from 'lucide-vue-next';
import { debounce } from '@/lib/debounce';
import DataTable from '@/components/admin/DataTable.vue';
import ConfirmModal from '@/components/admin/ConfirmModal.vue';
import {
    create as adminRoomsCreate,
    destroy as adminRoomsDestroy,
    edit as adminRoomsEdit,
    index as adminRoomsIndex,
} from '@/actions/App/Http/Controllers/Admin/RoomController';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import type { Paginated } from '@/types';

type AdminRoomRow = {
    id: number;
    name: string;
    slug: string;
    type: string;
    type_label: string;
    price_per_night: number;
    capacity: number;
    floor: number;
    status: string;
    status_label: string;
    thumbnail: string | null;
    bookings_count?: number;
    reviews_count?: number;
};

type Option = { value: string; label: string };

const props = defineProps<{
    rooms: Paginated<AdminRoomRow>;
    filters: { q: string | null; status: string | null; type: string | null };
    options: { types: Option[]; statuses: Option[] };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: adminDashboard().url },
            { title: 'Rooms', href: adminRoomsIndex().url },
        ],
    },
});

const search = ref(props.filters.q ?? '');
const status = ref(props.filters.status ?? '');
const type = ref(props.filters.type ?? '');

const apply = debounce(() => {
    router.get(
        adminRoomsIndex().url,
        {
            q: search.value || undefined,
            status: status.value || undefined,
            type: type.value || undefined,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}, 250);

watch([search, status, type], apply);

const formatPrice = (value: number) =>
    new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(value);

const columns = [
    { key: 'room', label: 'Room' },
    { key: 'type_label', label: 'Type' },
    { key: 'price_per_night', label: 'Price', align: 'right' as const },
    { key: 'capacity', label: 'Capacity', align: 'center' as const },
    { key: 'status_label', label: 'Status' },
    { key: 'actions', label: '', align: 'right' as const, width: '120px' },
];

const target = ref<AdminRoomRow | null>(null);
const open = ref(false);
const deleting = ref(false);

const askDelete = (room: AdminRoomRow) => {
    target.value = room;
    open.value = true;
};

const confirmDelete = () => {
    if (!target.value) return;
    deleting.value = true;
    router.delete(adminRoomsDestroy({ room: target.value.slug }).url, {
        onFinish: () => {
            deleting.value = false;
            open.value = false;
            target.value = null;
        },
    });
};
</script>

<template>
    <Head title="Admin · Rooms" />

    <div class="space-y-5 p-4 md:p-6">
        <header class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                    Inventory
                </p>
                <h1 class="mt-1 font-serif text-3xl text-[#1a2744]">Rooms</h1>
            </div>
            <Link
                :href="adminRoomsCreate().url"
                class="inline-flex items-center gap-2 rounded-md bg-[#1a2744] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#243558]"
            >
                <Plus class="size-4" />
                New room
            </Link>
        </header>

        <div class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:grid-cols-[2fr_1fr_1fr]">
            <label class="relative">
                <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                <input
                    v-model="search"
                    type="search"
                    placeholder="Search by name…"
                    class="w-full rounded-md border border-slate-200 bg-white py-2 pl-9 pr-3 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                />
            </label>
            <select
                v-model="status"
                class="rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
            >
                <option value="">All statuses</option>
                <option v-for="s in options.statuses" :key="s.value" :value="s.value">
                    {{ s.label }}
                </option>
            </select>
            <select
                v-model="type"
                class="rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
            >
                <option value="">All types</option>
                <option v-for="t in options.types" :key="t.value" :value="t.value">
                    {{ t.label }}
                </option>
            </select>
        </div>

        <DataTable
            :columns="columns"
            :rows="rooms.data"
            :row-key="(row) => row.id"
            empty-state="No rooms match your filters."
        >
            <template #cell-room="{ row }">
                <div class="flex items-center gap-3">
                    <img
                        v-if="row.thumbnail"
                        :src="row.thumbnail"
                        :alt="row.name"
                        class="size-11 rounded-lg object-cover"
                    />
                    <div
                        v-else
                        class="flex size-11 items-center justify-center rounded-lg bg-slate-100 text-xs text-slate-400"
                    >
                        N/A
                    </div>
                    <div>
                        <p class="font-medium text-slate-800">{{ row.name }}</p>
                        <p class="text-xs text-slate-400">Floor {{ row.floor }}</p>
                    </div>
                </div>
            </template>
            <template #cell-price_per_night="{ row }">
                <span class="font-medium text-slate-800">
                    {{ formatPrice(row.price_per_night) }}
                </span>
            </template>
            <template #cell-status_label="{ row }">
                <span
                    class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset"
                    :class="
                        row.status === 'available'
                            ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                            : 'bg-slate-100 text-slate-600 ring-slate-200'
                    "
                >
                    {{ row.status_label }}
                </span>
            </template>
            <template #cell-actions="{ row }">
                <div class="flex justify-end gap-2">
                    <Link
                        :href="adminRoomsEdit({ room: row.slug }).url"
                        class="inline-flex size-8 items-center justify-center rounded-md text-slate-500 transition hover:bg-slate-100 hover:text-[#1a2744]"
                        :aria-label="`Edit ${row.name}`"
                    >
                        <Pencil class="size-4" />
                    </Link>
                    <button
                        type="button"
                        class="inline-flex size-8 items-center justify-center rounded-md text-rose-600 transition hover:bg-rose-50"
                        :aria-label="`Delete ${row.name}`"
                        @click="askDelete(row)"
                    >
                        <Trash2 class="size-4" />
                    </button>
                </div>
            </template>
        </DataTable>

        <p class="text-xs text-slate-400">
            Showing {{ rooms.meta.from ?? 0 }}–{{ rooms.meta.to ?? 0 }} of {{ rooms.meta.total }} rooms.
        </p>

        <ConfirmModal
            v-model:open="open"
            :title="`Delete ${target?.name ?? 'room'}?`"
            description="This permanently removes the room and all its photos. Existing bookings will lose the linked room."
            confirm-label="Delete room"
            variant="danger"
            :processing="deleting"
            @confirm="confirmDelete"
        />
    </div>
</template>
