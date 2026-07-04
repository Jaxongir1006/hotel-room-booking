<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, CalendarDays, Loader2, MessageSquare, Star } from 'lucide-vue-next';
import StatusBadge from '@/components/bookings/StatusBadge.vue';
import ReviewForm from '@/components/bookings/ReviewForm.vue';
import { dashboard } from '@/routes';
import { index as roomsIndex, show as roomShow } from '@/routes/rooms';
import {
    cancel as bookingCancel,
    index as bookingsIndex,
} from '@/actions/App/Http/Controllers/BookingController';
import type { Booking } from '@/types';

const props = defineProps<{
    booking: { data: Booking };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard().url },
            { title: 'My bookings', href: bookingsIndex().url },
            { title: 'Reservation', href: '#' },
        ],
    },
});

const booking = computed(() => props.booking.data);
const cancelling = ref(false);

const cancel = () => {
    if (!confirm('Cancel this reservation? This cannot be undone.')) return;
    cancelling.value = true;
    router.post(
        bookingCancel({ booking: booking.value.reference }).url,
        {},
        {
            onFinish: () => (cancelling.value = false),
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
    <Head :title="`Reservation ${booking.reference}`" />

    <div class="mx-auto w-full max-w-4xl p-6">
        <Link
            :href="bookingsIndex().url"
            class="inline-flex items-center gap-1 text-sm text-slate-500 transition hover:text-[#1a2744]"
        >
            <ArrowLeft class="size-4" />
            All reservations
        </Link>

        <div class="mt-6 rounded-xl border border-slate-200 bg-white p-8">
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

            <div class="mt-8 grid gap-6 sm:grid-cols-2">
                <div class="flex items-start gap-4">
                    <img
                        v-if="booking.room?.thumbnail"
                        :src="booking.room.thumbnail"
                        :alt="booking.room?.name ?? ''"
                        class="size-20 rounded-lg object-cover"
                    />
                    <div>
                        <p class="text-xs uppercase tracking-wider text-slate-400">Room</p>
                        <Link
                            v-if="booking.room"
                            :href="roomShow({ slug: booking.room.slug }).url"
                            class="font-serif text-base text-[#1a2744] transition hover:text-[#c9a84c]"
                        >
                            {{ booking.room.name }}
                        </Link>
                        <p class="text-xs text-slate-400">{{ booking.room?.type_label }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-400">Stay</p>
                    <p class="mt-1 flex items-center gap-2 text-sm text-slate-700">
                        <CalendarDays class="size-4 text-slate-400" />
                        {{ formatDate(booking.check_in) }}
                    </p>
                    <p class="mt-1 flex items-center gap-2 text-sm text-slate-700">
                        <CalendarDays class="size-4 text-slate-400" />
                        {{ formatDate(booking.check_out) }}
                    </p>
                    <p class="mt-2 text-xs text-slate-400">
                        {{ booking.nights }} {{ booking.nights === 1 ? 'night' : 'nights' }}
                    </p>
                </div>
            </div>

            <div
                v-if="booking.notes"
                class="mt-6 rounded-lg bg-slate-50 p-4 text-sm text-slate-600"
            >
                <p class="flex items-center gap-2 text-xs uppercase tracking-wider text-slate-400">
                    <MessageSquare class="size-3.5" />
                    Your note
                </p>
                <p class="mt-2">{{ booking.notes }}</p>
            </div>

            <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-6">
                <div>
                    <p class="text-xs uppercase tracking-wider text-slate-400">Total</p>
                    <p class="font-serif text-2xl text-[#1a2744]">
                        {{ formatPrice(booking.total_price) }}
                    </p>
                </div>

                <button
                    v-if="booking.is_cancellable"
                    type="button"
                    :disabled="cancelling"
                    @click="cancel"
                    class="inline-flex items-center gap-2 rounded-md border border-rose-200 bg-white px-4 py-2 text-sm font-medium text-rose-700 transition hover:bg-rose-50 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <Loader2 v-if="cancelling" class="size-4 animate-spin" />
                    Cancel reservation
                </button>
            </div>
        </div>

        <section class="mt-6">
            <div
                v-if="booking.review"
                class="rounded-xl border border-slate-200 bg-white p-6"
            >
                <header>
                    <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                        Your review
                    </p>
                    <div class="mt-1 flex items-center gap-2">
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
                </header>
                <p
                    v-if="booking.review.comment"
                    class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-600"
                >
                    {{ booking.review.comment }}
                </p>
                <p
                    v-else
                    class="mt-3 text-sm italic text-slate-400"
                >
                    No comment shared.
                </p>
            </div>

            <ReviewForm
                v-else-if="booking.can_be_reviewed"
                :booking-reference="booking.reference"
            />
        </section>

        <p class="mt-4 text-xs text-slate-400">
            Need to make a change? <Link :href="roomsIndex().url" class="underline-offset-2 hover:underline">Browse other rooms</Link>
            or reach the concierge at concierge@aurelia.example.
        </p>
    </div>
</template>
