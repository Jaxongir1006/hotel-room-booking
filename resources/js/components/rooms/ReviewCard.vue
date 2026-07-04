<script setup lang="ts">
import { computed } from 'vue';
import { Star } from 'lucide-vue-next';
import type { RoomReview } from '@/types';

const props = defineProps<{ review: RoomReview }>();

const formattedDate = computed(() =>
    new Date(props.review.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
    }),
);

const guestInitials = computed(() => {
    const name = props.review.guest_name ?? 'Guest';
    return name
        .split(' ')
        .map((part) => part[0]?.toUpperCase() ?? '')
        .slice(0, 2)
        .join('');
});
</script>

<template>
    <article class="rounded-lg border border-slate-200 bg-white p-5">
        <header class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span
                    class="flex size-9 items-center justify-center rounded-full bg-[#1a2744] text-xs font-semibold text-[#c9a84c]"
                >
                    {{ guestInitials }}
                </span>
                <div>
                    <p class="text-sm font-medium text-slate-800">
                        {{ review.guest_name ?? 'Guest' }}
                    </p>
                    <p class="text-xs text-slate-400">{{ formattedDate }}</p>
                </div>
            </div>
            <div class="flex items-center gap-0.5">
                <Star
                    v-for="i in 5"
                    :key="i"
                    class="size-4"
                    :class="
                        i <= review.rating
                            ? 'fill-[#c9a84c] text-[#c9a84c]'
                            : 'text-slate-200'
                    "
                />
            </div>
        </header>
        <p
            v-if="review.comment"
            class="mt-3 text-sm leading-relaxed text-slate-600"
        >
            {{ review.comment }}
        </p>
    </article>
</template>
