<script setup lang="ts" generic="T extends Record<string, unknown>">
type Column<Row> = {
    key: string;
    label: string;
    align?: 'left' | 'right' | 'center';
    width?: string;
    render?: (row: Row) => unknown;
};

defineProps<{
    columns: Column<T>[];
    rows: T[];
    emptyState?: string;
    rowKey?: (row: T) => string | number;
}>();
</script>

<template>
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50/60 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th
                            v-for="column in columns"
                            :key="column.key"
                            scope="col"
                            class="px-4 py-3 font-medium"
                            :class="{
                                'text-left': column.align !== 'right' && column.align !== 'center',
                                'text-right': column.align === 'right',
                                'text-center': column.align === 'center',
                            }"
                            :style="column.width ? { width: column.width } : undefined"
                        >
                            {{ column.label }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <tr v-if="!rows.length">
                        <td
                            :colspan="columns.length"
                            class="px-4 py-10 text-center text-sm text-slate-400"
                        >
                            {{ emptyState ?? 'No records to display.' }}
                        </td>
                    </tr>
                    <tr
                        v-for="(row, index) in rows"
                        :key="rowKey ? rowKey(row) : index"
                        class="transition hover:bg-slate-50/60"
                    >
                        <td
                            v-for="column in columns"
                            :key="column.key"
                            class="px-4 py-3 align-middle"
                            :class="{
                                'text-right': column.align === 'right',
                                'text-center': column.align === 'center',
                            }"
                        >
                            <slot
                                :name="`cell-${column.key}`"
                                :row="row"
                                :value="row[column.key]"
                            >
                                {{
                                    column.render
                                        ? column.render(row)
                                        : row[column.key]
                                }}
                            </slot>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
