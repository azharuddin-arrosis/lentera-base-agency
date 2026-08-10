<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowDownRight, ArrowLeft, ArrowUpRight, Crown, Lock, Target, TrendingUp } from '@lucide/vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

interface SignalDetail {
    id: number; pair: string; market: string; side: string; timeframe: string | null; leverage: string | null;
    author: string; author_avatar: string | null; status: string; result: string | null; pnl_percent: number | null;
    is_premium: boolean; locked: boolean; published_at: string | null; created_at: string | null;
    entry?: number | null; entry_max?: number | null; stop_loss?: number | null; take_profits?: number[]; analysis?: string | null;
}

defineProps<{ signal: SignalDetail; canSeePremium: boolean }>();

const RESULT: Record<string, { label: string; class: string }> = {
    running: { label: 'Berjalan', class: 'bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300' },
    tp_hit: { label: 'Target Tercapai', class: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' },
    sl_hit: { label: 'Stop Loss', class: 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300' },
    breakeven: { label: 'Breakeven', class: 'bg-muted text-muted-foreground' },
};
const STATUS: Record<string, string> = { open: 'Aktif', closed: 'Selesai', cancelled: 'Dibatalkan' };
const fmt = (n: number | null | undefined) =>
    n === null || n === undefined ? '-' : new Intl.NumberFormat('id-ID', { maximumFractionDigits: 8 }).format(n);
</script>

<template>
    <Head :title="`Signal ${signal.pair} ${signal.side === 'long' ? 'LONG' : 'SHORT'}`" />

    <PublicLayout>
        <article class="mx-auto max-w-3xl px-4 py-10 sm:py-12">
            <Link href="/signals" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="size-4" /> Semua Signal
            </Link>

            <!-- Header -->
            <div class="mt-5 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-3xl font-extrabold tracking-tight">{{ signal.pair }}</h1>
                        <Crown v-if="signal.is_premium" class="size-5 text-amber-500" />
                    </div>
                    <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-muted-foreground">
                        <span class="rounded bg-muted px-2 py-0.5 uppercase">{{ signal.market }}</span>
                        <span v-if="signal.timeframe">TF {{ signal.timeframe }}</span>
                        <span v-if="signal.leverage">· {{ signal.leverage }}</span>
                        <span>· {{ STATUS[signal.status] }}</span>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-bold" :class="signal.side === 'long' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300'">
                    <ArrowUpRight v-if="signal.side === 'long'" class="size-4" /><ArrowDownRight v-else class="size-4" />
                    {{ signal.side === 'long' ? 'LONG' : 'SHORT' }}
                </span>
            </div>

            <!-- Meta -->
            <div class="mt-4 flex flex-wrap items-center gap-3 text-sm">
                <div class="flex items-center gap-2">
                    <img v-if="signal.author_avatar" :src="signal.author_avatar" :alt="signal.author" class="size-7 rounded-full object-cover" />
                    <span class="font-medium">{{ signal.author }}</span>
                </div>
                <span v-if="signal.published_at" class="text-muted-foreground">· {{ signal.published_at }}</span>
                <span v-if="signal.result" class="rounded-full px-2.5 py-0.5 text-xs font-medium" :class="RESULT[signal.result]?.class">{{ RESULT[signal.result]?.label }}</span>
                <span v-if="signal.pnl_percent !== null && signal.pnl_percent !== undefined" class="text-base font-bold" :class="signal.pnl_percent > 0 ? 'text-emerald-600 dark:text-emerald-400' : signal.pnl_percent < 0 ? 'text-rose-600 dark:text-rose-400' : ''">{{ signal.pnl_percent > 0 ? '+' : '' }}{{ signal.pnl_percent }}%</span>
            </div>

            <!-- Terkunci -->
            <div v-if="signal.locked" class="mt-8 flex flex-col items-center gap-4 rounded-2xl border border-dashed bg-muted/40 py-12 text-center">
                <div class="flex size-14 items-center justify-center rounded-full bg-amber-500/15 text-amber-500"><Lock class="size-7" /></div>
                <div>
                    <p class="text-lg font-semibold">Sinyal Premium</p>
                    <p class="mx-auto mt-1 max-w-sm text-sm text-muted-foreground">Entry, Stop Loss, Target, dan analisa lengkap khusus untuk member berlangganan Lentera Project.</p>
                </div>
                <Link href="/contact" class="rounded-lg bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-600">Langganan untuk Buka</Link>
            </div>

            <!-- Terbuka -->
            <template v-else>
                <div class="mt-8 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border bg-card p-4">
                        <p class="flex items-center gap-1.5 text-xs text-muted-foreground"><TrendingUp class="size-4" /> Entry</p>
                        <p class="mt-1 text-lg font-bold">{{ fmt(signal.entry) }}<span v-if="signal.entry_max" class="text-sm font-medium text-muted-foreground"> – {{ fmt(signal.entry_max) }}</span></p>
                    </div>
                    <div class="rounded-2xl border border-rose-500/20 bg-rose-500/5 p-4">
                        <p class="text-xs text-rose-600 dark:text-rose-400">Stop Loss</p>
                        <p class="mt-1 text-lg font-bold text-rose-600 dark:text-rose-400">{{ fmt(signal.stop_loss) }}</p>
                    </div>
                    <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-4">
                        <p class="flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400"><Target class="size-4" /> Target</p>
                        <div class="mt-1 flex flex-wrap gap-1.5">
                            <span v-for="(tp, i) in signal.take_profits ?? []" :key="i" class="rounded-md bg-emerald-500/10 px-2 py-0.5 text-sm font-semibold text-emerald-700 dark:text-emerald-300">TP{{ i + 1 }}: {{ fmt(tp) }}</span>
                            <span v-if="!signal.take_profits?.length" class="text-sm text-muted-foreground">-</span>
                        </div>
                    </div>
                </div>

                <div v-if="signal.analysis" class="mt-6">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Analisa</h2>
                    <p class="mt-2 whitespace-pre-line leading-relaxed text-foreground/90">{{ signal.analysis }}</p>
                </div>

                <div class="mt-8 rounded-xl bg-muted/40 p-4 text-xs text-muted-foreground">
                    Sinyal ini bukan ajakan finansial. Selalu gunakan manajemen risiko sesuai profil Anda. Past performance tidak menjamin hasil di masa depan.
                </div>
            </template>
        </article>
    </PublicLayout>
</template>
