<script setup lang="ts">
import { computed } from 'vue';
import type { BookingStatus } from '@/types';

const props = defineProps<{
    status: BookingStatus;
    label?: string;
}>();

const classes = computed(() => {
    const map: Record<BookingStatus, string> = {
        pending: 'bg-amber-50 text-amber-700 ring-amber-200',
        confirmed: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        cancelled: 'bg-rose-50 text-rose-700 ring-rose-200',
        completed: 'bg-sky-50 text-sky-700 ring-sky-200',
    };
    return map[props.status];
});

const text = computed(() => props.label ?? props.status.charAt(0).toUpperCase() + props.status.slice(1));
</script>

<template>
    <span
        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset"
        :class="classes"
    >
        {{ text }}
    </span>
</template>
