<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Hotel } from 'lucide-vue-next';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative flex min-h-svh w-full overflow-hidden">
        <!-- Brand panel (hidden on small screens) -->
        <aside
            class="relative hidden w-1/2 overflow-hidden bg-[#1a2744] text-white lg:block"
        >
            <div
                aria-hidden="true"
                class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=1600&q=80')] bg-cover bg-center opacity-40"
            />
            <div
                aria-hidden="true"
                class="absolute inset-0 bg-gradient-to-br from-[#0e1830] via-[#1a2744]/80 to-[#1a2744]/40"
            />
            <div
                aria-hidden="true"
                class="pointer-events-none absolute -bottom-24 -left-16 size-80 rounded-full bg-[#c9a84c]/15 blur-3xl"
            />

            <div class="relative flex h-full flex-col justify-between p-12">
                <Link
                    :href="home().url"
                    class="group inline-flex w-fit items-center gap-2"
                >
                    <span
                        class="flex size-10 items-center justify-center rounded-md bg-[#c9a84c] text-[#1a2744] transition-transform duration-300 group-hover:rotate-6"
                    >
                        <Hotel class="size-5" />
                    </span>
                    <span class="font-serif text-lg tracking-wide">Aurelia Stay</span>
                </Link>

                <div class="animate-fade-up max-w-md">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#c9a84c]">
                        Concierge access
                    </p>
                    <h2 class="mt-3 font-serif text-4xl leading-tight">
                        Pick up where you left off, or plan a new escape.
                    </h2>
                    <p class="mt-4 text-sm leading-relaxed text-white/70">
                        Manage reservations, share reviews, and unlock returning-guest
                        amenities — all in one place.
                    </p>
                </div>

                <p class="text-xs text-white/40">
                    © {{ new Date().getFullYear() }} Aurelia Stay
                </p>
            </div>
        </aside>

        <!-- Form panel -->
        <main
            class="relative flex w-full flex-col items-center justify-center bg-background p-6 md:p-10 lg:w-1/2"
        >
            <Link
                :href="home().url"
                class="absolute left-6 top-6 inline-flex items-center gap-2 text-sm text-slate-500 transition-colors duration-200 hover:text-[#1a2744] lg:hidden"
            >
                <span
                    class="flex size-8 items-center justify-center rounded-md bg-[#c9a84c] text-[#1a2744]"
                >
                    <Hotel class="size-4" />
                </span>
                <span class="font-serif">Aurelia Stay</span>
            </Link>

            <div class="animate-fade-up w-full max-w-sm">
                <div class="space-y-2 text-center">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-[#c9a84c]">
                        Welcome
                    </p>
                    <h1 class="font-serif text-3xl text-[#1a2744]">{{ title }}</h1>
                    <p
                        v-if="description"
                        class="text-sm text-slate-500"
                    >
                        {{ description }}
                    </p>
                </div>

                <div class="mt-8">
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>
