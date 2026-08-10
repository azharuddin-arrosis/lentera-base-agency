<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Lock, LockOpen, Plus, Search } from '@lucide/vue';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatUsd, pnlColor } from '@/lib/format';
import type { BreadcrumbItem } from '@/types';

interface MemberRow {
    id: number; name: string; slug: string; title: string | null; specialization: string | null;
    avatar_url: string | null; is_active: boolean; is_featured: boolean;
    stats: { total_pnl: number; win_rate: number; total_trades: number };
}

const props = defineProps<{
    members: { data: MemberRow[]; links: Array<{ url: string | null; label: string; active: boolean }>; total: number; from: number | null; to: number | null };
    filters: { search: string | null; status: string | null; featured: boolean | null };
}>();

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');
const featured = ref(!!props.filters.featured);

const applyFilter = () => {
    router.get('/admin/members', {
        search: search.value || undefined,
        status: status.value || undefined,
        featured: featured.value || undefined,
    }, { preserveState: true, replace: true });
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Team', href: '/admin/members' },
];

const toggleActive = (id: number) => router.post(`/admin/members/${id}/toggle-active`, {}, { preserveScroll: true });
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Kelola Team" />

        <div class="flex flex-col gap-6 p-4 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Team</h1>
                    <p class="text-sm text-muted-foreground">Konsultan/trader yang tampil di website. Klik "Detail" untuk edit & kelola cuti.</p>
                </div>
                <Link href="/admin/members/create" class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600">
                    <Plus class="size-4" /> Tambah Team
                </Link>
            </div>

            <!-- Filter -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 sm:max-w-xs">
                    <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama, jabatan, spesialisasi..."
                        class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        @keyup.enter="applyFilter"
                    />
                </div>
                <select v-model="status" class="rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500" @change="applyFilter">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
                <label class="inline-flex items-center gap-2 rounded-lg border border-border px-3 py-2 text-sm">
                    <input v-model="featured" type="checkbox" class="size-4 rounded border-border text-emerald-500 focus:ring-emerald-500" @change="applyFilter" />
                    Unggulan saja
                </label>
                <button type="button" class="rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-muted" @click="applyFilter">Terapkan</button>
            </div>

            <div class="overflow-hidden rounded-xl border border-border/60 bg-card">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[720px] text-sm">
                        <thead>
                            <tr class="border-b border-border/60 text-left text-xs uppercase tracking-wide text-muted-foreground">
                                <th class="w-12 px-5 py-3 font-medium">No.</th>
                                <th class="px-4 py-3 font-medium">Team</th>
                                <th class="px-4 py-3 font-medium">Spesialisasi</th>
                                <th class="px-4 py-3 text-right font-medium">PnL</th>
                                <th class="px-4 py-3 text-right font-medium">WR</th>
                                <th class="px-4 py-3 text-center font-medium">Status</th>
                                <th class="px-5 py-3 text-right font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(m, i) in members.data" :key="m.id" class="border-b border-border/40 last:border-0 hover:bg-muted/40" :class="{ 'opacity-60': !m.is_active }">
                                <td class="px-5 py-3 text-muted-foreground">{{ (members.from ?? 1) + i }}</td>
                                <td class="px-4 py-3">
                                    <Link :href="`/admin/members/${m.id}`" class="flex items-center gap-3">
                                        <img :src="m.avatar_url ?? ''" :alt="m.name" class="size-10 rounded-lg bg-muted object-cover" />
                                        <div>
                                            <p class="font-medium hover:text-emerald-600 dark:hover:text-emerald-400">{{ m.name }} <span v-if="m.is_featured" class="ml-1 rounded bg-emerald-500/10 px-1.5 py-0.5 text-[10px] text-emerald-600 dark:text-emerald-400">Unggulan</span></p>
                                            <p class="text-xs text-muted-foreground">{{ m.title }}</p>
                                        </div>
                                    </Link>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">{{ m.specialization ?? '-' }}</td>
                                <td class="px-4 py-3 text-right font-semibold" :class="pnlColor(m.stats.total_pnl)">{{ formatUsd(m.stats.total_pnl, true) }}</td>
                                <td class="px-4 py-3 text-right">{{ m.stats.win_rate }}%</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="m.is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-muted text-muted-foreground'">
                                        {{ m.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-medium transition-colors"
                                            :class="m.is_active ? 'border-border text-muted-foreground hover:bg-amber-500/10 hover:text-amber-600 dark:hover:text-amber-400' : 'border-emerald-500/40 text-emerald-600 hover:bg-emerald-500/10 dark:text-emerald-400'"
                                            :title="m.is_active ? 'Nonaktifkan (lock)' : 'Aktifkan'"
                                            @click="toggleActive(m.id)"
                                        >
                                            <component :is="m.is_active ? Lock : LockOpen" class="size-3.5" />
                                            {{ m.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                        <Link :href="`/admin/members/${m.id}`" class="inline-flex items-center rounded-lg bg-foreground px-3 py-1.5 text-xs font-semibold text-background hover:opacity-90">
                                            Detail
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!members.data.length">
                                <td colspan="7" class="px-5 py-10 text-center text-muted-foreground">Tidak ada team pada filter ini.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="members.links.length > 3" class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-xs text-muted-foreground">Menampilkan {{ members.from ?? 0 }}–{{ members.to ?? 0 }} dari {{ members.total }}</p>
                <div class="flex flex-wrap items-center justify-center gap-1">
                    <template v-for="(link, i) in members.links" :key="i">
                        <Link v-if="link.url" :href="link.url" class="rounded-lg border px-3 py-1.5 text-sm" :class="link.active ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-border hover:bg-muted'" v-html="link.label" preserve-scroll />
                        <span v-else class="rounded-lg border border-border px-3 py-1.5 text-sm text-muted-foreground opacity-50" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
