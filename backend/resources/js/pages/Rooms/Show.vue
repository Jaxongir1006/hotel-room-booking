<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    ArrowLeft,
    BedDouble,
    Building2,
    CheckCircle2,
    Star,
    Users,
} from 'lucide-vue-next';
import GuestLayout from '@/layouts/GuestLayout.vue';
import ImageGallery from '@/components/rooms/ImageGallery.vue';
import ReviewCard from '@/components/rooms/ReviewCard.vue';
import { index as roomsIndex } from '@/routes/rooms';
import { login, register } from '@/routes';
import { create as bookingCreate } from '@/actions/App/Http/Controllers/BookingController';
import { usePage } from '@inertiajs/vue3';
import type { RoomDetail } from '@/types';

const props = defineProps<{
    room: { data: RoomDetail };
}>();

const page = usePage();
const isAuthenticated = computed(() => Boolean(page.props.auth?.user));

const room = computed(() => props.room.data);
const galleryImages = computed(() => {
    const main = room.value.thumbnail ? [room.value.thumbnail] : [];
    return [...main, ...(room.value.images ?? [])];
});

const formatPrice = (price: number) =>
    new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(price);
</script>

<template>
    <Head :title="`${room.name} · Aurelia Stay`" />

    <GuestLayout>
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <Link
                :href="roomsIndex().url"
                class="inline-flex items-center gap-1 text-sm text-slate-500 transition hover:text-[#1a2744]"
            >
                <ArrowLeft class="size-4" />
                Back to rooms
            </Link>

            <div class="mt-6 grid gap-10 lg:grid-cols-[1.6fr_1fr]">
                <div>
                    <ImageGallery :images="galleryImages" :alt="room.name" />

                    <div class="mt-8">
                        <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                            {{ room.type_label }} · Floor {{ room.floor }}
                        </p>
                        <div class="mt-2 flex flex-wrap items-center justify-between gap-3">
                            <h1 class="font-serif text-3xl text-[#1a2744] md:text-4xl">
                                {{ room.name }}
                            </h1>
                            <div
                                v-if="room.average_rating"
                                class="flex items-center gap-1.5 text-sm text-slate-700"
                            >
                                <Star class="size-4 fill-[#c9a84c] text-[#c9a84c]" />
                                <span class="font-medium">{{ room.average_rating }}</span>
                                <span class="text-slate-400">({{ room.reviews_count }} reviews)</span>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-wrap gap-6 text-sm text-slate-600">
                            <div class="flex items-center gap-2">
                                <Users class="size-4 text-slate-400" />
                                Up to {{ room.capacity }} guests
                            </div>
                            <div class="flex items-center gap-2">
                                <BedDouble class="size-4 text-slate-400" />
                                {{ room.type_label }} occupancy
                            </div>
                            <div class="flex items-center gap-2">
                                <Building2 class="size-4 text-slate-400" />
                                Floor {{ room.floor }}
                            </div>
                        </div>

                        <p class="mt-6 whitespace-pre-line text-base leading-relaxed text-slate-600">
                            {{ room.description }}
                        </p>
                    </div>

                    <section class="mt-10">
                        <h2 class="font-serif text-2xl text-[#1a2744]">Amenities</h2>
                        <div
                            v-if="room.amenities.length"
                            class="mt-4 grid gap-3 sm:grid-cols-2"
                        >
                            <div
                                v-for="amenity in room.amenities"
                                :key="amenity.id"
                                class="flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-3"
                            >
                                <span
                                    class="flex size-9 items-center justify-center rounded-md bg-[#1a2744]/5 text-[#1a2744]"
                                >
                                    <CheckCircle2 class="size-5" />
                                </span>
                                <span class="text-sm text-slate-700">{{ amenity.name }}</span>
                            </div>
                        </div>
                        <p v-else class="mt-4 text-sm text-slate-500">No amenities listed.</p>
                    </section>

                    <section class="mt-12">
                        <div class="flex items-end justify-between">
                            <h2 class="font-serif text-2xl text-[#1a2744]">Guest reviews</h2>
                            <span class="text-sm text-slate-400">
                                {{ room.reviews_count }} total
                            </span>
                        </div>
                        <div v-if="room.reviews.length" class="mt-5 grid gap-4">
                            <ReviewCard
                                v-for="review in room.reviews"
                                :key="review.id"
                                :review="review"
                            />
                        </div>
                        <p v-else class="mt-5 rounded-lg border border-dashed border-slate-200 bg-white p-6 text-center text-sm text-slate-500">
                            No reviews yet — be among the first to share your stay.
                        </p>
                    </section>
                </div>

                <!-- Booking sidebar -->
                <aside class="lg:sticky lg:top-24 lg:self-start">
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-baseline gap-1">
                            <span class="font-serif text-3xl text-[#1a2744]">
                                {{ formatPrice(room.price_per_night) }}
                            </span>
                            <span class="text-sm text-slate-500">/ night</span>
                        </div>

                        <div v-if="isAuthenticated" class="mt-5">
                            <Link
                                :href="bookingCreate({ query: { room: room.slug } }).url"
                                class="block cursor-pointer rounded-md bg-[#1a2744] px-4 py-3 text-center text-sm font-medium text-white shadow-sm transition-colors duration-200 hover:bg-[#243558]"
                            >
                                Reserve this room
                            </Link>
                        </div>
                        <div v-else class="mt-5 space-y-2">
                            <Link
                                :href="login().url"
                                class="block cursor-pointer rounded-md bg-[#1a2744] px-4 py-3 text-center text-sm font-medium text-white transition-colors duration-200 hover:bg-[#243558]"
                            >
                                Sign in to reserve
                            </Link>
                            <Link
                                :href="register().url"
                                class="block cursor-pointer rounded-md border border-slate-200 px-4 py-3 text-center text-sm text-slate-600 transition-colors duration-200 hover:border-[#c9a84c] hover:text-[#1a2744]"
                            >
                                Create an account
                            </Link>
                        </div>

                        <div
                            v-if="room.unavailable_dates.length"
                            class="mt-6 border-t border-slate-100 pt-4"
                        >
                            <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                                Already booked
                            </p>
                            <p class="mt-2 text-xs text-slate-500">
                                {{ room.unavailable_dates.length }} dates in the next six months are
                                unavailable. Detailed calendar arrives with the booking form.
                            </p>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </GuestLayout>
</template>
