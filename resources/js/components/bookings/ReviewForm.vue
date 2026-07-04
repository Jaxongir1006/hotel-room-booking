<script setup lang="ts">
import { computed, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Loader2, Star } from 'lucide-vue-next';
import { store as reviewStore } from '@/actions/App/Http/Controllers/ReviewController';

const props = defineProps<{ bookingReference: string }>();

const form = useForm<{ rating: number; comment: string }>({
    rating: 5,
    comment: '',
});

const hovered = ref<number | null>(null);

const activeRating = computed(() => hovered.value ?? form.rating);

const setRating = (value: number) => {
    form.rating = value;
};

const submit = () => {
    form.post(reviewStore({ booking: props.bookingReference }).url, {
        preserveScroll: true,
        onSuccess: () => form.reset('comment'),
    });
};
</script>

<template>
    <form
        class="rounded-xl border border-slate-200 bg-white p-6"
        @submit.prevent="submit"
    >
        <header>
            <p class="text-xs font-semibold uppercase tracking-widest text-[#c9a84c]">
                Share your experience
            </p>
            <h2 class="mt-1 font-serif text-xl text-[#1a2744]">
                How was your stay?
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Your feedback helps future guests choose with confidence.
            </p>
        </header>

        <div class="mt-5">
            <label class="text-xs font-medium uppercase tracking-wider text-slate-500">
                Rating
            </label>
            <div class="mt-2 flex items-center gap-1">
                <button
                    v-for="i in 5"
                    :key="i"
                    type="button"
                    class="rounded-md p-1 transition hover:bg-slate-50"
                    @mouseenter="hovered = i"
                    @mouseleave="hovered = null"
                    @click="setRating(i)"
                    :aria-label="`Rate ${i} of 5`"
                >
                    <Star
                        class="size-7 transition"
                        :class="
                            i <= activeRating
                                ? 'fill-[#c9a84c] text-[#c9a84c]'
                                : 'text-slate-200'
                        "
                    />
                </button>
                <span class="ml-3 text-sm text-slate-500">
                    {{ activeRating }} / 5
                </span>
            </div>
            <p
                v-if="form.errors.rating"
                class="mt-1.5 text-xs text-rose-600"
            >
                {{ form.errors.rating }}
            </p>
        </div>

        <div class="mt-5">
            <label
                for="review-comment"
                class="text-xs font-medium uppercase tracking-wider text-slate-500"
            >
                Comment <span class="text-slate-400 normal-case">(optional)</span>
            </label>
            <textarea
                id="review-comment"
                v-model="form.comment"
                rows="4"
                maxlength="1000"
                placeholder="Tell us about the room, the service, the little details that made the stay memorable…"
                class="mt-2 block w-full resize-none rounded-md border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm transition focus:border-[#c9a84c] focus:outline-none focus:ring-1 focus:ring-[#c9a84c]"
            />
            <div class="mt-1 flex justify-between text-xs text-slate-400">
                <span v-if="form.errors.comment" class="text-rose-600">
                    {{ form.errors.comment }}
                </span>
                <span v-else>{{ form.comment.length }} / 1000</span>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center gap-2 rounded-md bg-[#1a2744] px-5 py-2.5 text-sm font-medium text-white transition hover:bg-[#243558] disabled:cursor-not-allowed disabled:opacity-60"
            >
                <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                Submit review
            </button>
        </div>
    </form>
</template>
