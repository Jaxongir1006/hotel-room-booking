<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { SidebarInset } from '@/components/ui/sidebar';
import type { AppVariant } from '@/types';

type Props = {
    variant?: AppVariant;
    class?: string;
};

const props = withDefaults(defineProps<Props>(), {
    variant: 'sidebar',
});
const className = computed(() => props.class);

// Re-key on URL so every Inertia navigation re-triggers the fade animation.
const page = usePage();
const navKey = computed(() => page.url);
</script>

<template>
    <SidebarInset v-if="props.variant === 'sidebar'" :class="className">
        <div :key="navKey" class="animate-fade-in">
            <slot />
        </div>
    </SidebarInset>
    <main
        v-else
        class="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl"
        :class="className"
    >
        <slot />
    </main>
</template>
