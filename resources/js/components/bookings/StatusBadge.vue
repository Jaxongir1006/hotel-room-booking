<script setup lang="ts">
import { computed } from 'vue';
import type { BookingStatus } from '@/types';

const props = defineProps<{
    status: BookingStatus;
    label?: string;
}>();

const classes = computed(() => {
    const map: Record<BookingStatus, string> = {
        pending: 'bg-amber-50 text-amber-800 ring-amber-200 [--dot:theme(colors.amber.500)]',
        confirmed: 'bg-emerald-50 text-emerald-800 ring-emerald-200 [--dot:theme(colors.emerald.500)]',
        cancelled: 'bg-rose-50 text-rose-800 ring-rose-200 [--dot:theme(colors.rose.500)]',
        completed: 'bg-sky-50 text-sky-800 ring-sky-200 [--dot:theme(colors.sky.500)]',
    };
    return map[props.status];
});

const text = computed(() => props.label ?? props.status.charAt(0).toUpperCase() + props.status.slice(1));
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-medium tracking-wide ring-1 ring-inset"
        :class="classes"
    >
        <span
            aria-hidden="true"
            class="size-1.5 rounded-full"
            style="background-color: var(--dot)"
        />
        {{ text }}
    </span>
</template>
