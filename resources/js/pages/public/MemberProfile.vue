<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, AtSign, Award, CalendarDays, Camera, CheckCircle2, Globe, LineChart, MapPin, Quote, Send, Sparkles, Star, TrendingUp } from '@lucide/vue';
import { computed, type Component } from 'vue';
import BarChart from '@/components/charts/BarChart.vue';
import EquityChart from '@/components/charts/EquityChart.vue';
import PnlCalendar from '@/components/public/PnlCalendar.vue';
import SecretMoney from '@/components/public/SecretMoney.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { formatPercent, pnlColor } from '@/lib/format';

const authed = computed(() => !!(usePage().props.auth as { user?: unknown } | undefined)?.user);

interface Trade {
    id: number;
    member: string;
    member_slug: string;
    symbol: string;
    side: string;
    market: string;
    leverage: number | null;
    entry_price: string | number | null;
    exit_price: string | number | null;
    pnl: number;
    roi: number | null;
    source: string;
    notes: string | null;
    date: string | null;
    closed_at: string | null;
}

defineProps<{
    member: {
        name: string;
        title: string | null;
        tagline: string | null;
        bio: string | null;
        avatar_url: string | null;
        specialization: string | null;
        experience_years: number;
        location: string | null;
        joined_at: string | null;
        socials: Record<string, string>;
    };
    stats: { total_pnl: number; total_trades: number; win_rate: number; winning_trades: number; best_trade: number; avg_roi: number };
    equity_curve: Array<{ label: string; value: number }>;
    monthly_pnl: Array<{ label: string; value: number }>;
    trades: Trade[];
    consultation: { done_count: number; rating_count: number; avg_rating: number };
    reviews: Array<{ id: number; name: string; rating: number; review: string | null; date: string }>;
}>();

const socialMeta: Record<string, { icon: Component; label: string }> = {
    telegram: { icon: Send, label: 'Telegram' },
    instagram: { icon: Camera, label: 'Instagram' },
    twitter: { icon: AtSign, label: 'X / Twitter' },
    tradingview: { icon: LineChart, label: 'TradingView' },
    website: { icon: Globe, label: 'Website' },
};
</script>

<template>
    <Head :title="`${member.name} — Profil Trader`" />

    <PublicLayout>
        <!-- ===== HERO ===== -->
        <div class="relative">
            <!-- cover -->
            <div class="h-56 w-full bg-gradient-to-br from-emerald-500 via-green-600 to-emerald-700 sm:h-64">
                <div class="pointer-events-none absolute inset-0 h-56 overflow-hidden sm:h-64">
                    <div class="absolute -right-16 -top-16 size-72 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="absolute -bottom-24 left-10 size-72 rounded-full bg-black/10 blur-2xl"></div>
                    <svg class="absolute inset-0 size-full opacity-[0.12]" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <pattern id="grid" width="32" height="32" patternUnits="userSpaceOnUse">
                                <path d="M32 0H0V32" fill="none" stroke="white" stroke-width="1" />
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#grid)" />
                    </svg>
                </div>
                <div class="relative mx-auto flex h-full max-w-6xl items-start px-4 pt-5 sm:px-6">
                    <Link href="/team" class="inline-flex items-center gap-1.5 rounded-lg bg-white/15 px-3 py-1.5 text-sm font-medium text-white backdrop-blur transition-colors hover:bg-white/25">
                        <ArrowLeft class="size-4" /> Tim
                    </Link>
                </div>
            </div>

            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="-mt-24 flex flex-col gap-6 sm:-mt-20 sm:flex-row sm:items-end">
                    <!-- avatar -->
                    <div class="relative shrink-0">
                        <img
                            :src="member.avatar_url ?? ''"
                            :alt="member.name"
                            class="size-40 rounded-3xl bg-muted object-cover shadow-xl ring-4 ring-background sm:size-44"
                        />
                        <span class="absolute -bottom-2 -right-2 flex items-center gap-1 rounded-full bg-emerald-500 px-2.5 py-1 text-[11px] font-semibold text-white shadow-md">
                            <Sparkles class="size-3" /> Verified
                        </span>
                    </div>
                    <!-- info -->
                    <div class="flex-1 pb-1">
                        <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">{{ member.name }}</h1>
                        <p class="mt-1 font-semibold text-emerald-600 dark:text-emerald-400">{{ member.title }}</p>
                        <p v-if="member.tagline" class="mt-2 max-w-xl text-sm italic text-muted-foreground">“{{ member.tagline }}”</p>
                    </div>
                    <!-- socials -->
                    <div v-if="Object.keys(member.socials).length" class="flex flex-wrap gap-2 pb-1">
                        <a
                            v-for="(url, key) in member.socials"
                            :key="key"
                            :href="url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex size-10 items-center justify-center rounded-xl border border-border bg-card text-muted-foreground shadow-sm transition-colors hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400"
                            :title="socialMeta[key]?.label ?? key"
                        >
                            <component :is="socialMeta[key]?.icon ?? Globe" class="size-4.5" />
                        </a>
                    </div>
                </div>

                <!-- quick facts -->
                <div class="mt-5 flex flex-wrap gap-x-6 gap-y-2 text-sm text-muted-foreground">
                    <span v-if="member.specialization" class="inline-flex items-center gap-1.5"><Award class="size-4 text-emerald-500" /> {{ member.specialization }}</span>
                    <span class="inline-flex items-center gap-1.5"><TrendingUp class="size-4 text-emerald-500" /> {{ member.experience_years }} tahun pengalaman</span>
                    <span v-if="member.location" class="inline-flex items-center gap-1.5"><MapPin class="size-4 text-emerald-500" /> {{ member.location }}</span>
                    <span v-if="member.joined_at" class="inline-flex items-center gap-1.5"><CalendarDays class="size-4 text-emerald-500" /> Bergabung {{ member.joined_at }}</span>
                </div>
            </div>
        </div>

        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6">
            <!-- ===== STAT STRIP ===== -->
            <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-6">
                <div class="rounded-2xl border border-emerald-500/20 bg-gradient-to-br from-emerald-500/10 to-transparent p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Total PnL</p>
                    <p class="mt-1 text-xl font-extrabold sm:text-2xl"><SecretMoney :value="stats.total_pnl" with-sign /></p>
                </div>
                <div class="rounded-2xl border border-emerald-500/20 bg-gradient-to-br from-emerald-500/10 to-transparent p-4">
                    <p class="flex items-center gap-1 text-xs font-medium uppercase tracking-wide text-muted-foreground"><CheckCircle2 class="size-3.5 text-emerald-500" /> Konsultasi Sukses</p>
                    <p class="mt-1 text-xl font-extrabold sm:text-2xl">{{ consultation.done_count }}</p>
                    <div v-if="consultation.rating_count" class="mt-0.5 flex items-center gap-1 text-xs">
                        <Star class="size-3.5 fill-yellow-400 text-yellow-400" />
                        <span class="font-semibold">{{ consultation.avg_rating }}</span>
                        <span class="text-muted-foreground">({{ consultation.rating_count }})</span>
                    </div>
                    <p v-else class="mt-0.5 text-[11px] text-muted-foreground">Belum ada rating</p>
                </div>
                <div class="rounded-2xl border border-border/60 bg-card p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Win Rate</p>
                    <p class="mt-1 text-xl font-extrabold sm:text-2xl">{{ stats.win_rate }}%</p>
                </div>
                <div class="rounded-2xl border border-border/60 bg-card p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Total Trade</p>
                    <p class="mt-1 text-xl font-extrabold sm:text-2xl">{{ stats.total_trades }}</p>
                </div>
                <div class="rounded-2xl border border-border/60 bg-card p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Trade Terbaik</p>
                    <p class="mt-1 text-xl font-extrabold sm:text-2xl"><SecretMoney :value="stats.best_trade" with-sign /></p>
                </div>
                <div class="rounded-2xl border border-border/60 bg-card p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Rata-rata ROI</p>
                    <p class="mt-1 text-xl font-extrabold sm:text-2xl" :class="pnlColor(stats.avg_roi)">{{ formatPercent(stats.avg_roi, true) }}</p>
                </div>
            </div>

            <!-- ===== BIO + CHARTS ===== -->
            <div class="mt-8 grid gap-6 lg:grid-cols-3">
                <div class="rounded-3xl border border-border/60 bg-card p-6 shadow-sm lg:col-span-1">
                    <h3 class="font-semibold">Tentang</h3>
                    <p v-if="member.bio" class="mt-3 text-sm leading-relaxed text-muted-foreground">{{ member.bio }}</p>
                    <p v-else class="mt-3 text-sm text-muted-foreground">Belum ada deskripsi.</p>
                </div>

                <div class="rounded-3xl border border-border/60 bg-card p-6 shadow-sm lg:col-span-2">
                    <h3 class="font-semibold">Kurva Ekuitas</h3>
                    <p class="text-xs text-muted-foreground">Akumulasi PnL dari waktu ke waktu</p>
                    <div class="mt-4"><EquityChart :points="equity_curve" /></div>
                </div>
            </div>

            <!-- Kalender PnL Harian + PnL Bulanan (satu baris) -->
            <div class="mt-6 grid gap-6 lg:grid-cols-5">
                <div class="rounded-3xl border border-border/60 bg-card p-6 shadow-sm lg:col-span-3">
                    <PnlCalendar :trades="trades" :authed="authed" />
                </div>
                <div class="flex flex-col rounded-3xl border border-border/60 bg-card p-6 shadow-sm lg:col-span-2">
                    <h3 class="font-semibold">PnL Bulanan</h3>
                    <p class="text-xs text-muted-foreground">6 bulan terakhir</p>
                    <div class="mt-4 flex-1"><BarChart :points="monthly_pnl" :mask="!authed" /></div>
                </div>
            </div>

            <!-- ===== ULASAN KONSULTASI ===== -->
            <div class="mt-6 rounded-3xl border border-border/60 bg-card p-6 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <Quote class="size-5 text-emerald-500" />
                        <h3 class="font-semibold">Ulasan Konsultasi</h3>
                    </div>
                    <div v-if="consultation.rating_count" class="flex items-center gap-2 rounded-full bg-emerald-500/10 px-3 py-1 text-sm">
                        <Star class="size-4 fill-yellow-400 text-yellow-400" />
                        <span class="font-bold">{{ consultation.avg_rating }}</span>
                        <span class="text-muted-foreground">dari {{ consultation.rating_count }} ulasan · {{ consultation.done_count }} konsultasi selesai</span>
                    </div>
                </div>

                <div v-if="reviews.length" class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <figure v-for="r in reviews" :key="r.id" class="flex flex-col rounded-2xl border border-border/60 bg-muted/20 p-4">
                        <div class="flex items-center gap-0.5">
                            <Star v-for="i in 5" :key="i" class="size-4" :class="i <= r.rating ? 'fill-yellow-400 text-yellow-400' : 'text-muted-foreground/30'" />
                        </div>
                        <blockquote v-if="r.review" class="mt-2 flex-1 text-sm text-foreground/90">“{{ r.review }}”</blockquote>
                        <blockquote v-else class="mt-2 flex-1 text-sm italic text-muted-foreground">Memberi rating {{ r.rating }} bintang.</blockquote>
                        <figcaption class="mt-3 text-xs">
                            <span class="font-semibold">{{ r.name }}</span>
                            <span class="text-muted-foreground"> · {{ r.date }}</span>
                        </figcaption>
                    </figure>
                </div>
                <p v-else class="mt-5 rounded-2xl border border-dashed border-border/60 py-10 text-center text-sm text-muted-foreground">
                    Belum ada ulasan konsultasi. Ulasan muncul setelah sesi konsultasi selesai & diberi rating.
                </p>
            </div>
        </div>
    </PublicLayout>
</template>
