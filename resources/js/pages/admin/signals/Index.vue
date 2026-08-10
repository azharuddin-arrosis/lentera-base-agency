<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Crown, Pencil, Plus, Radio, Search } from '@lucide/vue';
import { ref } from 'vue';
import ConfirmDelete from '@/components/admin/ConfirmDelete.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface SignalRow {
    id: number; pair: string; market: string; side: string; timeframe: string | null;
    author: string; status: string; result: string | null; pnl_percent: number | null;
    is_premium: boolean; is_published: boolean; published_at: string | null;
}

const props = defineProps<{
    signals: { data: SignalRow[]; links: Array<{ url: string | null; label: string; active: boolean }>; from: number | null };
    filters: { search: string | null; status: string | null };
    counts: { all: number; open: number; closed: number; premium: number };
}>();

const search = ref(props.filters.search ?? '');
const statusFilter = ref(props.filters.status ?? '');
const applyFilter = () => router.get('/admin/signals', {
    search: search.value || undefined,
    status: statusFilter.value || undefined,
}, { preserveState: true, replace: true });

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Signal', href: '/admin/signals' },
];

const destroy = (id: number) => router.delete(`/admin/signals/${id}`, { preserveScroll: true });

const statusClass = (s: string) =>
    s === 'open' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
    : s === 'closed' ? 'bg-sky-500/10 text-sky-600 dark:text-sky-400'
    : 'bg-rose-500/10 text-rose-600 dark:text-rose-400';

const RESULT: Record<string, string> = { running: 'Berjalan', tp_hit: 'TP', sl_hit: 'SL', breakeven: 'BEP' };
const resultClass = (r: string | null) =>
    r === 'tp_hit' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
    : r === 'sl_hit' ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400'
    : 'bg-muted text-muted-foreground';
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Kelola Signal" />

        <div class="flex flex-col gap-6 p-4 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="flex items-center gap-2 text-2xl font-bold tracking-tight"><Radio class="size-6 text-emerald-500" /> Signal</h1>
                    <p class="text-sm text-muted-foreground">Sinyal trading yang tampil di halaman publik & member.</p>
                </div>
                <Link href="/admin/signals/create" class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600">
                    <Plus class="size-4" /> Buat Signal
                </Link>
            </div>

            <!-- KPI -->
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-xl border border-border/60 bg-card p-4"><p class="text-xs uppercase tracking-wide text-muted-foreground">Total</p><p class="mt-1 text-2xl font-bold">{{ counts.all }}</p></div>
                <div class="rounded-xl border border-border/60 bg-card p-4"><p class="text-xs uppercase tracking-wide text-muted-foreground">Aktif</p><p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ counts.open }}</p></div>
                <div class="rounded-xl border border-border/60 bg-card p-4"><p class="text-xs uppercase tracking-wide text-muted-foreground">Selesai</p><p class="mt-1 text-2xl font-bold">{{ counts.closed }}</p></div>
                <div class="rounded-xl border border-border/60 bg-card p-4"><p class="text-xs uppercase tracking-wide text-muted-foreground">Premium</p><p class="mt-1 text-2xl font-bold text-amber-500">{{ counts.premium }}</p></div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 sm:max-w-xs">
                    <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <input v-model="search" type="text" placeholder="Cari pair atau penulis..." class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" @keyup.enter="applyFilter" />
                </div>
                <select v-model="statusFilter" class="rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500" @change="applyFilter">
                    <option value="">Semua Status</option>
                    <option value="open">Aktif</option>
                    <option value="closed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
                <button type="button" class="rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-muted" @click="applyFilter">Terapkan</button>
            </div>

            <div class="overflow-hidden rounded-xl border border-border/60 bg-card">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[820px] text-sm">
                        <thead>
                            <tr class="border-b border-border/60 text-left text-xs uppercase tracking-wide text-muted-foreground">
                                <th class="w-12 px-5 py-3 font-medium">No.</th>
                                <th class="px-4 py-3 font-medium">Pair</th>
                                <th class="px-4 py-3 font-medium">Arah</th>
                                <th class="px-4 py-3 font-medium">TF</th>
                                <th class="px-4 py-3 font-medium">Penulis</th>
                                <th class="px-4 py-3 text-center font-medium">Status</th>
                                <th class="px-4 py-3 text-center font-medium">Hasil</th>
                                <th class="px-4 py-3 text-right font-medium">PnL%</th>
                                <th class="px-4 py-3 font-medium">Publikasi</th>
                                <th class="px-5 py-3 text-right font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(s, i) in signals.data" :key="s.id" class="border-b border-border/40 last:border-0 hover:bg-muted/40">
                                <td class="px-5 py-3 text-muted-foreground">{{ (signals.from ?? 1) + i }}</td>
                                <td class="px-4 py-3 font-medium">
                                    <span class="inline-flex items-center gap-1.5">
                                        {{ s.pair }}
                                        <Crown v-if="s.is_premium" class="size-3.5 text-amber-500" title="Premium" />
                                    </span>
                                    <span class="ml-1 rounded bg-muted px-1.5 py-0.5 text-[10px] uppercase text-muted-foreground">{{ s.market }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium" :class="s.side === 'long' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400'">
                                        {{ s.side === 'long' ? 'Long' : 'Short' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">{{ s.timeframe ?? '-' }}</td>
                                <td class="px-4 py-3">{{ s.author }}</td>
                                <td class="px-4 py-3 text-center"><span class="rounded-full px-2 py-0.5 text-xs font-medium capitalize" :class="statusClass(s.status)">{{ s.status }}</span></td>
                                <td class="px-4 py-3 text-center"><span v-if="s.result" class="rounded-full px-2 py-0.5 text-xs font-medium" :class="resultClass(s.result)">{{ RESULT[s.result] ?? s.result }}</span><span v-else class="text-muted-foreground">-</span></td>
                                <td class="px-4 py-3 text-right font-semibold" :class="(s.pnl_percent ?? 0) > 0 ? 'text-emerald-600 dark:text-emerald-400' : (s.pnl_percent ?? 0) < 0 ? 'text-rose-600 dark:text-rose-400' : ''">{{ s.pnl_percent !== null ? `${s.pnl_percent > 0 ? '+' : ''}${s.pnl_percent}%` : '-' }}</td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    <span v-if="s.published_at">{{ s.published_at }}</span>
                                    <span v-else class="rounded bg-amber-500/10 px-1.5 py-0.5 text-[10px] text-amber-600 dark:text-amber-400">Draft</span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <Link :href="`/admin/signals/${s.id}/edit`" class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground" title="Edit">
                                            <Pencil class="size-4" />
                                        </Link>
                                        <ConfirmDelete @confirm="destroy(s.id)" />
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!signals.data.length">
                                <td colspan="10" class="px-5 py-10 text-center text-muted-foreground">Belum ada signal. Klik "Buat Signal" untuk menambah.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-if="signals.links.length > 3" class="flex flex-wrap items-center justify-center gap-1">
                <template v-for="(link, i) in signals.links" :key="i">
                    <Link v-if="link.url" :href="link.url" class="rounded-lg border px-3 py-1.5 text-sm" :class="link.active ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-border hover:bg-muted'" v-html="link.label" />
                    <span v-else class="rounded-lg border border-border px-3 py-1.5 text-sm text-muted-foreground opacity-50" v-html="link.label" />
                </template>
            </div>
        </div>
    </AppLayout>
</template>
