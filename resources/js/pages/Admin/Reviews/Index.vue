<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Search, Star, Trash2 } from 'lucide-vue-next';
import { debounce } from '@/lib/debounce';
import DataTable from '@/components/admin/DataTable.vue';
import ConfirmModal from '@/components/admin/ConfirmModal.vue';
import {
    destroy as adminReviewsDestroy,
    index as adminReviewsIndex,
} from '@/actions/App/Http/Controllers/Admin/ReviewController';
import { show as roomShow } from '@/routes/rooms';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import type { FlatPaginated } from '@/types';

type AdminReviewRow = {
    id: number;
    rating: number;
    comment: string | null;
    created_at: string | null;
    guest_name: string | null;
    guest_email: string | null;
    room: { id: number; name: string; slug: string; thumbnail: string | null } | null;
    booking_reference: string | null;
};

const props = defineProps<{
    reviews: FlatPaginated<AdminReviewRow>;
    filters: { q: string | null; rating: number | null };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: adminDashboard().url },
            { title: 'Reviews', href: adminReviewsIndex().url },
        ],
    },
});

const search = ref(props.filters.q ?? '');
const rating = ref<string>(props.filters.rating ? String(props.filters.rating) : '');

const apply = debounce(() => {
    router.get(
        adminReviewsIndex().url,
        {
            q: search.value || undefined,
            rating: rating.value || undefined,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}, 250);

watch([search, rating], apply);

const target = ref<AdminReviewRow | null>(null);
const open = ref(false);
const deleting = ref(false);

const askDelete = (review: AdminReviewRow) => {
    target.value = review;
    open.value = true;
};

const confirmDelete = () => {
    if (!target.value) return;
    deleting.value = true;
    router.delete(adminReviewsDestroy({ review: target.value.id }).url, {
        preserveScroll: true,
        onFinish: () => {
            deleting.value = false;
            open.value = false;
            target.value = null;
        },
    });
};

const formatDate = (iso: string | null) =>
    iso
        ? new Date(iso).toLocaleDateString('en-US', {
              month: 'short',
              day: 'numeric',
              year: 'numeric',
          })
        : '—';

const columns = [
    { key: 'guest', label: 'Guest' },
    { key: 'room', label: 'Room' },
    { key: 'rating', label: 'Rating' },
    { key: 'comment', label: 'Comment' },
    { key: 'created_at', label: 'When' },
    { key: 'actions', label: '', align: 'right' as const, width: '60px' },
];
</script>

<template>
    <Head title="Admin · Reviews" />

    <div class="space-y-5 p-4 md:p-6">
        <header>
            <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                Feedback
            </p>
            <h1 class="mt-1 font-serif text-3xl text-[#1a2744]">Reviews</h1>
        </header>

        <div class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:grid-cols-[2fr_1fr]">
            <label class="relative">
                <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                <input
                    v-model="search"
                    type="search"
                    placeholder="Search by guest, room, comment…"
                    class="w-full rounded-md border border-slate-200 bg-white py-2 pl-9 pr-3 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                />
            </label>
            <select
                v-model="rating"
                class="rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
            >
                <option value="">All ratings</option>
                <option v-for="r in [5, 4, 3, 2, 1]" :key="r" :value="String(r)">
                    {{ r }} stars
                </option>
            </select>
        </div>

        <DataTable
            :columns="columns"
            :rows="reviews.data"
            :row-key="(row) => row.id"
            empty-state="No reviews to moderate."
        >
            <template #cell-guest="{ row }">
                <div>
                    <p class="font-medium text-slate-800">{{ row.guest_name ?? '—' }}</p>
                    <p class="text-xs text-slate-400">{{ row.guest_email }}</p>
                </div>
            </template>
            <template #cell-room="{ row }">
                <Link
                    v-if="row.room"
                    :href="roomShow({ slug: row.room.slug }).url"
                    class="text-sm text-[#1a2744] hover:text-[#c9a84c]"
                >
                    {{ row.room.name }}
                </Link>
                <span v-else class="text-slate-400">—</span>
            </template>
            <template #cell-rating="{ row }">
                <div class="flex items-center gap-0.5">
                    <Star
                        v-for="i in 5"
                        :key="i"
                        class="size-4"
                        :class="
                            i <= row.rating
                                ? 'fill-[#c9a84c] text-[#c9a84c]'
                                : 'text-slate-200'
                        "
                    />
                </div>
            </template>
            <template #cell-comment="{ row }">
                <p class="line-clamp-2 max-w-md text-sm text-slate-600">
                    {{ row.comment ?? '—' }}
                </p>
            </template>
            <template #cell-created_at="{ row }">
                <span class="text-xs text-slate-500">{{ formatDate(row.created_at) }}</span>
            </template>
            <template #cell-actions="{ row }">
                <button
                    type="button"
                    class="inline-flex size-8 items-center justify-center rounded-md text-rose-600 transition hover:bg-rose-50"
                    aria-label="Delete review"
                    @click="askDelete(row)"
                >
                    <Trash2 class="size-4" />
                </button>
            </template>
        </DataTable>

        <p class="text-xs text-slate-400">
            Showing {{ reviews.from ?? 0 }}–{{ reviews.to ?? 0 }} of
            {{ reviews.total }} reviews.
        </p>

        <ConfirmModal
            v-model:open="open"
            :title="`Delete review by ${target?.guest_name ?? 'guest'}?`"
            description="This permanently removes the review and updates the room's average rating."
            confirm-label="Delete review"
            variant="danger"
            :processing="deleting"
            @confirm="confirmDelete"
        />
    </div>
</template>
