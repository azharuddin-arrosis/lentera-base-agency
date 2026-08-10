<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowDownRight, ArrowUpRight, Crown, Lock, Radio, Target, TrendingUp } from '@lucide/vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

interface SignalCard {
    id: number; pair: string; market: string; side: string; timeframe: string | null; leverage: string | null;
    author: string; author_avatar: string | null; status: string; result: string | null; pnl_percent: number | null;
    is_premium: boolean; locked: boolean; published_at: string | null;
    entry?: number | null; entry_max?: number | null; stop_loss?: number | null; take_profits?: number[]; analysis?: string | null;
}

defineProps<{
    signals: { data: SignalCard[]; links: Array<{ url: string | null; label: string; active: boolean }> };
    canSeePremium: boolean;
    stats: { total: number; open: number; win: number };
}>();

const RESULT: Record<string, { label: string; class: string }> = {
    running: { label: 'Berjalan', class: 'bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300' },
    tp_hit: { label: 'Target Tercapai', class: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' },
    sl_hit: { label: 'Stop Loss', class: 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300' },
    breakeven: { label: 'Breakeven', class: 'bg-muted text-muted-foreground' },
};

const fmt = (n: number | null | undefined) =>
    n === null || n === undefined ? '-' : new Intl.NumberFormat('id-ID', { maximumFractionDigits: 8 }).format(n);
</script>

<template>
    <Head title="Signal Trading" />

    <PublicLayout>
        <!-- Hero -->
        <section class="border-b bg-gradient-to-b from-emerald-500/10 to-transparent">
            <div class="mx-auto max-w-6xl px-4 py-12 sm:py-16">
                <p class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-3 py-1 text-sm font-medium text-emerald-600 dark:text-emerald-400">
                    <Radio class="size-4" /> Signal Trading
                </p>
                <h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">Sinyal dari tim & konsultan Lentera</h1>
                <p class="mt-2 max-w-2xl text-muted-foreground">Ide trading beserta entry, stop loss, dan target. Sebagian sinyal premium khusus member berlangganan.</p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <div class="rounded-xl border bg-card px-5 py-3"><p class="text-xs text-muted-foreground">Total Sinyal</p><p class="text-2xl font-bold">{{ stats.total }}</p></div>
                    <div class="rounded-xl border bg-card px-5 py-3"><p class="text-xs text-muted-foreground">Aktif</p><p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ stats.open }}</p></div>
                    <div class="rounded-xl border bg-card px-5 py-3"><p class="text-xs text-muted-foreground">Target Tercapai</p><p class="text-2xl font-bold">{{ stats.win }}</p></div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-4 py-10">
            <div v-if="signals.data.length" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <article v-for="s in signals.data" :key="s.id" class="flex flex-col overflow-hidden rounded-2xl border bg-card shadow-sm transition hover:shadow-md">
                    <!-- header -->
                    <div class="flex items-start justify-between gap-2 border-b p-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <Link :href="`/signals/${s.id}`" class="text-lg font-bold tracking-tight hover:text-emerald-600 dark:hover:text-emerald-400">{{ s.pair }}</Link>
                                <Crown v-if="s.is_premium" class="size-4 text-amber-500" />
                            </div>
                            <div class="mt-1 flex flex-wrap items-center gap-1.5 text-xs text-muted-foreground">
                                <span class="rounded bg-muted px-1.5 py-0.5 uppercase">{{ s.market }}</span>
                                <span v-if="s.timeframe">{{ s.timeframe }}</span>
                                <span v-if="s.leverage">· {{ s.leverage }}</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold" :class="s.side === 'long' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300'">
                            <ArrowUpRight v-if="s.side === 'long'" class="size-3.5" />
                            <ArrowDownRight v-else class="size-3.5" />
                            {{ s.side === 'long' ? 'LONG' : 'SHORT' }}
                        </span>
                    </div>

                    <!-- konten -->
                    <div class="flex flex-1 flex-col p-4">
                        <!-- terkunci -->
                        <div v-if="s.locked" class="flex flex-1 flex-col items-center justify-center gap-3 rounded-xl border border-dashed bg-muted/40 py-8 text-center">
                            <div class="flex size-11 items-center justify-center rounded-full bg-amber-500/15 text-amber-500"><Lock class="size-5" /></div>
                            <div>
                                <p class="font-semibold">Konten Premium</p>
                                <p class="mt-0.5 text-xs text-muted-foreground">Entry, Stop Loss & Target khusus member berlangganan.</p>
                            </div>
                            <Link href="/contact" class="rounded-lg bg-amber-500 px-4 py-2 text-xs font-semibold text-white transition hover:bg-amber-600">Langganan untuk Buka</Link>
                        </div>

                        <!-- terbuka -->
                        <template v-else>
                            <dl class="space-y-2 text-sm">
                                <div class="flex items-center justify-between rounded-lg bg-muted/40 px-3 py-2">
                                    <dt class="flex items-center gap-1.5 text-muted-foreground"><TrendingUp class="size-4" /> Entry</dt>
                                    <dd class="font-semibold">{{ fmt(s.entry) }}<span v-if="s.entry_max"> – {{ fmt(s.entry_max) }}</span></dd>
                                </div>
                                <div class="flex items-center justify-between rounded-lg bg-rose-500/5 px-3 py-2">
                                    <dt class="text-rose-600 dark:text-rose-400">Stop Loss</dt>
                                    <dd class="font-semibold text-rose-600 dark:text-rose-400">{{ fmt(s.stop_loss) }}</dd>
                                </div>
                                <div v-if="s.take_profits && s.take_profits.length" class="rounded-lg bg-emerald-500/5 px-3 py-2">
                                    <dt class="mb-1 flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400"><Target class="size-4" /> Target</dt>
                                    <dd class="flex flex-wrap gap-1.5">
                                        <span v-for="(tp, i) in s.take_profits" :key="i" class="rounded-md bg-emerald-500/10 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-300">TP{{ i + 1 }}: {{ fmt(tp) }}</span>
                                    </dd>
                                </div>
                            </dl>
                            <p v-if="s.analysis" class="mt-3 line-clamp-4 text-sm text-muted-foreground">{{ s.analysis }}</p>
                        </template>

                        <!-- footer -->
                        <div class="mt-4 flex items-center justify-between border-t pt-3 text-xs">
                            <div class="flex items-center gap-2">
                                <img v-if="s.author_avatar" :src="s.author_avatar" :alt="s.author" class="size-6 rounded-full object-cover" />
                                <span class="text-muted-foreground">{{ s.author }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span v-if="s.result" class="rounded-full px-2 py-0.5 font-medium" :class="RESULT[s.result]?.class">{{ RESULT[s.result]?.label }}</span>
                                <span v-if="s.pnl_percent !== null && s.pnl_percent !== undefined" class="font-bold" :class="s.pnl_percent > 0 ? 'text-emerald-600 dark:text-emerald-400' : s.pnl_percent < 0 ? 'text-rose-600 dark:text-rose-400' : ''">{{ s.pnl_percent > 0 ? '+' : '' }}{{ s.pnl_percent }}%</span>
                            </div>
                        </div>
                        <div class="mt-2 flex items-center justify-between border-t pt-2">
                            <span v-if="s.published_at" class="text-[11px] text-muted-foreground">{{ s.published_at }}</span>
                            <Link :href="`/signals/${s.id}`" class="text-xs font-medium text-emerald-600 hover:underline dark:text-emerald-400">Selengkapnya →</Link>
                        </div>
                    </div>
                </article>
            </div>

            <div v-else class="rounded-2xl border border-dashed py-20 text-center text-muted-foreground">
                <Radio class="mx-auto size-8 opacity-50" />
                <p class="mt-3">Belum ada sinyal yang dipublikasikan.</p>
            </div>

            <!-- pagination -->
            <div v-if="signals.links.length > 3" class="mt-8 flex flex-wrap items-center justify-center gap-1">
                <template v-for="(link, i) in signals.links" :key="i">
                    <Link v-if="link.url" :href="link.url" class="rounded-lg border px-3 py-1.5 text-sm" :class="link.active ? 'border-emerald-500 bg-emerald-500 text-white' : 'hover:bg-muted'" v-html="link.label" />
                    <span v-else class="rounded-lg border px-3 py-1.5 text-sm text-muted-foreground opacity-50" v-html="link.label" />
                </template>
            </div>
        </section>
    </PublicLayout>
</template>
