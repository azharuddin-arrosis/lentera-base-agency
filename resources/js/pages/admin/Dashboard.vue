<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CandlestickChart, MessagesSquare, Plus, TrendingUp, Users } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatNumber, formatUsd, pnlColor } from '@/lib/format';
import type { BreadcrumbItem } from '@/types';

defineProps<{
    kpi: { total_pnl: number; total_trades: number; win_rate: number; members: number; new_leads: number };
    recentTrades: Array<{ id: number; member: string; symbol: string; side: string; pnl: number; closed_at: string | null }>;
    recentLeads: Array<{ id: number; name: string; subject: string | null; status: string; created_at: string | null }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Dashboard" />

        <div class="flex flex-col gap-6 p-4 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Selamat datang 👋</h1>
                    <p class="text-sm text-muted-foreground">Ringkasan performa & aktivitas Lentera Project.</p>
                </div>
                <Link href="/admin/trades/create" class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600">
                    <Plus class="size-4" /> Input PnL
                </Link>
            </div>

            <!-- KPI -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-border/60 bg-card p-5">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Total PnL</p>
                        <TrendingUp class="size-4 text-emerald-500" />
                    </div>
                    <p class="mt-2 text-2xl font-bold" :class="pnlColor(kpi.total_pnl)">{{ formatUsd(kpi.total_pnl, true) }}</p>
                </div>
                <div class="rounded-xl border border-border/60 bg-card p-5">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Win Rate</p>
                        <CandlestickChart class="size-4 text-emerald-500" />
                    </div>
                    <p class="mt-2 text-2xl font-bold">{{ kpi.win_rate }}%</p>
                    <p class="text-xs text-muted-foreground">{{ formatNumber(kpi.total_trades) }} trade tercatat</p>
                </div>
                <div class="rounded-xl border border-border/60 bg-card p-5">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Member</p>
                        <Users class="size-4 text-sky-500" />
                    </div>
                    <p class="mt-2 text-2xl font-bold">{{ kpi.members }}</p>
                </div>
                <Link href="/admin/leads" class="rounded-xl border border-border/60 bg-card p-5 transition-colors hover:bg-muted/50">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Pesan Baru</p>
                        <MessagesSquare class="size-4 text-rose-500" />
                    </div>
                    <p class="mt-2 text-2xl font-bold">{{ kpi.new_leads }}</p>
                </Link>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Recent trades -->
                <div class="rounded-xl border border-border/60 bg-card">
                    <div class="flex items-center justify-between border-b border-border/60 px-5 py-3">
                        <h3 class="font-semibold">Trade Terbaru</h3>
                        <Link href="/admin/trades" class="text-xs font-medium text-emerald-600 hover:underline dark:text-emerald-400">Semua</Link>
                    </div>
                    <ul class="divide-y divide-border/40">
                        <li v-for="t in recentTrades" :key="t.id" class="flex items-center justify-between px-5 py-3 text-sm">
                            <div>
                                <p class="font-medium">{{ t.symbol }} <span class="text-xs text-muted-foreground">· {{ t.member }}</span></p>
                                <p class="text-xs text-muted-foreground">{{ t.closed_at }} · {{ t.side === 'long' ? 'Long' : 'Short' }}</p>
                            </div>
                            <span class="font-semibold" :class="pnlColor(t.pnl)">{{ formatUsd(t.pnl, true) }}</span>
                        </li>
                        <li v-if="!recentTrades.length" class="px-5 py-8 text-center text-sm text-muted-foreground">Belum ada trade.</li>
                    </ul>
                </div>

                <!-- Recent leads -->
                <div class="rounded-xl border border-border/60 bg-card">
                    <div class="flex items-center justify-between border-b border-border/60 px-5 py-3">
                        <h3 class="font-semibold">Pesan Terbaru</h3>
                        <Link href="/admin/leads" class="text-xs font-medium text-emerald-600 hover:underline dark:text-emerald-400">Semua</Link>
                    </div>
                    <ul class="divide-y divide-border/40">
                        <li v-for="l in recentLeads" :key="l.id" class="flex items-center justify-between px-5 py-3 text-sm">
                            <div>
                                <p class="font-medium">{{ l.name }}</p>
                                <p class="text-xs text-muted-foreground">{{ l.subject ?? 'Tanpa subjek' }} · {{ l.created_at }}</p>
                            </div>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="l.status === 'new' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-muted text-muted-foreground'">{{ l.status }}</span>
                        </li>
                        <li v-if="!recentLeads.length" class="px-5 py-8 text-center text-sm text-muted-foreground">Belum ada pesan.</li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
