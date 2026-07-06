<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    ShieldCheck,
    Sparkles,
    Star,
    Wifi,
    Play,
    ChevronRight,
    Award,
    Clock,
    MapPin,
} from 'lucide-vue-next';
import { onMounted, onUnmounted, ref, computed } from 'vue';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { index as roomsIndex, show as roomsShow } from '@/routes/rooms';
import type { RoomSummary } from '@/types';

// Static GSAP imports to avoid chunk-loading errors
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

const props = defineProps<{
    canRegister: boolean;
    featuredRooms: { data: RoomSummary[] };
}>();

// ── Showcase Rooms Data (Best Rooms) ───────────────────────────
const bestRooms = computed(() => {
    const rooms = props.featuredRooms?.data || [];
    if (rooms.length > 0) {
        return rooms.slice(0, 3);
    }
    return [
        {
            id: 0,
            name: 'The Royal Penthouse',
            slug: 'royal-penthouse',
            thumbnail:
                'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800&q=80',
            price_per_night: 380,
            average_rating: 4.9,
        },
        {
            id: 1,
            name: 'The Presidential Suite',
            slug: 'presidential-suite',
            thumbnail:
                'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&q=80',
            price_per_night: 450,
            average_rating: 4.8,
        },
        {
            id: 2,
            name: 'The Panoramic Loft',
            slug: 'panoramic-loft',
            thumbnail:
                'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=800&q=80',
            price_per_night: 290,
            average_rating: 4.7,
        },
    ];
});

// ── Testimonials Dataset (Best Reviews Advertisement) ──────────
const testimonials = [
    {
        name: 'Sienna M.',
        role: 'Royal Penthouse Guest',
        rating: 5,
        text: 'The most thoughtful service I have experienced. Every detail had been considered before we even arrived.',
        stay: 'June 2026 · 5 Nights',
    },
    {
        name: 'Adrian K.',
        role: 'Presidential Suite Guest',
        rating: 5,
        text: 'A genuine sanctuary in the heart of the city. The suites are nothing short of cinematic.',
        stay: 'May 2026 · 3 Nights',
    },
    {
        name: 'Priya R.',
        role: 'Panoramic Loft Guest',
        rating: 5,
        text: 'Refined elegance, attentive staff, and the bath products alone are worth the booking.',
        stay: 'April 2026 · 7 Nights',
    },
];

const activeReviewIndex = ref(0);
const activeReview = computed(() => testimonials[activeReviewIndex.value]);
const reviewProgress = ref(0);

let reviewAutoplayTimer: any = null;
let reviewProgressTimer: any = null;

const startReviewAutoplay = () => {
    stopReviewAutoplay();
    reviewProgress.value = 0;
    reviewProgressTimer = setInterval(() => {
        reviewProgress.value += 2;
        if (reviewProgress.value >= 100) {
            reviewProgress.value = 0;
            activeReviewIndex.value =
                (activeReviewIndex.value + 1) % testimonials.length;
        }
    }, 100);
};

const stopReviewAutoplay = () => {
    if (reviewAutoplayTimer) {
        clearInterval(reviewAutoplayTimer);
        reviewAutoplayTimer = null;
    }
    if (reviewProgressTimer) {
        clearInterval(reviewProgressTimer);
        reviewProgressTimer = null;
    }
};

const selectReview = (idx: number) => {
    activeReviewIndex.value = idx;
    reviewProgress.value = 0;
    startReviewAutoplay();
};

// ── Stats for social proof ──────────────────────────────────────
const stats = [
    { value: '4.9', label: 'Guest Rating', icon: Star },
    { value: '24/7', label: 'Concierge', icon: Clock },
    { value: '20+', label: 'Unique Suites', icon: Award },
    { value: 'Prime', label: 'Location', icon: MapPin },
];

// ── GSAP ScrollTrigger refs ────────────────────────────────────
const heroSectionRef = ref<HTMLElement | null>(null);
const heroTextRef = ref<HTMLElement | null>(null);
const cardRefs = ref<HTMLElement[]>([]);

const setCardRef = (el: any, index: number) => {
    if (el) {
        cardRefs.value[index] = el;
    }
};

let scrollCtx: any = null;

onMounted(() => {
    gsap.registerPlugin(ScrollTrigger);
    startReviewAutoplay();

    // Skip animations for reduced-motion users
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    scrollCtx = gsap.context(() => {
        // Pin the hero section during scroll animation (Apple-style)
        const tl = gsap.timeline({
            scrollTrigger: {
                trigger: heroSectionRef.value,
                start: 'top top',
                end: '+=3000',
                scrub: 1.0,
                pin: true,
                anticipatePin: 1,
            },
        });

        // Set initial state for all cards via GSAP
        cardRefs.value.forEach((card) => {
            if (card) {
                gsap.set(card, {
                    scale: 0.8,
                    opacity: 0,
                    rotateY: 0,
                    rotateX: 0,
                    y: 320,
                    transformPerspective: 1500,
                    pointerEvents: 'none',
                });
            }
        });

        // 1. Text elements fade out and scale up
        tl.to(
            heroTextRef.value,
            {
                scale: 1.15,
                opacity: 0,
                y: -60,
                ease: 'power1.out',
            },
            0,
        );

        // 2. Card 1 enters
        if (cardRefs.value[0]) {
            tl.to(
                cardRefs.value[0],
                {
                    scale: 1.08,
                    opacity: 1,
                    rotateY: 12,
                    rotateX: 4,
                    y: -20,
                    pointerEvents: 'auto',
                    ease: 'power2.out',
                },
                0.12,
            );

            tl.to(
                cardRefs.value[0],
                {
                    scale: 1.3,
                    opacity: 0,
                    rotateY: 25,
                    y: -100,
                    pointerEvents: 'none',
                    ease: 'power2.in',
                },
                0.45,
            );
        }

        // 4. Card 2 enters
        if (cardRefs.value[1]) {
            tl.to(
                cardRefs.value[1],
                {
                    scale: 1.08,
                    opacity: 1,
                    rotateY: -12,
                    rotateX: 4,
                    y: -20,
                    pointerEvents: 'auto',
                    ease: 'power2.out',
                },
                0.55,
            );

            tl.to(
                cardRefs.value[1],
                {
                    scale: 1.3,
                    opacity: 0,
                    rotateY: -25,
                    y: -100,
                    pointerEvents: 'none',
                    ease: 'power2.in',
                },
                0.8,
            );
        }

        // 6. Card 3 enters
        if (cardRefs.value[2]) {
            tl.to(
                cardRefs.value[2],
                {
                    scale: 1.08,
                    opacity: 1,
                    rotateY: 12,
                    rotateX: 4,
                    y: -20,
                    pointerEvents: 'auto',
                    ease: 'power2.out',
                },
                0.88,
            );
        }

        // Stats bar entrance
        gsap.fromTo(
            '.stat-item',
            { y: 30, opacity: 0 },
            {
                scrollTrigger: {
                    trigger: '.stats-bar',
                    start: 'top 90%',
                    once: true,
                },
                y: 0,
                opacity: 1,
                stagger: 0.1,
                duration: 0.6,
                ease: 'power2.out',
            },
        );

        // Feature Cards entrance
        gsap.fromTo(
            '.feature-card',
            { y: 50, opacity: 0 },
            {
                scrollTrigger: {
                    trigger: '.feature-cards-section',
                    start: 'top 85%',
                    once: true,
                },
                y: 0,
                opacity: 1,
                stagger: 0.15,
                duration: 0.8,
                ease: 'power2.out',
            },
        );

        // Experience section
        gsap.fromTo(
            '.experience-content',
            { y: 60, opacity: 0 },
            {
                scrollTrigger: {
                    trigger: '.experience-section',
                    start: 'top 80%',
                    once: true,
                },
                y: 0,
                opacity: 1,
                duration: 1,
                ease: 'power3.out',
            },
        );

        // Testimonial card entrance
        gsap.fromTo(
            '.testimonial-card',
            { y: 40, opacity: 0 },
            {
                scrollTrigger: {
                    trigger: '.testimonials-section',
                    start: 'top 80%',
                    once: true,
                },
                y: 0,
                opacity: 1,
                duration: 1.2,
                ease: 'power3.out',
            },
        );

        // CTA banner entrance
        gsap.fromTo(
            '.cta-banner',
            { y: 50, opacity: 0, scale: 0.97 },
            {
                scrollTrigger: {
                    trigger: '.cta-section',
                    start: 'top 85%',
                    once: true,
                },
                y: 0,
                opacity: 1,
                scale: 1,
                duration: 1,
                ease: 'power3.out',
            },
        );
    });
});

onUnmounted(() => {
    if (scrollCtx) {
        scrollCtx.revert();
        scrollCtx = null;
    }
    stopReviewAutoplay();
});
</script>

<template>
    <Head title="Aurelia Stay — Refined hospitality" />

    <GuestLayout>
        <!-- Luxury Architectural Parent Container -->
        <div
            class="welcome-wrapper bg-page relative w-full overflow-hidden text-white"
        >
            <!-- ═══ FULL-PAGE AMBIENT BACKGROUND LAYER ═══ -->
            <div class="pointer-events-none absolute inset-0 z-0">
                <!-- Animated gradient mesh base -->
                <div class="bg-mesh absolute inset-0" />

                <!-- Dot grid pattern for architectural precision -->
                <div class="bg-dot-grid absolute inset-0 opacity-[0.35]" />

                <!-- Large floating ambient orbs throughout the page -->
                <div
                    class="animate-drift-1 absolute top-[5%] left-[10%] size-[700px] rounded-full bg-gradient-to-br from-[#c9a84c]/[0.04] to-transparent blur-[140px]"
                />
                <div
                    class="animate-drift-2 absolute top-[15%] right-[5%] size-[500px] rounded-full bg-gradient-to-bl from-[#1a2744]/25 to-transparent blur-[120px]"
                />
                <div
                    class="animate-pulse-slow absolute top-[35%] left-[50%] size-[900px] -translate-x-1/2 rounded-full bg-[#c9a84c]/[0.015] blur-[160px]"
                />
                <div
                    class="animate-drift-3 absolute top-[55%] right-[15%] size-[600px] rounded-full bg-gradient-to-tl from-[#0d1f3c]/40 to-transparent blur-[130px]"
                />
                <div
                    class="animate-drift-1 absolute top-[70%] left-[5%] size-[500px] rounded-full bg-gradient-to-tr from-[#c9a84c]/[0.03] to-transparent blur-[120px]"
                    style="animation-delay: -8s"
                />
                <div
                    class="animate-drift-2 absolute top-[85%] right-[30%] size-[700px] rounded-full bg-gradient-to-bl from-[#1a2744]/20 to-transparent blur-[140px]"
                    style="animation-delay: -12s"
                />
            </div>

            <!-- Film-grain texture overlay for premium depth -->
            <div
                class="bg-noise pointer-events-none fixed inset-0 z-[100] opacity-[0.02] mix-blend-overlay"
            />

            <!-- ═══════════════════════════════════════════════ -->
            <!-- HERO SECTION: Apple-Style Scroll-Driven        -->
            <!-- ═══════════════════════════════════════════════ -->
            <section
                ref="heroSectionRef"
                class="hero-section relative flex h-screen w-full items-center justify-center overflow-hidden bg-transparent text-white"
            >
                <!-- Hero-specific concentrated glow -->
                <div
                    class="pointer-events-none absolute inset-0 z-0 overflow-hidden"
                >
                    <div
                        class="animate-pulse-slow absolute top-1/2 left-1/2 size-[600px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-radial-[at_center] from-[#c9a84c]/[0.06] to-transparent blur-[80px]"
                    />
                </div>

                <!-- Main wrapper -->
                <div
                    class="relative z-10 mx-auto flex h-full w-full max-w-7xl items-center justify-center px-4 sm:px-6 lg:px-8"
                >
                    <!-- Floating Room Cards (stacked, animated sequentially on scroll) -->
                    <div
                        v-for="(room, idx) in bestRooms"
                        :key="room.id"
                        :ref="(el) => setCardRef(el, idx)"
                        class="pointer-events-none absolute z-0 flex items-center justify-center"
                        style="will-change: transform, opacity"
                    >
                        <div
                            class="room-showcase-card w-[340px] rounded-3xl border border-white/[0.08] bg-white/[0.03] p-5 shadow-[0_50px_100px_-20px_rgba(0,0,0,0.8)] backdrop-blur-2xl transition-all duration-500 hover:border-[#c9a84c]/30 hover:shadow-[0_60px_120px_-20px_rgba(201,168,76,0.15)] sm:w-[400px]"
                        >
                            <!-- Image Container -->
                            <div
                                class="relative aspect-[4/5] overflow-hidden rounded-2xl bg-[#0b0f19]"
                            >
                                <img
                                    :src="room.thumbnail || undefined"
                                    :alt="room.name"
                                    class="h-full w-full object-cover opacity-90 transition-transform duration-700 hover:scale-105"
                                    loading="lazy"
                                />
                                <div
                                    class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/95 via-black/30 to-transparent"
                                />

                                <!-- Badges -->
                                <div
                                    class="pointer-events-none absolute inset-x-0 bottom-0 p-6"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <span
                                            class="rounded-full border border-[#c9a84c]/30 bg-[#0b0f19]/85 px-3 py-1 text-[10px] font-bold tracking-widest text-[#c9a84c] uppercase backdrop-blur-md"
                                        >
                                            Featured Suite
                                        </span>
                                        <div
                                            class="flex items-center gap-1 text-sm text-amber-400"
                                        >
                                            <Star
                                                class="size-4 fill-amber-400 text-amber-400"
                                            />
                                            <span class="font-bold text-white">
                                                {{
                                                    room.average_rating || '4.9'
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                    <h3
                                        class="mt-3 font-serif text-2xl tracking-wide text-white"
                                    >
                                        {{ room.name }}
                                    </h3>
                                </div>
                            </div>

                            <!-- Details & Actions -->
                            <div
                                class="mt-5 flex items-center justify-between px-1"
                            >
                                <div>
                                    <p
                                        class="text-[10px] tracking-widest text-slate-400 uppercase"
                                    >
                                        Starting from
                                    </p>
                                    <p
                                        class="mt-0.5 font-serif text-xl text-[#c9a84c]"
                                    >
                                        ${{ Math.round(room.price_per_night) }}
                                        <span class="text-xs text-slate-400"
                                            >/ night</span
                                        >
                                    </p>
                                </div>
                                <Link
                                    :href="roomsShow(room.slug).url"
                                    class="pointer-events-auto rounded-full border border-white/10 bg-white/5 px-4.5 py-2 text-xs font-semibold tracking-wider text-white uppercase transition-all duration-300 hover:border-[#c9a84c] hover:bg-[#c9a84c] hover:text-[#0b0f19]"
                                >
                                    View Details
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Typography: Centered on top of the card -->
                    <div
                        ref="heroTextRef"
                        class="pointer-events-none relative z-10 flex flex-col items-center text-center"
                        style="will-change: transform, opacity"
                    >
                        <p
                            class="animate-fade-up text-xs font-semibold tracking-[0.4em] text-[#c9a84c] uppercase"
                        >
                            Aurelia Stay
                        </p>
                        <h1
                            class="animate-fade-up-delayed-1 mt-5 max-w-4xl font-serif text-5xl leading-[1.15] text-white sm:text-6xl md:text-7xl"
                        >
                            A retreat where every detail is
                            <span class="font-normal text-[#c9a84c] italic"
                                >curated</span
                            >
                            for you.
                        </h1>
                        <p
                            class="animate-fade-up-delayed-2 mt-6 max-w-xl text-base leading-relaxed text-slate-300"
                        >
                            Discover thoughtfully designed suites, anticipatory
                            service, and the kind of quiet luxury that lingers
                            long after check-out.
                        </p>
                        <div
                            class="animate-fade-up-delayed-3 pointer-events-auto mt-10 flex flex-wrap items-center justify-center gap-6"
                        >
                            <Link
                                :href="roomsIndex().url"
                                class="group inline-flex cursor-pointer items-center gap-2 rounded-md bg-[#c9a84c] px-6 py-3.5 text-sm font-semibold text-[#0b0f19] shadow-lg shadow-[#c9a84c]/20 transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#dab867] hover:shadow-xl hover:shadow-[#c9a84c]/30"
                            >
                                Browse rooms
                                <ArrowRight
                                    class="size-4 transition-transform duration-200 group-hover:translate-x-0.5"
                                />
                            </Link>
                            <a
                                href="#testimonials"
                                class="flex cursor-pointer items-center gap-2 text-sm tracking-widest text-slate-300 uppercase transition-colors duration-200 hover:text-[#c9a84c]"
                            >
                                Discover the experience
                                <span class="animate-bounce">↓</span>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════ -->
            <!-- STATS BAR: Social Proof Strip                   -->
            <!-- ═══════════════════════════════════════════════ -->
            <section
                class="stats-bar relative z-10 border-y border-white/[0.06] bg-white/[0.02] backdrop-blur-md"
            >
                <!-- Subtle top edge glow on stats bar -->
                <div
                    class="pointer-events-none absolute -top-px left-1/2 h-px w-3/4 -translate-x-1/2 bg-gradient-to-r from-transparent via-[#c9a84c]/20 to-transparent"
                />
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-2 md:grid-cols-4">
                        <div
                            v-for="(stat, idx) in stats"
                            :key="stat.label"
                            class="stat-item flex items-center gap-4 px-6 py-8"
                            :class="
                                idx > 0 ? 'border-l border-white/[0.06]' : ''
                            "
                        >
                            <div
                                class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-[#c9a84c]/10 text-[#c9a84c]"
                            >
                                <component :is="stat.icon" class="size-5" />
                            </div>
                            <div>
                                <p
                                    class="text-xl leading-none font-bold text-white"
                                >
                                    {{ stat.value }}
                                </p>
                                <p
                                    class="mt-1 text-[11px] tracking-wider text-slate-400 uppercase"
                                >
                                    {{ stat.label }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════ -->
            <!-- FEATURE CARDS SECTION                           -->
            <!-- ═══════════════════════════════════════════════ -->
            <section
                class="feature-cards-section bg-transparent py-28 text-white"
            >
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-12 text-center">
                        <p
                            class="text-xs font-semibold tracking-[0.3em] text-[#c9a84c] uppercase"
                        >
                            Why Aurelia
                        </p>
                        <h2
                            class="mt-3 font-serif text-3xl text-white md:text-4xl"
                        >
                            Designed around your comfort
                        </h2>
                    </div>
                    <div class="grid gap-8 md:grid-cols-3">
                        <div
                            class="feature-card group cursor-default rounded-2xl border border-white/[0.06] bg-white/[0.02] p-8 shadow-sm backdrop-blur-md transition-all duration-500 hover:-translate-y-2 hover:border-[#c9a84c]/40 hover:bg-white/[0.04] hover:shadow-[0_25px_60px_-15px_rgba(201,168,76,0.1)]"
                        >
                            <div
                                class="flex size-14 items-center justify-center rounded-2xl bg-[#c9a84c] text-[#0b0f19] shadow-lg shadow-[#c9a84c]/10 transition-all duration-500 group-hover:scale-110 group-hover:rotate-6"
                            >
                                <Sparkles class="size-6" />
                            </div>
                            <h3
                                class="mt-6 font-serif text-xl font-semibold text-white"
                            >
                                Curated suites
                            </h3>
                            <p
                                class="mt-3 text-sm leading-relaxed text-slate-400"
                            >
                                Twenty rooms across four collections, each with
                                its own unique layout, custom furnishings, and
                                artistic character.
                            </p>
                            <div
                                class="mt-6 flex items-center gap-1 text-xs text-[#c9a84c] opacity-0 transition-all duration-300 group-hover:opacity-100"
                            >
                                <span>Explore collections</span>
                                <ChevronRight class="size-3" />
                            </div>
                        </div>

                        <div
                            class="feature-card group cursor-default rounded-2xl border border-white/[0.06] bg-white/[0.02] p-8 shadow-sm backdrop-blur-md transition-all duration-500 hover:-translate-y-2 hover:border-[#c9a84c]/40 hover:bg-white/[0.04] hover:shadow-[0_25px_60px_-15px_rgba(201,168,76,0.1)]"
                        >
                            <div
                                class="flex size-14 items-center justify-center rounded-2xl bg-[#c9a84c] text-[#0b0f19] shadow-lg shadow-[#c9a84c]/10 transition-all duration-500 group-hover:scale-110 group-hover:rotate-6"
                            >
                                <ShieldCheck class="size-6" />
                            </div>
                            <h3
                                class="mt-6 font-serif text-xl font-semibold text-white"
                            >
                                Confident booking
                            </h3>
                            <p
                                class="mt-3 text-sm leading-relaxed text-slate-400"
                            >
                                Free cancellation on pending reservations.
                                Secure payments, direct communication, and no
                                hidden booking charges.
                            </p>
                            <div
                                class="mt-6 flex items-center gap-1 text-xs text-[#c9a84c] opacity-0 transition-all duration-300 group-hover:opacity-100"
                            >
                                <span>Learn more</span>
                                <ChevronRight class="size-3" />
                            </div>
                        </div>

                        <div
                            class="feature-card group cursor-default rounded-2xl border border-white/[0.06] bg-white/[0.02] p-8 shadow-sm backdrop-blur-md transition-all duration-500 hover:-translate-y-2 hover:border-[#c9a84c]/40 hover:bg-white/[0.04] hover:shadow-[0_25px_60px_-15px_rgba(201,168,76,0.1)]"
                        >
                            <div
                                class="flex size-14 items-center justify-center rounded-2xl bg-[#c9a84c] text-[#0b0f19] shadow-lg shadow-[#c9a84c]/10 transition-all duration-500 group-hover:scale-110 group-hover:rotate-6"
                            >
                                <Wifi class="size-6" />
                            </div>
                            <h3
                                class="mt-6 font-serif text-xl font-semibold text-white"
                            >
                                All amenities included
                            </h3>
                            <p
                                class="mt-3 text-sm leading-relaxed text-slate-400"
                            >
                                Premium high-speed Wi-Fi, organic breakfast
                                buffet, and dedicated 24/7 concierge service
                                included with every stay.
                            </p>
                            <div
                                class="mt-6 flex items-center gap-1 text-xs text-[#c9a84c] opacity-0 transition-all duration-300 group-hover:opacity-100"
                            >
                                <span>View amenities</span>
                                <ChevronRight class="size-3" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════ -->
            <!-- SIGNATURE EXPERIENCE: Cinematic Image Banner    -->
            <!-- ═══════════════════════════════════════════════ -->
            <section class="experience-section relative overflow-hidden">
                <!-- Full-width cinematic image -->
                <div class="relative h-[70vh] min-h-[500px]">
                    <img
                        src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=1600&q=80"
                        alt="Aurelia Stay luxury hotel lobby"
                        class="absolute inset-0 h-full w-full object-cover"
                        loading="lazy"
                    />
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-[#05070c]/95 via-[#05070c]/60 to-transparent"
                    />
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-[#05070c] via-transparent to-[#05070c]/40"
                    />

                    <!-- Content overlay -->
                    <div
                        class="experience-content relative z-10 flex h-full items-center"
                    >
                        <div
                            class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8"
                        >
                            <div class="max-w-lg">
                                <p
                                    class="text-xs font-semibold tracking-[0.3em] text-[#c9a84c] uppercase"
                                >
                                    The Signature Experience
                                </p>
                                <h2
                                    class="mt-4 font-serif text-4xl leading-tight text-white md:text-5xl"
                                >
                                    Where architecture meets
                                    <span class="text-[#c9a84c] italic"
                                        >serenity</span
                                    >
                                </h2>
                                <p
                                    class="mt-6 max-w-md text-base leading-relaxed text-slate-300"
                                >
                                    Each floor tells a different story. From the
                                    marble-clad lobby to the rooftop terrace,
                                    every space has been composed to calm the
                                    senses and elevate the spirit.
                                </p>
                                <div class="mt-8 flex items-center gap-4">
                                    <Link
                                        :href="roomsIndex().url"
                                        class="group inline-flex items-center gap-2 rounded-md bg-[#c9a84c] px-5 py-3 text-sm font-semibold text-[#0b0f19] transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#dab867] hover:shadow-lg hover:shadow-[#c9a84c]/20"
                                    >
                                        Explore Suites
                                        <ArrowRight
                                            class="size-4 transition-transform duration-200 group-hover:translate-x-0.5"
                                        />
                                    </Link>
                                    <button
                                        class="flex cursor-pointer items-center gap-2 rounded-full border border-white/20 bg-white/5 px-5 py-3 text-sm text-white backdrop-blur transition-all duration-300 hover:border-white/40 hover:bg-white/10"
                                    >
                                        <Play class="size-4 fill-white" />
                                        Watch tour
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════ -->
            <!-- TESTIMONIALS: Best Reviews Carousel             -->
            <!-- ═══════════════════════════════════════════════ -->
            <section
                id="testimonials"
                class="testimonials-section relative overflow-hidden bg-transparent py-32 text-white"
            >
                <!-- Ambient glow behind testimonials -->
                <div
                    class="pointer-events-none absolute top-1/2 left-1/2 size-[700px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#c9a84c]/[0.03] blur-[160px]"
                />
                <div
                    class="pointer-events-none absolute top-0 left-0 h-px w-full bg-gradient-to-r from-transparent via-white/[0.06] to-transparent"
                />

                <div class="relative z-10 mx-auto max-w-4xl px-4 text-center">
                    <p
                        class="text-xs font-semibold tracking-[0.3em] text-[#c9a84c] uppercase"
                    >
                        Guest Endorsements
                    </p>
                    <h2 class="mt-2 font-serif text-3xl text-white md:text-4xl">
                        Words from those who have stayed
                    </h2>

                    <div
                        class="mt-16 flex justify-center"
                        @mouseenter="stopReviewAutoplay"
                        @mouseleave="startReviewAutoplay"
                    >
                        <div class="relative min-h-[360px] w-full max-w-2xl">
                            <Transition name="review-slide" mode="out-in">
                                <div
                                    :key="activeReviewIndex"
                                    class="testimonial-card relative overflow-hidden rounded-3xl border border-white/[0.08] bg-white/[0.02] p-8 text-center shadow-[0_50px_100px_-20px_rgba(0,0,0,0.8)] backdrop-blur-2xl md:p-12"
                                >
                                    <!-- Quote Mark overlay -->
                                    <div
                                        class="pointer-events-none absolute top-4 right-6 font-serif text-[120px] leading-none text-white/[0.03] select-none"
                                    >
                                        &ldquo;
                                    </div>

                                    <!-- Subtle gold edge glow -->
                                    <div
                                        class="pointer-events-none absolute -top-px left-1/2 h-px w-1/2 -translate-x-1/2 bg-gradient-to-r from-transparent via-[#c9a84c]/40 to-transparent"
                                    />

                                    <div
                                        class="flex justify-center gap-1 text-[#c9a84c]"
                                    >
                                        <Star
                                            v-for="i in 5"
                                            :key="i"
                                            class="size-5 fill-[#c9a84c] text-[#c9a84c]"
                                        />
                                    </div>

                                    <blockquote
                                        class="mt-8 font-serif text-2xl leading-relaxed text-slate-200 italic md:text-3xl"
                                    >
                                        "{{ activeReview.text }}"
                                    </blockquote>

                                    <hr
                                        class="mx-auto my-8 max-w-xs border-white/[0.05]"
                                    />

                                    <!-- Reviewer info with avatar initial -->
                                    <div
                                        class="flex items-center justify-center gap-4"
                                    >
                                        <div
                                            class="flex size-12 items-center justify-center rounded-full border border-[#c9a84c]/20 bg-[#c9a84c]/10 font-serif text-lg font-bold text-[#c9a84c]"
                                        >
                                            {{ activeReview.name.charAt(0) }}
                                        </div>
                                        <cite
                                            class="block text-left not-italic"
                                        >
                                            <span
                                                class="block text-sm font-semibold tracking-wider text-white uppercase"
                                            >
                                                {{ activeReview.name }}
                                            </span>
                                            <span
                                                class="mt-0.5 block text-xs tracking-wider text-slate-400 uppercase"
                                            >
                                                {{ activeReview.role }}
                                            </span>
                                            <span
                                                class="mt-0.5 block text-[10px] tracking-wider text-slate-500"
                                            >
                                                {{ activeReview.stay }}
                                            </span>
                                        </cite>
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    </div>

                    <!-- Slider Navigation Dots with progress -->
                    <div class="mt-8 flex justify-center gap-3">
                        <button
                            v-for="(t, idx) in testimonials"
                            :key="t.name"
                            @click="selectReview(idx)"
                            class="relative h-1.5 cursor-pointer overflow-hidden rounded-full transition-all duration-500"
                            :class="
                                idx === activeReviewIndex
                                    ? 'w-10 bg-white/10'
                                    : 'w-6 bg-white/10 hover:bg-white/20'
                            "
                            :aria-label="`Go to slide ${idx + 1}`"
                        >
                            <div
                                v-if="idx === activeReviewIndex"
                                class="absolute inset-y-0 left-0 rounded-full bg-[#c9a84c] transition-all duration-100"
                                :style="{ width: `${reviewProgress}%` }"
                            />
                            <div
                                v-else
                                class="absolute inset-0 rounded-full bg-white/20"
                            />
                        </button>
                    </div>
                </div>
            </section>

            <!-- ═══════════════════════════════════════════════ -->
            <!-- CTA BANNER: Final call-to-action before footer  -->
            <!-- ═══════════════════════════════════════════════ -->
            <section class="cta-section relative pb-0">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div
                        class="cta-banner relative overflow-hidden rounded-3xl border border-white/[0.08] bg-gradient-to-br from-[#1a2744] via-[#0f1a2e] to-[#05070c] p-12 text-center md:p-16"
                    >
                        <!-- Decorative glow -->
                        <div
                            class="pointer-events-none absolute -top-20 left-1/2 size-[400px] -translate-x-1/2 rounded-full bg-[#c9a84c]/[0.06] blur-[100px]"
                        />
                        <div
                            class="pointer-events-none absolute -top-px left-1/2 h-px w-2/3 -translate-x-1/2 bg-gradient-to-r from-transparent via-[#c9a84c]/30 to-transparent"
                        />

                        <div class="relative z-10">
                            <p
                                class="text-xs font-semibold tracking-[0.3em] text-[#c9a84c] uppercase"
                            >
                                Begin Your Stay
                            </p>
                            <h2
                                class="mx-auto mt-4 max-w-2xl font-serif text-3xl leading-tight text-white md:text-5xl"
                            >
                                Your finest chapter starts at Aurelia
                            </h2>
                            <p
                                class="mx-auto mt-5 max-w-lg text-base text-slate-300"
                            >
                                Book your suite today and experience the
                                intersection of modern luxury and timeless
                                hospitality.
                            </p>
                            <div
                                class="mt-10 flex flex-wrap items-center justify-center gap-4"
                            >
                                <Link
                                    :href="roomsIndex().url"
                                    class="group inline-flex items-center gap-2 rounded-md bg-[#c9a84c] px-7 py-4 text-sm font-semibold text-[#0b0f19] shadow-lg shadow-[#c9a84c]/20 transition-all duration-300 hover:-translate-y-0.5 hover:bg-[#dab867] hover:shadow-xl hover:shadow-[#c9a84c]/30"
                                >
                                    Reserve a Suite
                                    <ArrowRight
                                        class="size-4 transition-transform duration-200 group-hover:translate-x-0.5"
                                    />
                                </Link>
                                <a
                                    href="mailto:concierge@aurelia.example"
                                    class="flex items-center gap-2 rounded-md border border-white/10 bg-white/5 px-7 py-4 text-sm font-medium text-white transition-all duration-300 hover:border-white/20 hover:bg-white/10"
                                >
                                    Contact Concierge
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </GuestLayout>
</template>

<style scoped>
.hero-section {
    min-height: 100vh;
}

/* ── Cross-fade transitions for slideshow ── */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.4s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* ── Cross-fade & slide transition for card details ── */
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
}
.fade-slide-enter-from {
    opacity: 0;
    transform: translateY(8px);
}
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}

/* ── Review Slide Transition ── */
.review-slide-enter-active,
.review-slide-leave-active {
    transition: all 0.5s cubic-bezier(0.25, 1, 0.5, 1);
}
.review-slide-enter-from {
    opacity: 0;
    transform: translateX(30px);
}
.review-slide-leave-to {
    opacity: 0;
    transform: translateX(-30px);
}

/* ── Page Background ── */
.bg-page {
    background: #030508;
}

/* Animated gradient mesh — dark with moving color pools */
.bg-mesh {
    background:
        radial-gradient(
            ellipse 80% 60% at 10% 20%,
            rgba(26, 39, 68, 0.35) 0%,
            transparent 60%
        ),
        radial-gradient(
            ellipse 70% 50% at 85% 30%,
            rgba(13, 31, 60, 0.25) 0%,
            transparent 55%
        ),
        radial-gradient(
            ellipse 60% 80% at 50% 80%,
            rgba(201, 168, 76, 0.03) 0%,
            transparent 50%
        ),
        radial-gradient(
            ellipse 90% 50% at 30% 60%,
            rgba(10, 18, 35, 0.4) 0%,
            transparent 60%
        ),
        linear-gradient(
            180deg,
            #030508 0%,
            #060a14 30%,
            #0a1020 60%,
            #0d152a 100%
        );
    animation: meshShift 30s ease-in-out infinite alternate;
}

@keyframes meshShift {
    0% {
        background-position:
            0% 0%,
            100% 0%,
            50% 100%,
            0% 50%,
            0% 0%;
    }
    50% {
        background-position:
            20% 10%,
            80% 20%,
            40% 90%,
            10% 40%,
            0% 0%;
    }
    100% {
        background-position:
            10% 5%,
            90% 10%,
            60% 95%,
            20% 60%,
            0% 0%;
    }
}

/* Dot grid pattern — subtle architectural precision */
.bg-dot-grid {
    background-image: radial-gradient(
        circle,
        rgba(255, 255, 255, 0.08) 1px,
        transparent 1px
    );
    background-size: 32px 32px;
}

/* Film-grain noise texture */
.bg-noise {
    background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22n%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.85%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23n)%22/%3E%3C/svg%3E');
    background-repeat: repeat;
    background-size: 200px 200px;
}

/* ── Premium Background Animations ── */
@keyframes drift1 {
    0% {
        transform: translate(0, 0) scale(1);
    }
    33% {
        transform: translate(50px, -40px) scale(1.08);
    }
    66% {
        transform: translate(-20px, 30px) scale(0.95);
    }
    100% {
        transform: translate(0, 0) scale(1);
    }
}
@keyframes drift2 {
    0% {
        transform: translate(0, 0) scale(1);
    }
    33% {
        transform: translate(-40px, 50px) scale(0.92);
    }
    66% {
        transform: translate(30px, -20px) scale(1.05);
    }
    100% {
        transform: translate(0, 0) scale(1);
    }
}
@keyframes drift3 {
    0% {
        transform: translate(0, 0) scale(1) rotate(0deg);
    }
    50% {
        transform: translate(60px, -50px) scale(1.1) rotate(3deg);
    }
    100% {
        transform: translate(0, 0) scale(1) rotate(0deg);
    }
}
@keyframes pulseSlow {
    0%,
    100% {
        opacity: 0.4;
        transform: scale(1);
    }
    50% {
        opacity: 0.9;
        transform: scale(1.05);
    }
}

.animate-drift-1 {
    animation: drift1 25s infinite ease-in-out;
}
.animate-drift-2 {
    animation: drift2 30s infinite ease-in-out;
}
.animate-drift-3 {
    animation: drift3 35s infinite ease-in-out;
}
.animate-pulse-slow {
    animation: pulseSlow 18s infinite ease-in-out;
}

/* ── Room card inner glow on hover ── */
.room-showcase-card::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 1.5rem;
    padding: 1px;
    background: linear-gradient(
        135deg,
        rgba(201, 168, 76, 0) 0%,
        rgba(201, 168, 76, 0.12) 50%,
        rgba(201, 168, 76, 0) 100%
    );
    mask:
        linear-gradient(#fff 0 0) content-box,
        linear-gradient(#fff 0 0);
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.5s;
    pointer-events: none;
}
.room-showcase-card:hover::before {
    opacity: 1;
}
</style>
