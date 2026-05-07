<script setup lang="ts">
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { ChevronLeft, ChevronRight, X } from 'lucide-vue-next';

const props = defineProps<{
    images: string[];
    alt?: string;
}>();

const lightboxIndex = ref<number | null>(null);

const allImages = computed(() => props.images.filter(Boolean));

const open = (index: number) => {
    lightboxIndex.value = index;
};

const close = () => {
    lightboxIndex.value = null;
};

const next = () => {
    if (lightboxIndex.value === null) return;
    lightboxIndex.value = (lightboxIndex.value + 1) % allImages.value.length;
};

const prev = () => {
    if (lightboxIndex.value === null) return;
    lightboxIndex.value =
        (lightboxIndex.value - 1 + allImages.value.length) % allImages.value.length;
};

const handleKey = (e: KeyboardEvent) => {
    if (lightboxIndex.value === null) return;
    if (e.key === 'Escape') close();
    if (e.key === 'ArrowRight') next();
    if (e.key === 'ArrowLeft') prev();
};

watch(lightboxIndex, (idx) => {
    if (idx !== null) {
        document.addEventListener('keydown', handleKey);
        document.body.style.overflow = 'hidden';
    } else {
        document.removeEventListener('keydown', handleKey);
        document.body.style.overflow = '';
    }
});

onBeforeUnmount(() => {
    document.removeEventListener('keydown', handleKey);
    document.body.style.overflow = '';
});
</script>

<template>
    <div v-if="allImages.length" class="space-y-3">
        <button
            type="button"
            class="block w-full overflow-hidden rounded-xl bg-slate-100"
            @click="open(0)"
        >
            <img
                :src="allImages[0]"
                :alt="alt ?? 'Room image'"
                class="aspect-[16/10] w-full object-cover transition hover:scale-[1.01]"
            />
        </button>

        <div v-if="allImages.length > 1" class="grid grid-cols-3 gap-3">
            <button
                v-for="(src, i) in allImages.slice(1, 4)"
                :key="src + i"
                type="button"
                class="overflow-hidden rounded-lg bg-slate-100"
                @click="open(i + 1)"
            >
                <img
                    :src="src"
                    :alt="alt ?? 'Room image'"
                    class="aspect-square w-full object-cover transition hover:opacity-90"
                />
            </button>
        </div>

        <Teleport to="body">
            <div
                v-if="lightboxIndex !== null"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/85"
                @click.self="close"
            >
                <button
                    type="button"
                    class="absolute right-6 top-6 rounded-full bg-white/10 p-2 text-white transition hover:bg-white/20"
                    @click="close"
                >
                    <X class="size-5" />
                </button>
                <button
                    v-if="allImages.length > 1"
                    type="button"
                    class="absolute left-4 rounded-full bg-white/10 p-2 text-white transition hover:bg-white/20 sm:left-8"
                    @click="prev"
                >
                    <ChevronLeft class="size-6" />
                </button>
                <img
                    :src="allImages[lightboxIndex]"
                    :alt="alt ?? 'Room image'"
                    class="max-h-[85vh] max-w-[90vw] rounded-lg object-contain shadow-2xl"
                />
                <button
                    v-if="allImages.length > 1"
                    type="button"
                    class="absolute right-4 rounded-full bg-white/10 p-2 text-white transition hover:bg-white/20 sm:right-8"
                    @click="next"
                >
                    <ChevronRight class="size-6" />
                </button>
                <div
                    v-if="allImages.length > 1"
                    class="absolute bottom-6 text-sm tracking-widest text-white/70"
                >
                    {{ lightboxIndex + 1 }} / {{ allImages.length }}
                </div>
            </div>
        </Teleport>
    </div>
</template>
