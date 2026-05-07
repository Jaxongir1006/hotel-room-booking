<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Hotel, User } from 'lucide-vue-next';
import { dashboard, home, login, logout, register } from '@/routes';
import { index as roomsIndex } from '@/routes/rooms';

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
const isAdmin = computed(() => user.value?.role === 'admin');
</script>

<template>
    <div class="flex min-h-screen flex-col bg-white text-slate-900 antialiased">
        <header
            class="sticky top-0 z-40 border-b border-white/10 bg-[#1a2744]/95 text-white backdrop-blur supports-[backdrop-filter]:bg-[#1a2744]/85"
        >
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <Link :href="home().url" class="flex items-center gap-2">
                    <span
                        class="flex size-9 items-center justify-center rounded-md bg-[#c9a84c] text-[#1a2744]"
                    >
                        <Hotel class="size-5" />
                    </span>
                    <span class="font-serif text-lg tracking-wide">Aurelia Stay</span>
                </Link>

                <nav class="hidden items-center gap-8 text-sm font-medium md:flex">
                    <Link
                        :href="home().url"
                        class="transition hover:text-[#c9a84c]"
                    >Home</Link>
                    <Link
                        :href="roomsIndex().url"
                        class="transition hover:text-[#c9a84c]"
                    >Rooms</Link>
                    <a href="#testimonials" class="transition hover:text-[#c9a84c]">Reviews</a>
                </nav>

                <div class="flex items-center gap-3">
                    <template v-if="user">
                        <Link
                            v-if="isAdmin"
                            :href="dashboard().url"
                            class="hidden rounded-md border border-[#c9a84c]/40 px-3 py-1.5 text-xs uppercase tracking-wider text-[#c9a84c] transition hover:bg-[#c9a84c] hover:text-[#1a2744] sm:inline-block"
                        >
                            Admin
                        </Link>
                        <Link
                            :href="dashboard().url"
                            class="flex items-center gap-2 rounded-md bg-white/10 px-3 py-1.5 text-sm transition hover:bg-white/20"
                        >
                            <User class="size-4" />
                            <span class="hidden sm:inline">{{ user.name.split(' ')[0] }}</span>
                        </Link>
                        <Link
                            :href="logout().url"
                            method="post"
                            as="button"
                            class="text-xs text-white/60 transition hover:text-white"
                        >
                            Sign out
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="login().url"
                            class="hidden text-sm text-white/80 transition hover:text-white sm:inline-block"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="register().url"
                            class="rounded-md bg-[#c9a84c] px-4 py-1.5 text-sm font-medium text-[#1a2744] transition hover:bg-[#dab867]"
                        >
                            Reserve
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="mt-24 border-t border-slate-200 bg-[#1a2744] text-slate-300">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:px-6 md:grid-cols-4 lg:px-8">
                <div>
                    <div class="flex items-center gap-2">
                        <span
                            class="flex size-8 items-center justify-center rounded-md bg-[#c9a84c] text-[#1a2744]"
                        >
                            <Hotel class="size-4" />
                        </span>
                        <span class="font-serif text-base text-white">Aurelia Stay</span>
                    </div>
                    <p class="mt-3 text-sm text-slate-400">
                        Refined hospitality, curated experiences. Every stay, a memory worth keeping.
                    </p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                        Discover
                    </h4>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li><Link :href="roomsIndex().url" class="hover:text-white">Browse rooms</Link></li>
                        <li><a href="#testimonials" class="hover:text-white">Guest reviews</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                        Account
                    </h4>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li v-if="!user"><Link :href="login().url" class="hover:text-white">Sign in</Link></li>
                        <li v-if="!user"><Link :href="register().url" class="hover:text-white">Create account</Link></li>
                        <li v-else><Link :href="dashboard().url" class="hover:text-white">My dashboard</Link></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                        Contact
                    </h4>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li>concierge@aurelia.example</li>
                        <li>+998 71 200 0000</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/5 py-4 text-center text-xs text-slate-500">
                © {{ new Date().getFullYear() }} Aurelia Stay. All rights reserved.
            </div>
        </footer>
    </div>
</template>
