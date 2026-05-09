<script setup lang="ts">
import type { Component } from 'vue';

defineProps<{
    label: string;
    value: string | number;
    helper?: string;
    icon?: Component;
    accent?: 'navy' | 'gold' | 'rose' | 'emerald';
}>();

const accentMap: Record<string, string> = {
    navy: 'bg-[#1a2744]/5 text-[#1a2744]',
    gold: 'bg-[#c9a84c]/15 text-[#8a6d20]',
    rose: 'bg-rose-50 text-rose-700',
    emerald: 'bg-emerald-50 text-emerald-700',
};
</script>

<template>
    <div
        class="rounded-xl border border-slate-200 bg-white p-5 transition hover:shadow-sm"
    >
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-slate-500">
                    {{ label }}
                </p>
                <p class="mt-2 font-serif text-3xl text-[#1a2744]">{{ value }}</p>
                <p v-if="helper" class="mt-1 text-xs text-slate-400">{{ helper }}</p>
            </div>
            <span
                v-if="icon"
                class="flex size-10 items-center justify-center rounded-lg"
                :class="accentMap[accent ?? 'navy']"
            >
                <component :is="icon" class="size-5" />
            </span>
        </div>
    </div>
</template>
