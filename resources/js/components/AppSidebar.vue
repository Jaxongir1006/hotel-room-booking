<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    CalendarCheck,
    Hotel,
    LayoutGrid,
    ShieldCheck,
    Sparkles,
    Star,
    Users,
} from 'lucide-vue-next';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { dashboard, home } from '@/routes';
import { index as bookingsIndex } from '@/actions/App/Http/Controllers/BookingController';
import { index as roomsIndex } from '@/routes/rooms';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import { index as adminRooms } from '@/actions/App/Http/Controllers/Admin/RoomController';
import { index as adminBookings } from '@/actions/App/Http/Controllers/Admin/BookingController';
import { index as adminUsers } from '@/actions/App/Http/Controllers/Admin/UserController';
import { index as adminReviews } from '@/actions/App/Http/Controllers/Admin/ReviewController';
import type { NavItem } from '@/types';

const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin');
const { isCurrentUrl } = useCurrentUrl();

const guestNav: NavItem[] = [
    { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
    { title: 'My bookings', href: bookingsIndex(), icon: CalendarCheck },
    { title: 'Browse rooms', href: roomsIndex(), icon: Hotel },
];

const adminNav: NavItem[] = [
    { title: 'Overview', href: adminDashboard(), icon: ShieldCheck },
    { title: 'Rooms', href: adminRooms(), icon: Hotel },
    { title: 'Bookings', href: adminBookings(), icon: CalendarCheck },
    { title: 'Users', href: adminUsers(), icon: Users },
    { title: 'Reviews', href: adminReviews(), icon: Star },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child class="cursor-pointer">
                        <Link :href="home().url" class="group">
                            <div
                                class="flex aspect-square size-9 items-center justify-center rounded-md bg-[#c9a84c] text-[#1a2744] shadow-sm transition-transform duration-200 group-hover:rotate-3"
                            >
                                <Hotel class="size-5" />
                            </div>
                            <div class="ml-1 grid flex-1 text-left text-sm leading-tight">
                                <span
                                    class="truncate font-serif text-base text-sidebar-foreground"
                                >
                                    Aurelia Stay
                                </span>
                                <span class="truncate text-[10px] uppercase tracking-[0.18em] text-[#c9a84c]">
                                    Concierge
                                </span>
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel
                    class="text-[10px] uppercase tracking-[0.2em] text-sidebar-foreground/50"
                >
                    Stays
                </SidebarGroupLabel>
                <SidebarGroupContent>
                    <SidebarMenu>
                        <SidebarMenuItem
                            v-for="item in guestNav"
                            :key="item.title"
                        >
                            <SidebarMenuButton
                                as-child
                                :is-active="isCurrentUrl(item.href)"
                                :tooltip="item.title"
                                class="cursor-pointer transition-colors duration-200"
                            >
                                <Link :href="item.href">
                                    <component :is="item.icon" />
                                    <span>{{ item.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroupContent>
            </SidebarGroup>

            <SidebarGroup v-if="isAdmin" class="px-2 py-0">
                <SidebarGroupLabel
                    class="flex items-center gap-1.5 text-[10px] uppercase tracking-[0.2em] text-[#c9a84c]"
                >
                    <Sparkles class="size-3" />
                    Administration
                </SidebarGroupLabel>
                <SidebarGroupContent>
                    <SidebarMenu>
                        <SidebarMenuItem
                            v-for="item in adminNav"
                            :key="item.title"
                        >
                            <SidebarMenuButton
                                as-child
                                :is-active="isCurrentUrl(item.href)"
                                :tooltip="item.title"
                                class="cursor-pointer transition-colors duration-200"
                            >
                                <Link :href="item.href">
                                    <component :is="item.icon" />
                                    <span>{{ item.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroupContent>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
