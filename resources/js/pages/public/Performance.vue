<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { LineChart, Lock } from '@lucide/vue';
import { computed } from 'vue';
import BarChart from '@/components/charts/BarChart.vue';
import EquityChart from '@/components/charts/EquityChart.vue';
import PnlCalendar from '@/components/public/PnlCalendar.vue';
import SecretMoney from '@/components/public/SecretMoney.vue';
import StatTile from '@/components/public/StatTile.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

const authed = computed(() => !!(usePage().props.auth as { user?: unknown } | undefined)?.user);

defineProps<{
    members: Array<{ name: string; slug: string }>;
    activeMember: string | null;
    stats: { total_pnl: number; total_trades: number; win_rate: number; best_trade: number };
    equity_curve: Array<{ label: string; value: number }>;
    monthly_pnl: Array<{ label: string; value: number }>;
    calendar_trades: Array<{ id: number; member: string; member_slug: string; symbol: string; side: string; leverage: number | null; pnl: number; roi: number | null; date: string | null; closed_at: string | null }>;
}>();

const filterBy = (slug: string | null) => {
    router.get('/performance', slug ? { member: slug } : {}, { preserveScroll: true, preserveState: true });
};
</script>

<template>
    <Head title="Track Record & Performa" />

    <PublicLayout>
        <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-300">
                    <LineChart class="size-3.5" /> Transparansi Kinerja
                </span>
                <h1 class="mt-5 text-4xl font-extrabold tracking-tight sm:text-5xl">Track Record</h1>
                <p class="mt-4 text-muted-foreground">Rekam jejak PnL tim kami — bisa Anda saring per konsultan.</p>
            </div>

            <!-- Banner login untuk guest -->
            <div v-if="!authed" class="mx-auto mt-8 flex max-w-3xl flex-col items-center justify-between gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/5 px-5 py-4 text-sm sm:flex-row">
                <div class="flex items-center gap-2.5">
                    <Lock class="size-4 shrink-0 text-emerald-600 dark:text-emerald-400" />
                    <span class="text-muted-foreground">Angka nominal disamarkan. <span class="font-medium text-foreground">Login atau daftar</span> untuk melihat angka PnL secara penuh.</span>
                </div>
                <div class="flex shrink-0 gap-2">
                    <Link href="/login" class="rounded-lg border border-border bg-card px-4 py-2 font-medium hover:bg-muted">Masuk</Link>
                    <Link href="/register" class="rounded-lg bg-gradient-to-br from-emerald-400 to-green-600 px-4 py-2 font-semibold text-white">Daftar</Link>
                </div>
            </div>

            <!-- Filter -->
            <div class="mt-10 flex flex-wrap justify-center gap-2">
                <button
                    class="rounded-full border px-4 py-1.5 text-sm font-medium transition-colors"
                    :class="!activeMember ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-border bg-card hover:bg-muted'"
                    @click="filterBy(null)"
                >
                    Semua Tim
                </button>
                <button
                    v-for="m in members"
                    :key="m.slug"
                    class="rounded-full border px-4 py-1.5 text-sm font-medium transition-colors"
                    :class="activeMember === m.slug ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-border bg-card hover:bg-muted'"
                    @click="filterBy(m.slug)"
                >
                    {{ m.name }}
                </button>
            </div>

            <!-- Stats -->
            <div class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
                <StatTile label="Total Realized PnL" value="" :accent="stats.total_pnl >= 0 ? 'up' : 'down'">
                    <template #value><SecretMoney :value="stats.total_pnl" with-sign :colored="false" /></template>
                </StatTile>
                <StatTile label="Win Rate" :value="`${stats.win_rate}%`" />
                <StatTile label="Total Trade" :value="String(stats.total_trades)" />
                <StatTile label="Trade Terbaik" value="" accent="up">
                    <template #value><SecretMoney :value="stats.best_trade" with-sign :colored="false" /></template>
                </StatTile>
            </div>

            <!-- Charts -->
            <div class="mt-8 grid gap-6 lg:grid-cols-5">
                <div class="rounded-3xl border border-border/60 bg-card p-6 shadow-sm lg:col-span-3">
                    <h3 class="font-semibold">Kurva Ekuitas</h3>
                    <p class="text-xs text-muted-foreground">Akumulasi PnL {{ activeMember ? 'konsultan terpilih' : 'seluruh tim' }}</p>
                    <div class="mt-4"><EquityChart :points="equity_curve" /></div>
                </div>
                <div class="rounded-3xl border border-border/60 bg-card p-6 shadow-sm lg:col-span-2">
                    <h3 class="font-semibold">PnL Bulanan</h3>
                    <p class="text-xs text-muted-foreground">6 bulan terakhir</p>
                    <div class="mt-4"><BarChart :points="monthly_pnl" :mask="!authed" /></div>
                </div>
            </div>

            <!-- Kalender PnL Harian -->
            <div class="mt-8 rounded-3xl border border-border/60 bg-card p-6 shadow-sm">
                <PnlCalendar :trades="calendar_trades" :authed="authed" />
            </div>
        </section>
    </PublicLayout>
</template>
