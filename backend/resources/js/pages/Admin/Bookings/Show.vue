<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, CalendarDays, Loader2, MessageSquare, Star } from 'lucide-vue-next';
import StatusBadge from '@/components/bookings/StatusBadge.vue';
import {
    index as adminBookingsIndex,
    update as adminBookingsUpdate,
} from '@/actions/App/Http/Controllers/Admin/BookingController';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import type { Booking, BookingStatus } from '@/types';

type Option = { value: string; label: string };

const props = defineProps<{
    booking: { data: Booking };
    statuses: Option[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: adminDashboard().url },
            { title: 'Bookings', href: adminBookingsIndex().url },
            { title: 'Reservation', href: '#' },
        ],
    },
});

const booking = computed(() => props.booking.data);
const selectedStatus = ref<BookingStatus>(booking.value.status);
const updating = ref(false);

const submitStatus = () => {
    if (selectedStatus.value === booking.value.status) return;
    updating.value = true;
    router.patch(
        adminBookingsUpdate({ booking: booking.value.reference }).url,
        { status: selectedStatus.value },
        {
            preserveScroll: true,
            onFinish: () => (updating.value = false),
        },
    );
};

const formatPrice = (price: number) =>
    new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2,
    }).format(price);

const formatDate = (iso: string) =>
    new Date(iso).toLocaleDateString('en-US', {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
</script>

<template>
    <Head :title="`Admin · ${booking.reference}`" />

    <div class="mx-auto max-w-4xl space-y-6 p-4 md:p-6">
        <Link
            :href="adminBookingsIndex().url"
            class="inline-flex items-center gap-1 text-sm text-slate-500 transition hover:text-[#1a2744]"
        >
            <ArrowLeft class="size-4" />
            All bookings
        </Link>

        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                        Reservation
                    </p>
                    <h1 class="mt-1 font-serif text-2xl text-[#1a2744]">
                        {{ booking.reference }}
                    </h1>
                </div>
                <StatusBadge :status="booking.status" :label="booking.status_label" />
            </div>

            <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Guest</dt>
                    <dd class="mt-1 text-sm text-slate-800">{{ booking.user?.name }}</dd>
                    <dd class="text-xs text-slate-400">{{ booking.user?.email }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Room</dt>
                    <dd class="mt-1 text-sm text-slate-800">{{ booking.room?.name }}</dd>
                    <dd class="text-xs text-slate-400">{{ booking.room?.type_label }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Stay</dt>
                    <dd class="mt-1 flex items-center gap-2 text-sm text-slate-700">
                        <CalendarDays class="size-4 text-slate-400" />
                        {{ formatDate(booking.check_in) }}
                    </dd>
                    <dd class="mt-1 flex items-center gap-2 text-sm text-slate-700">
                        <CalendarDays class="size-4 text-slate-400" />
                        {{ formatDate(booking.check_out) }}
                    </dd>
                    <dd class="mt-1 text-xs text-slate-400">
                        {{ booking.nights }} {{ booking.nights === 1 ? 'night' : 'nights' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs uppercase tracking-wider text-slate-400">Total</dt>
                    <dd class="mt-1 font-serif text-xl text-[#1a2744]">
                        {{ formatPrice(booking.total_price) }}
                    </dd>
                </div>
            </dl>

            <div
                v-if="booking.notes"
                class="mt-6 rounded-lg bg-slate-50 p-4 text-sm text-slate-600"
            >
                <p class="flex items-center gap-2 text-xs uppercase tracking-wider text-slate-400">
                    <MessageSquare class="size-3.5" />
                    Guest note
                </p>
                <p class="mt-2">{{ booking.notes }}</p>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                Manage
            </p>
            <h2 class="mt-1 font-serif text-xl text-[#1a2744]">Update status</h2>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <select
                    v-model="selectedStatus"
                    class="rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                >
                    <option
                        v-for="s in statuses"
                        :key="s.value"
                        :value="s.value"
                    >
                        {{ s.label }}
                    </option>
                </select>
                <button
                    type="button"
                    :disabled="updating || selectedStatus === booking.status"
                    class="inline-flex items-center gap-2 rounded-md bg-[#1a2744] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#243558] disabled:cursor-not-allowed disabled:opacity-60"
                    @click="submitStatus"
                >
                    <Loader2 v-if="updating" class="size-4 animate-spin" />
                    Save status
                </button>
            </div>
            <p class="mt-3 text-xs text-slate-400">
                Marking a booking as Confirmed will queue an invoice email to the guest.
            </p>
        </div>

        <div
            v-if="booking.review"
            class="rounded-xl border border-slate-200 bg-white p-6"
        >
            <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                Guest review
            </p>
            <div class="mt-2 flex items-center gap-2">
                <Star
                    v-for="i in 5"
                    :key="i"
                    class="size-4"
                    :class="
                        i <= booking.review.rating
                            ? 'fill-[#c9a84c] text-[#c9a84c]'
                            : 'text-slate-200'
                    "
                />
                <span class="text-sm text-slate-500">
                    {{ booking.review.rating }} / 5
                </span>
            </div>
            <p
                v-if="booking.review.comment"
                class="mt-3 whitespace-pre-line text-sm text-slate-600"
            >
                {{ booking.review.comment }}
            </p>
        </div>
    </div>
</template>
