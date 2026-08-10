<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowRight,
    Briefcase,
    ChartPie,
    CheckCircle2,
    GraduationCap,
    LineChart,
    Quote,
    ShieldCheck,
    Signal,
    Sparkles,
    Star,
    TrendingUp,
    Users,
} from '@lucide/vue';
import type { Component } from 'vue';
import SecretMoney from '@/components/public/SecretMoney.vue';
import StatTile from '@/components/public/StatTile.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { formatNumber } from '@/lib/format';

interface MemberCard {
    name: string;
    slug: string;
    title: string | null;
    specialization: string | null;
    avatar_url: string | null;
    stats: { total_pnl: number; win_rate: number; total_trades: number };
}

defineProps<{
    stats: { total_pnl: number; total_trades: number; win_rate: number; members: number };
    members: MemberCard[];
    services: Array<{ id: number; title: string; description: string; icon: string; price_label: string | null; features: string[]; is_featured: boolean }>;
    testimonials: Array<{ id: number; author_name: string; author_role: string | null; quote: string; rating: number }>;
}>();

const iconMap: Record<string, Component> = {
    signal: Signal,
    'graduation-cap': GraduationCap,
    'chart-pie': ChartPie,
    briefcase: Briefcase,
    sparkles: Sparkles,
};
</script>

<template>
    <Head title="Konsultan Trading Profesional">
        <meta name="description" content="Lentera Project — konsultan trading yang menerangi jalan investasi Anda dengan analisis, disiplin, dan manajemen risiko." />
    </Head>

    <PublicLayout>
        <!-- HERO -->
        <section class="relative overflow-hidden">
            <div class="pointer-events-none absolute inset-0 -z-10">
                <div class="absolute -top-24 left-1/2 size-[520px] -translate-x-1/2 rounded-full bg-emerald-400/20 blur-3xl"></div>
                <div class="absolute right-0 top-40 size-72 rounded-full bg-green-500/10 blur-3xl"></div>
            </div>

            <div class="mx-auto max-w-6xl px-4 pb-16 pt-16 sm:px-6 sm:pt-24">
                <div class="mx-auto max-w-3xl text-center">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-300">
                        <Sparkles class="size-3.5" /> Konsultan Trading & Edukasi Pasar
                    </span>
                    <h1 class="mt-5 text-4xl font-extrabold leading-tight tracking-tight sm:text-6xl">
                        Menerangi jalan
                        <span class="bg-gradient-to-br from-emerald-500 to-green-700 bg-clip-text text-transparent">trading</span>
                        Anda
                    </h1>
                    <p class="mx-auto mt-5 max-w-2xl text-base text-muted-foreground sm:text-lg">
                        Lentera Project membantu Anda trading dengan lebih tenang dan terukur — lewat sinyal, mentoring,
                        dan analisis berbasis manajemen risiko. Transparan, dengan rekam jejak yang bisa Anda pantau.
                    </p>
                    <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                        <Link href="/schedule" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-br from-emerald-400 to-green-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition-transform hover:-translate-y-0.5">
                            Jadwalkan Konsultasi <ArrowRight class="size-4" />
                        </Link>
                        <Link href="/performance" class="inline-flex items-center gap-2 rounded-xl border border-border bg-card px-6 py-3 text-sm font-semibold transition-colors hover:bg-muted">
                            <LineChart class="size-4" /> Lihat Track Record
                        </Link>
                    </div>
                </div>

                <!-- Stat band -->
                <div class="mx-auto mt-14 grid max-w-4xl grid-cols-2 gap-4 lg:grid-cols-4">
                    <StatTile label="Total Realized PnL" value="" accent="up" sub="Akumulasi seluruh tim">
                        <template #value><SecretMoney :value="stats.total_pnl" with-sign :colored="false" /></template>
                    </StatTile>
                    <StatTile label="Win Rate" :value="`${stats.win_rate}%`" sub="Rasio trade profit" />
                    <StatTile label="Total Trade" :value="formatNumber(stats.total_trades)" sub="Transaksi tercatat" />
                    <StatTile label="Konsultan" :value="formatNumber(stats.members)" sub="Trader berpengalaman" />
                </div>
            </div>
        </section>

        <!-- VALUE PROPS -->
        <section class="border-y border-border/60 bg-muted/30">
            <div class="mx-auto grid max-w-6xl gap-6 px-4 py-14 sm:px-6 md:grid-cols-3">
                <div class="flex gap-4">
                    <ShieldCheck class="size-6 shrink-0 text-emerald-500" />
                    <div>
                        <h3 class="font-semibold">Manajemen Risiko Utama</h3>
                        <p class="mt-1 text-sm text-muted-foreground">Setiap posisi terukur — maksimal 1-2% risiko per trade. Modal Anda dijaga.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <TrendingUp class="size-6 shrink-0 text-emerald-500" />
                    <div>
                        <h3 class="font-semibold">Transparan & Terukur</h3>
                        <p class="mt-1 text-sm text-muted-foreground">Rekam jejak PnL bisa Anda pantau langsung, bukan sekadar klaim.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <GraduationCap class="size-6 shrink-0 text-emerald-500" />
                    <div>
                        <h3 class="font-semibold">Fokus Edukasi</h3>
                        <p class="mt-1 text-sm text-muted-foreground">Bukan cuma ikut sinyal — Anda diajari paham prosesnya agar mandiri.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- SERVICES -->
        <section class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Layanan Kami</h2>
                <p class="mt-3 text-muted-foreground">Pilih layanan yang paling sesuai dengan tujuan dan level Anda.</p>
            </div>

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="service in services"
                    :key="service.id"
                    class="relative flex flex-col rounded-2xl border bg-card p-6 shadow-sm transition-transform hover:-translate-y-1"
                    :class="service.is_featured ? 'border-emerald-500/40 ring-1 ring-emerald-500/20' : 'border-border/60'"
                >
                    <span v-if="service.is_featured" class="absolute -top-2.5 right-4 rounded-full bg-emerald-500 px-2.5 py-0.5 text-[10px] font-semibold text-white">POPULER</span>
                    <span class="flex size-11 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                        <component :is="iconMap[service.icon] ?? Sparkles" class="size-5" />
                    </span>
                    <h3 class="mt-4 font-semibold">{{ service.title }}</h3>
                    <p class="mt-2 flex-1 text-sm text-muted-foreground">{{ service.description }}</p>
                    <ul class="mt-4 space-y-1.5">
                        <li v-for="(f, i) in service.features" :key="i" class="flex items-start gap-2 text-xs text-muted-foreground">
                            <CheckCircle2 class="mt-0.5 size-3.5 shrink-0 text-emerald-500" /> {{ f }}
                        </li>
                    </ul>
                    <p v-if="service.price_label" class="mt-5 text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ service.price_label }}</p>
                </div>
            </div>
        </section>

        <!-- TEAM -->
        <section class="border-y border-border/60 bg-muted/30">
            <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Tim Konsultan</h2>
                        <p class="mt-3 text-muted-foreground">Trader berpengalaman yang siap mendampingi Anda.</p>
                    </div>
                    <Link href="/team" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600 hover:underline dark:text-emerald-400">
                        Lihat semua <ArrowRight class="size-4" />
                    </Link>
                </div>

                <div class="mt-12 grid gap-6 sm:grid-cols-2">
                    <Link
                        v-for="m in members"
                        :key="m.slug"
                        :href="`/team/${m.slug}`"
                        class="group flex items-center gap-5 rounded-2xl border border-border/60 bg-card p-5 shadow-sm transition-transform hover:-translate-y-1"
                    >
                        <img :src="m.avatar_url ?? ''" :alt="m.name" class="size-20 shrink-0 rounded-2xl bg-muted object-cover" />
                        <div class="min-w-0 flex-1">
                            <h3 class="truncate font-semibold group-hover:text-emerald-600 dark:group-hover:text-emerald-400">{{ m.name }}</h3>
                            <p class="truncate text-sm text-muted-foreground">{{ m.title }}</p>
                            <div class="mt-3 flex gap-4 text-xs">
                                <span><SecretMoney :value="m.stats.total_pnl" with-sign class="font-semibold" /> <span class="text-muted-foreground">PnL</span></span>
                                <span><span class="font-semibold">{{ m.stats.win_rate }}%</span> <span class="text-muted-foreground">WR</span></span>
                                <span><span class="font-semibold">{{ m.stats.total_trades }}</span> <span class="text-muted-foreground">trade</span></span>
                            </div>
                        </div>
                        <ArrowRight class="size-5 shrink-0 text-muted-foreground transition-transform group-hover:translate-x-1 group-hover:text-emerald-500" />
                    </Link>
                </div>
            </div>
        </section>

        <!-- TESTIMONIALS -->
        <section v-if="testimonials.length" class="mx-auto max-w-6xl px-4 py-20 sm:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Kata Mereka</h2>
                <p class="mt-3 text-muted-foreground">Pengalaman member & klien bersama Lentera Project.</p>
            </div>
            <div class="mt-12 grid gap-6 md:grid-cols-3">
                <figure v-for="t in testimonials" :key="t.id" class="flex flex-col rounded-2xl border border-border/60 bg-card p-6 shadow-sm">
                    <Quote class="size-7 text-emerald-500/40" />
                    <blockquote class="mt-3 flex-1 text-sm leading-relaxed text-foreground/90">“{{ t.quote }}”</blockquote>
                    <div class="mt-4 flex items-center gap-0.5">
                        <Star v-for="i in t.rating" :key="i" class="size-4 fill-yellow-400 text-yellow-400" />
                    </div>
                    <figcaption class="mt-3">
                        <p class="text-sm font-semibold">{{ t.author_name }}</p>
                        <p class="text-xs text-muted-foreground">{{ t.author_role }}</p>
                    </figcaption>
                </figure>
            </div>
        </section>

        <!-- CTA -->
        <section class="mx-auto max-w-6xl px-4 pb-24 sm:px-6">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-500 to-green-700 px-6 py-14 text-center text-white shadow-lg sm:px-12">
                <div class="pointer-events-none absolute -right-10 -top-10 size-48 rounded-full bg-white/10 blur-2xl"></div>
                <Users class="mx-auto size-10 opacity-90" />
                <h2 class="mt-4 text-2xl font-bold sm:text-3xl">Siap trading lebih terarah?</h2>
                <p class="mx-auto mt-3 max-w-xl text-white/90">Konsultasikan tujuan Anda dengan tim kami. Kami bantu susun rencana yang realistis dan aman.</p>
                <Link href="/contact" class="mt-7 inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-semibold text-green-700 shadow-md transition-transform hover:-translate-y-0.5">
                    Hubungi Kami <ArrowRight class="size-4" />
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>
