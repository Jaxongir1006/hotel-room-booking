<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Star, Users } from 'lucide-vue-next';
import { show as roomShow } from '@/routes/rooms';
import type { RoomSummary } from '@/types';

defineProps<{ room: RoomSummary }>();

const formatPrice = (price: number) =>
    new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0,
    }).format(price);
</script>

<template>
    <Link
        :href="roomShow({ slug: room.slug }).url"
        class="group block cursor-pointer overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-[#c9a84c]/50 hover:shadow-xl"
    >
        <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
            <img
                v-if="room.thumbnail"
                :src="room.thumbnail"
                :alt="room.name"
                loading="lazy"
                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
            />
            <div
                v-else
                class="flex h-full items-center justify-center text-slate-400"
            >
                No image
            </div>
            <div
                class="absolute left-3 top-3 rounded-full bg-[#1a2744]/90 px-3 py-1 text-xs uppercase tracking-widest text-[#c9a84c] backdrop-blur"
            >
                {{ room.type_label }}
            </div>
        </div>

        <div class="space-y-3 p-5">
            <div class="flex items-start justify-between gap-2">
                <h3
                    class="font-serif text-lg leading-tight text-[#1a2744] transition group-hover:text-[#c9a84c]"
                >
                    {{ room.name }}
                </h3>
                <div
                    v-if="room.average_rating"
                    class="flex shrink-0 items-center gap-1 text-sm text-slate-700"
                >
                    <Star class="size-4 fill-[#c9a84c] text-[#c9a84c]" />
                    <span class="font-medium">{{ room.average_rating }}</span>
                    <span class="text-slate-400">({{ room.reviews_count }})</span>
                </div>
            </div>

            <p class="line-clamp-2 text-sm leading-relaxed text-slate-500">
                {{ room.description_excerpt }}
            </p>

            <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                <div class="flex items-center gap-1.5 text-sm text-slate-600">
                    <Users class="size-4 text-slate-400" />
                    <span>Up to {{ room.capacity }} guests</span>
                </div>
                <div class="text-right">
                    <span class="font-serif text-lg font-medium text-[#1a2744]">
                        {{ formatPrice(room.price_per_night) }}
                    </span>
                    <span class="text-xs text-slate-500"> / night</span>
                </div>
            </div>
        </div>
    </Link>
</template>
