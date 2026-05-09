<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Loader2 } from 'lucide-vue-next';

const props = withDefaults(
    defineProps<{
        open: boolean;
        title: string;
        description?: string;
        confirmLabel?: string;
        cancelLabel?: string;
        variant?: 'danger' | 'primary';
        processing?: boolean;
    }>(),
    {
        confirmLabel: 'Confirm',
        cancelLabel: 'Cancel',
        variant: 'primary',
        processing: false,
    },
);

const emit = defineEmits<{
    (event: 'update:open', value: boolean): void;
    (event: 'confirm'): void;
    (event: 'cancel'): void;
}>();

const handleClose = () => {
    if (props.processing) return;
    emit('update:open', false);
    emit('cancel');
};

const handleConfirm = () => {
    if (props.processing) return;
    emit('confirm');
};
</script>

<template>
    <Dialog :open="open" @update:open="(value) => emit('update:open', value)">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ title }}</DialogTitle>
                <DialogDescription v-if="description">
                    {{ description }}
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="mt-2 gap-2 sm:gap-2">
                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-md border border-slate-200 px-4 py-2 text-sm text-slate-600 transition hover:bg-slate-50"
                    :disabled="processing"
                    @click="handleClose"
                >
                    {{ cancelLabel }}
                </button>
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-md px-4 py-2 text-sm font-medium text-white transition disabled:cursor-not-allowed disabled:opacity-60"
                    :class="
                        variant === 'danger'
                            ? 'bg-rose-600 hover:bg-rose-700'
                            : 'bg-[#1a2744] hover:bg-[#243558]'
                    "
                    :disabled="processing"
                    @click="handleConfirm"
                >
                    <Loader2 v-if="processing" class="size-4 animate-spin" />
                    {{ confirmLabel }}
                </button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
