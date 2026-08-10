<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { CalendarDays, Pencil, TrendingUp, Wallet, X } from '@lucide/vue';
import { computed, ref } from 'vue';
import ConfirmDelete from '@/components/admin/ConfirmDelete.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatUsd, pnlColor } from '@/lib/format';
import type { BreadcrumbItem } from '@/types';

type Withdrawal = {
    id: number;
    member_id: number;
    member: string | null;
    amount: number;
    withdrawn_at: string | null;
    withdrawn_label: string | null;
    method: string | null;
    note: string | null;
};

const props = defineProps<{
    members: Array<{ id: number; name: string }>;
    filters: { member: number | null };
    summary: {
        total_pnl: number;
        total_withdrawn: number;
        remaining: number;
        trading_days: number;
        profit_days: number;
        best_day: number;
        worst_day: number;
        avg_daily: number;
        withdraw_count: number;
    };
    daily: Array<{ date: string; label: string; pnl: number; trades: number }>;
    withdrawals: {
        data: Withdrawal[];
        links: Array<{ url: string | null; label: string; active: boolean }>;
        from: number | null;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Financial Plan', href: '/admin/financial-plan' },
];

// ── Filter member ──
const memberFilter = ref(props.filters.member ? String(props.filters.member) : '');
const applyFilter = () => {
    router.get('/admin/financial-plan', { member: memberFilter.value || undefined }, { preserveState: true, replace: true });
};

// ── Form catat / edit withdrawal ──
const today = new Date().toISOString().slice(0, 10);
const defaultMember = props.filters.member ?? props.members[0]?.id ?? null;

const editingId = ref<number | null>(null);
const form = useForm({
    member_id: defaultMember,
    amount: null as number | null,
    withdrawn_at: today,
    method: '',
    note: '',
});

const submit = () => {
    if (editingId.value) {
        form.put(`/admin/financial-plan/withdrawals/${editingId.value}`, {
            preserveScroll: true,
            onSuccess: resetForm,
        });
    } else {
        form.post('/admin/financial-plan/withdrawals', {
            preserveScroll: true,
            onSuccess: resetForm,
        });
    }
};

const startEdit = (w: Withdrawal) => {
    editingId.value = w.id;
    form.member_id = w.member_id;
    form.amount = w.amount;
    form.withdrawn_at = w.withdrawn_at ?? today;
    form.method = w.method ?? '';
    form.note = w.note ?? '';
};

const resetForm = () => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    form.member_id = defaultMember;
    form.withdrawn_at = today;
};

const destroy = (id: number) => router.delete(`/admin/financial-plan/withdrawals/${id}`, { preserveScroll: true });

const inputClass =
    'w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20';

const profitRatio = computed(() =>
    props.summary.trading_days > 0 ? Math.round((props.summary.profit_days / props.summary.trading_days) * 100) : 0,
);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Financial Plan" />

        <div class="flex flex-col gap-6 p-4 sm:p-6">
            <!-- Header + filter -->
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Financial Plan</h1>
                    <p class="text-sm text-muted-foreground">Rekap trading: total ditarik (WD) &amp; PnL harian (otomatis dari data trade).</p>
                </div>
                <select v-model="memberFilter" :class="inputClass" class="w-auto" @change="applyFilter">
                    <option value="">Semua Konsultan</option>
                    <option v-for="m in members" :key="m.id" :value="String(m.id)">{{ m.name }}</option>
                </select>
            </div>

            <!-- KPI -->
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div class="rounded-xl border border-border/60 bg-card p-4">
                    <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-muted-foreground">
                        <TrendingUp class="size-4" /> Total PnL Realized
                    </div>
                    <p class="mt-2 text-2xl font-bold" :class="pnlColor(summary.total_pnl)">{{ formatUsd(summary.total_pnl, true) }}</p>
                    <p class="mt-1 text-xs text-muted-foreground">Akumulasi seluruh trade closed</p>
                </div>
                <div class="rounded-xl border border-border/60 bg-card p-4">
                    <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-muted-foreground">
                        <Wallet class="size-4" /> Total Ditarik (WD)
                    </div>
                    <p class="mt-2 text-2xl font-bold text-foreground">{{ formatUsd(summary.total_withdrawn) }}</p>
                    <p class="mt-1 text-xs text-muted-foreground">{{ summary.withdraw_count }}x penarikan tercatat</p>
                </div>
                <div class="rounded-xl border border-border/60 bg-card p-4">
                    <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-muted-foreground">
                        <TrendingUp class="size-4" /> Belum Ditarik
                    </div>
                    <p class="mt-2 text-2xl font-bold" :class="pnlColor(summary.remaining)">{{ formatUsd(summary.remaining) }}</p>
                    <p class="mt-1 text-xs text-muted-foreground">Profit yang masih di akun</p>
                </div>
                <div class="rounded-xl border border-border/60 bg-card p-4">
                    <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-muted-foreground">
                        <CalendarDays class="size-4" /> Hari Profit
                    </div>
                    <p class="mt-2 text-2xl font-bold text-foreground">{{ summary.profit_days }}<span class="text-base font-medium text-muted-foreground">/{{ summary.trading_days }}</span></p>
                    <p class="mt-1 text-xs text-muted-foreground">{{ profitRatio }}% · rata-rata {{ formatUsd(summary.avg_daily, true) }}/hari</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-5">
                <!-- Rekap PnL harian -->
                <div class="lg:col-span-3">
                    <div class="overflow-hidden rounded-xl border border-border/60 bg-card">
                        <div class="flex items-center justify-between border-b border-border/60 px-5 py-3">
                            <h2 class="text-sm font-semibold">Rekap PnL Harian</h2>
                            <span class="text-xs text-muted-foreground">Terbaik {{ formatUsd(summary.best_day, true) }} · Terburuk {{ formatUsd(summary.worst_day, true) }}</span>
                        </div>
                        <div class="max-h-[520px] overflow-y-auto">
                            <table class="w-full text-sm">
                                <thead class="sticky top-0 bg-card">
                                    <tr class="border-b border-border/60 text-left text-xs uppercase tracking-wide text-muted-foreground">
                                        <th class="w-12 px-5 py-3 font-medium">No.</th>
                                        <th class="px-4 py-3 font-medium">Tanggal</th>
                                        <th class="px-4 py-3 text-center font-medium">Trade</th>
                                        <th class="px-5 py-3 text-right font-medium">PnL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(d, i) in daily" :key="d.date" class="border-b border-border/40 last:border-0 hover:bg-muted/40">
                                        <td class="px-5 py-3 text-muted-foreground">{{ i + 1 }}</td>
                                        <td class="px-4 py-3 font-medium">{{ d.label }}</td>
                                        <td class="px-4 py-3 text-center text-muted-foreground">{{ d.trades }}</td>
                                        <td class="px-5 py-3 text-right font-semibold" :class="pnlColor(d.pnl)">{{ formatUsd(d.pnl, true) }}</td>
                                    </tr>
                                    <tr v-if="!daily.length">
                                        <td colspan="4" class="px-5 py-10 text-center text-muted-foreground">Belum ada trade closed untuk dihitung.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Withdrawal -->
                <div class="lg:col-span-2">
                    <div class="rounded-xl border border-border/60 bg-card">
                        <div class="flex items-center justify-between border-b border-border/60 px-5 py-3">
                            <h2 class="text-sm font-semibold">{{ editingId ? 'Edit Withdrawal' : 'Catat Withdrawal' }}</h2>
                            <button v-if="editingId" type="button" class="inline-flex items-center gap-1 text-xs text-muted-foreground hover:text-foreground" @click="resetForm">
                                <X class="size-3.5" /> Batal
                            </button>
                        </div>

                        <form class="space-y-3 p-5" @submit.prevent="submit">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-muted-foreground">Konsultan</label>
                                <select v-model="form.member_id" :class="inputClass">
                                    <option v-for="m in members" :key="m.id" :value="m.id">{{ m.name }}</option>
                                </select>
                                <p v-if="form.errors.member_id" class="mt-1 text-xs text-rose-500">{{ form.errors.member_id }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-muted-foreground">Jumlah (USD)</label>
                                    <input v-model.number="form.amount" type="number" step="0.01" min="0" placeholder="0.00" :class="inputClass" />
                                    <p v-if="form.errors.amount" class="mt-1 text-xs text-rose-500">{{ form.errors.amount }}</p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-muted-foreground">Tanggal</label>
                                    <input v-model="form.withdrawn_at" type="date" :class="inputClass" />
                                    <p v-if="form.errors.withdrawn_at" class="mt-1 text-xs text-rose-500">{{ form.errors.withdrawn_at }}</p>
                                </div>
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-muted-foreground">Metode <span class="font-normal">(opsional)</span></label>
                                <input v-model="form.method" type="text" placeholder="mis. USDT TRC20 / Bank BCA" :class="inputClass" />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-muted-foreground">Catatan <span class="font-normal">(opsional)</span></label>
                                <textarea v-model="form.note" rows="2" :class="inputClass" />
                            </div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-emerald-600 disabled:opacity-60"
                            >
                                {{ editingId ? 'Simpan Perubahan' : 'Catat Withdrawal' }}
                            </button>
                        </form>

                        <!-- Riwayat -->
                        <div class="border-t border-border/60">
                            <div class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Riwayat Penarikan</div>
                            <ul class="divide-y divide-border/40">
                                <li v-for="w in withdrawals.data" :key="w.id" class="flex items-start justify-between gap-3 px-5 py-3">
                                    <div class="min-w-0">
                                        <p class="font-semibold">{{ formatUsd(w.amount) }}</p>
                                        <p class="truncate text-xs text-muted-foreground">
                                            {{ w.withdrawn_label }} · {{ w.member }}<span v-if="w.method"> · {{ w.method }}</span>
                                        </p>
                                        <p v-if="w.note" class="mt-0.5 truncate text-xs text-muted-foreground/80">{{ w.note }}</p>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-1">
                                        <button type="button" class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground" title="Edit" @click="startEdit(w)">
                                            <Pencil class="size-4" />
                                        </button>
                                        <ConfirmDelete @confirm="destroy(w.id)" />
                                    </div>
                                </li>
                                <li v-if="!withdrawals.data.length" class="px-5 py-8 text-center text-sm text-muted-foreground">Belum ada withdrawal tercatat.</li>
                            </ul>

                            <div v-if="withdrawals.links.length > 3" class="flex flex-wrap items-center justify-center gap-1 border-t border-border/60 px-5 py-3">
                                <template v-for="(link, i) in withdrawals.links" :key="i">
                                    <Link
                                        v-if="link.url"
                                        :href="link.url"
                                        class="rounded-lg border px-2.5 py-1 text-xs"
                                        :class="link.active ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-border hover:bg-muted'"
                                        preserve-scroll
                                        v-html="link.label"
                                    />
                                    <span v-else class="rounded-lg border border-border px-2.5 py-1 text-xs text-muted-foreground opacity-50" v-html="link.label" />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
