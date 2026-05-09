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
    <div class="flex min-h-screen flex-col bg-[#fdfbf6] text-slate-900 antialiased">
        <header
            class="sticky top-0 z-40 border-b border-white/10 bg-[#1a2744]/95 text-white backdrop-blur supports-[backdrop-filter]:bg-[#1a2744]/80"
        >
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <Link :href="home().url" class="group flex cursor-pointer items-center gap-2">
                    <span
                        class="flex size-9 items-center justify-center rounded-md bg-[#c9a84c] text-[#1a2744] transition-transform duration-300 group-hover:rotate-6"
                    >
                        <Hotel class="size-5" />
                    </span>
                    <span class="font-serif text-lg tracking-wide">Aurelia Stay</span>
                </Link>

                <nav class="hidden items-center gap-8 text-sm font-medium md:flex">
                    <Link
                        :href="home().url"
                        class="group relative cursor-pointer py-1 transition-colors duration-200 hover:text-[#c9a84c]"
                    >
                        Home
                        <span class="absolute -bottom-0.5 left-0 h-px w-0 bg-[#c9a84c] transition-all duration-300 group-hover:w-full" />
                    </Link>
                    <Link
                        :href="roomsIndex().url"
                        class="group relative cursor-pointer py-1 transition-colors duration-200 hover:text-[#c9a84c]"
                    >
                        Rooms
                        <span class="absolute -bottom-0.5 left-0 h-px w-0 bg-[#c9a84c] transition-all duration-300 group-hover:w-full" />
                    </Link>
                    <a
                        href="#testimonials"
                        class="group relative cursor-pointer py-1 transition-colors duration-200 hover:text-[#c9a84c]"
                    >
                        Reviews
                        <span class="absolute -bottom-0.5 left-0 h-px w-0 bg-[#c9a84c] transition-all duration-300 group-hover:w-full" />
                    </a>
                </nav>

                <div class="flex items-center gap-3">
                    <template v-if="user">
                        <Link
                            v-if="isAdmin"
                            :href="dashboard().url"
                            class="hidden cursor-pointer rounded-md border border-[#c9a84c]/40 px-3 py-1.5 text-xs uppercase tracking-wider text-[#c9a84c] transition-colors duration-200 hover:bg-[#c9a84c] hover:text-[#1a2744] sm:inline-block"
                        >
                            Admin
                        </Link>
                        <Link
                            :href="dashboard().url"
                            class="flex cursor-pointer items-center gap-2 rounded-md bg-white/10 px-3 py-1.5 text-sm transition-colors duration-200 hover:bg-white/20"
                        >
                            <User class="size-4" />
                            <span class="hidden sm:inline">{{ user.name.split(' ')[0] }}</span>
                        </Link>
                        <Link
                            :href="logout().url"
                            method="post"
                            as="button"
                            class="cursor-pointer text-xs text-white/60 transition-colors duration-200 hover:text-white"
                        >
                            Sign out
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="login().url"
                            class="hidden cursor-pointer text-sm text-white/80 transition-colors duration-200 hover:text-white sm:inline-block"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="register().url"
                            class="cursor-pointer rounded-md bg-[#c9a84c] px-4 py-1.5 text-sm font-medium text-[#1a2744] shadow-md shadow-[#c9a84c]/20 transition-colors duration-200 hover:bg-[#dab867]"
                        >
                            Reserve
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <main :key="page.url" class="flex-1 animate-fade-in">
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
