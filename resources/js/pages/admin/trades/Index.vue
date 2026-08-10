<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Search } from '@lucide/vue';
import { ref } from 'vue';
import ConfirmDelete from '@/components/admin/ConfirmDelete.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatPercent, formatUsd, pnlColor } from '@/lib/format';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    trades: {
        data: Array<{ id: number; member: string; symbol: string; side: string; market: string; leverage: number | null; pnl: number; roi: number | null; status: string; source: string; closed_at: string | null }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
        from: number | null;
    };
    members: Array<{ id: number; name: string }>;
    filters: { member: number | null; search: string | null };
}>();

const search = ref(props.filters.search ?? '');
const memberFilter = ref(props.filters.member ? String(props.filters.member) : '');

const applyFilter = () => {
    router.get('/admin/trades', {
        member: memberFilter.value || undefined,
        search: search.value || undefined,
    }, { preserveState: true, replace: true });
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Trade & PnL', href: '/admin/trades' },
];

const destroy = (id: number) => router.delete(`/admin/trades/${id}`, { preserveScroll: true });
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Kelola Trade & PnL" />

        <div class="flex flex-col gap-6 p-4 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Trade & PnL</h1>
                    <p class="text-sm text-muted-foreground">Input manual atau hasil sinkron Binance.</p>
                </div>
                <Link href="/admin/trades/create" class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600">
                    <Plus class="size-4" /> Input PnL
                </Link>
            </div>

            <!-- Filter -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 sm:max-w-xs">
                    <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari simbol (mis. BTCUSDT)"
                        class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        @keyup.enter="applyFilter"
                    />
                </div>
                <select v-model="memberFilter" class="rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500" @change="applyFilter">
                    <option value="">Semua Member</option>
                    <option v-for="mm in members" :key="mm.id" :value="String(mm.id)">{{ mm.name }}</option>
                </select>
                <button type="button" class="rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-muted" @click="applyFilter">Terapkan</button>
            </div>

            <div class="overflow-hidden rounded-xl border border-border/60 bg-card">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[820px] text-sm">
                        <thead>
                            <tr class="border-b border-border/60 text-left text-xs uppercase tracking-wide text-muted-foreground">
                                <th class="w-12 px-5 py-3 font-medium">No.</th>
                                <th class="px-4 py-3 font-medium">Tanggal</th>
                                <th class="px-4 py-3 font-medium">Member</th>
                                <th class="px-4 py-3 font-medium">Simbol</th>
                                <th class="px-4 py-3 font-medium">Arah</th>
                                <th class="px-4 py-3 text-right font-medium">ROI</th>
                                <th class="px-4 py-3 text-right font-medium">PnL</th>
                                <th class="px-4 py-3 text-center font-medium">Sumber</th>
                                <th class="px-5 py-3 text-right font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(t, i) in trades.data" :key="t.id" class="border-b border-border/40 last:border-0 hover:bg-muted/40">
                                <td class="px-5 py-3 text-muted-foreground">{{ (trades.from ?? 1) + i }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-muted-foreground">{{ t.closed_at ?? '-' }}</td>
                                <td class="px-4 py-3">{{ t.member }}</td>
                                <td class="px-4 py-3 font-medium">
                                    {{ t.symbol }}
                                    <span v-if="t.leverage" class="ml-1 rounded bg-muted px-1.5 py-0.5 text-[10px] text-muted-foreground">{{ Number(t.leverage) }}x</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium" :class="t.side === 'long' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400'">
                                        {{ t.side === 'long' ? 'Long' : 'Short' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right" :class="pnlColor(t.roi)">{{ t.roi !== null ? formatPercent(t.roi, true) : '-' }}</td>
                                <td class="px-4 py-3 text-right font-semibold" :class="pnlColor(t.pnl)">{{ formatUsd(t.pnl, true) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="t.source === 'binance' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-muted text-muted-foreground'">{{ t.source }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link :href="`/admin/trades/${t.id}/edit`" class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground" title="Edit">
                                            <Pencil class="size-4" />
                                        </Link>
                                        <ConfirmDelete @confirm="destroy(t.id)" />
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!trades.data.length">
                                <td colspan="9" class="px-5 py-10 text-center text-muted-foreground">Belum ada trade. Klik "Input PnL" untuk menambah.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="trades.links.length > 3" class="flex flex-wrap items-center justify-center gap-1">
                <template v-for="(link, i) in trades.links" :key="i">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="rounded-lg border px-3 py-1.5 text-sm"
                        :class="link.active ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-border hover:bg-muted'"
                        v-html="link.label"
                    />
                    <span v-else class="rounded-lg border border-border px-3 py-1.5 text-sm text-muted-foreground opacity-50" v-html="link.label" />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
