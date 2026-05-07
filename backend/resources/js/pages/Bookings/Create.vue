<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, BedDouble } from 'lucide-vue-next';
import BookingForm from '@/components/bookings/BookingForm.vue';
import { index as roomsIndex, show as roomShow } from '@/routes/rooms';
import { dashboard } from '@/routes';
import type { RoomDetail } from '@/types';

const props = defineProps<{
    room: { data: RoomDetail } | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: dashboard().url },
            { title: 'New reservation', href: '#' },
        ],
    },
});

const room = computed(() => props.room?.data ?? null);
</script>

<template>
    <Head title="New reservation · Aurelia Stay" />

    <div class="mx-auto w-full max-w-5xl p-6">
        <Link
            :href="roomsIndex().url"
            class="inline-flex items-center gap-1 text-sm text-slate-500 transition hover:text-[#1a2744]"
        >
            <ArrowLeft class="size-4" />
            Back to rooms
        </Link>

        <div v-if="room" class="mt-6 grid gap-8 lg:grid-cols-[1.2fr_1fr]">
            <section>
                <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                    Reserve · {{ room.type_label }}
                </p>
                <h1 class="mt-2 font-serif text-3xl text-[#1a2744]">{{ room.name }}</h1>
                <p class="mt-3 text-sm leading-relaxed text-slate-500">
                    {{ room.description }}
                </p>

                <Link
                    :href="roomShow({ slug: room.slug }).url"
                    class="mt-4 inline-flex items-center gap-1 text-xs uppercase tracking-widest text-[#1a2744] underline-offset-4 hover:underline"
                >
                    <BedDouble class="size-3.5" />
                    See full room details
                </Link>

                <div
                    v-if="room.thumbnail"
                    class="mt-6 overflow-hidden rounded-xl"
                >
                    <img :src="room.thumbnail" :alt="room.name" class="aspect-[16/10] w-full object-cover" />
                </div>
            </section>

            <aside class="lg:sticky lg:top-6 lg:self-start">
                <div class="rounded-xl border border-slate-200 bg-white p-6">
                    <h2 class="font-serif text-lg text-[#1a2744]">Your dates</h2>
                    <BookingForm
                        class="mt-4"
                        :room-slug="room.slug"
                        :price-per-night="room.price_per_night"
                        :unavailable-dates="room.unavailable_dates"
                    />
                </div>
            </aside>
        </div>

        <div
            v-else
            class="mt-10 rounded-xl border border-dashed border-slate-200 bg-white p-12 text-center"
        >
            <p class="font-serif text-lg text-[#1a2744]">No room selected</p>
            <p class="mt-2 text-sm text-slate-500">
                Pick a room first to start a reservation.
            </p>
            <Link
                :href="roomsIndex().url"
                class="mt-6 inline-block rounded-md bg-[#1a2744] px-5 py-2 text-sm font-medium text-white transition hover:bg-[#243558]"
            >
                Browse rooms
            </Link>
        </div>
    </div>
</template>
