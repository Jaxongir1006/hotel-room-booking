<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { CalendarRange, Loader2 } from 'lucide-vue-next';
import { store as storeBooking } from '@/actions/App/Http/Controllers/BookingController';

const props = defineProps<{
    roomSlug: string;
    pricePerNight: number;
    unavailableDates: string[];
}>();

const today = new Date().toISOString().slice(0, 10);

const form = useForm({
    room_slug: props.roomSlug,
    check_in: '',
    check_out: '',
    notes: '',
});

const minCheckIn = ref(today);

const minCheckOut = computed(() => {
    if (!form.check_in) return today;
    const dt = new Date(form.check_in);
    dt.setDate(dt.getDate() + 1);
    return dt.toISOString().slice(0, 10);
});

const nights = computed(() => {
    if (!form.check_in || !form.check_out) return 0;
    const ci = new Date(form.check_in);
    const co = new Date(form.check_out);
    const ms = co.getTime() - ci.getTime();
    return Math.max(0, Math.round(ms / (1000 * 60 * 60 * 24)));
});

const total = computed(() => nights.value * props.pricePerNight);

const unavailableSet = computed(() => new Set(props.unavailableDates));

const overlapsUnavailable = computed(() => {
    if (!form.check_in || !form.check_out) return false;
    const start = new Date(form.check_in);
    const end = new Date(form.check_out);
    const cursor = new Date(start);
    while (cursor < end) {
        if (unavailableSet.value.has(cursor.toISOString().slice(0, 10))) {
            return true;
        }
        cursor.setDate(cursor.getDate() + 1);
    }
    return false;
});

watch(
    () => form.check_in,
    () => {
        if (form.check_out && form.check_out <= form.check_in) {
            form.check_out = '';
        }
    },
);

const formatPrice = (value: number) =>
    new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2,
    }).format(value);

const submit = () => {
    form.post(storeBooking().url, { preserveScroll: true });
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-5">
        <div class="grid grid-cols-2 gap-3">
            <label class="block">
                <span class="text-xs font-medium uppercase tracking-wider text-slate-500">
                    Check-in
                </span>
                <input
                    v-model="form.check_in"
                    type="date"
                    :min="minCheckIn"
                    required
                    class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                />
            </label>
            <label class="block">
                <span class="text-xs font-medium uppercase tracking-wider text-slate-500">
                    Check-out
                </span>
                <input
                    v-model="form.check_out"
                    type="date"
                    :min="minCheckOut"
                    required
                    class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                />
            </label>
        </div>

        <p
            v-if="overlapsUnavailable"
            class="rounded-md bg-rose-50 px-3 py-2 text-xs text-rose-700"
        >
            Some dates in your range are already reserved. Please pick a different window.
        </p>

        <label class="block">
            <span class="text-xs font-medium uppercase tracking-wider text-slate-500">
                Notes for the concierge (optional)
            </span>
            <textarea
                v-model="form.notes"
                rows="3"
                maxlength="500"
                placeholder="Special requests, arrival time…"
                class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
            />
        </label>

        <div class="rounded-lg bg-slate-50 p-4 text-sm">
            <div class="flex items-center justify-between text-slate-600">
                <span class="flex items-center gap-2">
                    <CalendarRange class="size-4 text-slate-400" />
                    {{ formatPrice(pricePerNight) }} × {{ nights }} {{ nights === 1 ? 'night' : 'nights' }}
                </span>
                <span>{{ formatPrice(total) }}</span>
            </div>
            <div class="mt-3 flex items-center justify-between border-t border-slate-200 pt-3 text-base font-medium text-[#1a2744]">
                <span>Total</span>
                <span class="font-serif text-lg">{{ formatPrice(total) }}</span>
            </div>
        </div>

        <p
            v-if="form.errors.room_slug"
            class="text-sm text-rose-600"
        >
            {{ form.errors.room_slug }}
        </p>
        <p
            v-if="form.errors.check_in"
            class="text-sm text-rose-600"
        >
            {{ form.errors.check_in }}
        </p>
        <p
            v-if="form.errors.check_out"
            class="text-sm text-rose-600"
        >
            {{ form.errors.check_out }}
        </p>

        <button
            type="submit"
            :disabled="form.processing || nights === 0 || overlapsUnavailable"
            class="flex w-full items-center justify-center gap-2 rounded-md bg-[#1a2744] px-4 py-3 text-sm font-medium text-white transition hover:bg-[#243558] disabled:cursor-not-allowed disabled:opacity-50"
        >
            <Loader2 v-if="form.processing" class="size-4 animate-spin" />
            Reserve · {{ formatPrice(total) }}
        </button>
    </form>
</template>
