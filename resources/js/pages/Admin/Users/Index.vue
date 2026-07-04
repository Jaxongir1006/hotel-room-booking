<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { debounce } from '@/lib/debounce';
import DataTable from '@/components/admin/DataTable.vue';
import {
    index as adminUsersIndex,
    update as adminUsersUpdate,
} from '@/actions/App/Http/Controllers/Admin/UserController';
import { index as adminDashboard } from '@/actions/App/Http/Controllers/Admin/DashboardController';
import type { FlatPaginated } from '@/types';

type Option = { value: string; label: string };

type AdminUserRow = {
    id: number;
    name: string;
    email: string;
    role: string;
    role_label: string;
    email_verified_at: string | null;
    bookings_count: number;
    reviews_count: number;
    created_at: string | null;
};

const props = defineProps<{
    users: FlatPaginated<AdminUserRow>;
    filters: { q: string | null; role: string | null };
    roles: Option[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: adminDashboard().url },
            { title: 'Users', href: adminUsersIndex().url },
        ],
    },
});

const page = usePage();
const currentUserId = page.props.auth?.user?.id;

const search = ref(props.filters.q ?? '');
const role = ref(props.filters.role ?? '');

const apply = debounce(() => {
    router.get(
        adminUsersIndex().url,
        {
            q: search.value || undefined,
            role: role.value || undefined,
        },
        { preserveState: true, preserveScroll: true, replace: true },
    );
}, 250);

watch([search, role], apply);

const updating = ref<number | null>(null);

const updateRole = (user: AdminUserRow, value: string) => {
    if (value === user.role) return;
    updating.value = user.id;
    router.patch(
        adminUsersUpdate({ user: user.id }).url,
        { role: value },
        {
            preserveScroll: true,
            preserveState: true,
            onFinish: () => (updating.value = null),
        },
    );
};

const formatDate = (iso: string | null) =>
    iso
        ? new Date(iso).toLocaleDateString('en-US', {
              month: 'short',
              day: 'numeric',
              year: 'numeric',
          })
        : '—';

const columns = [
    { key: 'user', label: 'User' },
    { key: 'role', label: 'Role' },
    { key: 'bookings_count', label: 'Bookings', align: 'center' as const },
    { key: 'reviews_count', label: 'Reviews', align: 'center' as const },
    { key: 'created_at', label: 'Joined' },
];
</script>

<template>
    <Head title="Admin · Users" />

    <div class="space-y-5 p-4 md:p-6">
        <header>
            <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                Members
            </p>
            <h1 class="mt-1 font-serif text-3xl text-[#1a2744]">Users</h1>
        </header>

        <div class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:grid-cols-[2fr_1fr]">
            <label class="relative">
                <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                <input
                    v-model="search"
                    type="search"
                    placeholder="Search by name or email…"
                    class="w-full rounded-md border border-slate-200 bg-white py-2 pl-9 pr-3 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
                />
            </label>
            <select
                v-model="role"
                class="rounded-md border border-slate-200 bg-white px-3 py-2 text-sm focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
            >
                <option value="">All roles</option>
                <option v-for="r in roles" :key="r.value" :value="r.value">
                    {{ r.label }}
                </option>
            </select>
        </div>

        <DataTable
            :columns="columns"
            :rows="users.data"
            :row-key="(row) => row.id"
            empty-state="No users found."
        >
            <template #cell-user="{ row }">
                <div>
                    <p class="font-medium text-slate-800">
                        {{ row.name }}
                        <span
                            v-if="row.id === currentUserId"
                            class="ml-1 text-xs text-[#c9a84c]"
                        >
                            (you)
                        </span>
                    </p>
                    <p class="text-xs text-slate-400">{{ row.email }}</p>
                </div>
            </template>
            <template #cell-role="{ row }">
                <select
                    class="rounded-md border border-slate-200 bg-white px-2 py-1 text-xs focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c] disabled:opacity-60"
                    :value="row.role"
                    :disabled="updating === row.id"
                    @change="updateRole(row, ($event.target as HTMLSelectElement).value)"
                >
                    <option v-for="r in roles" :key="r.value" :value="r.value">
                        {{ r.label }}
                    </option>
                </select>
            </template>
            <template #cell-created_at="{ row }">
                <span class="text-xs text-slate-500">{{ formatDate(row.created_at) }}</span>
            </template>
        </DataTable>

        <p class="text-xs text-slate-400">
            Showing {{ users.from ?? 0 }}–{{ users.to ?? 0 }} of
            {{ users.total }} users.
        </p>
    </div>
</template>
