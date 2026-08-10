<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    trade: Record<string, unknown> | null;
    members: Array<{ id: number; name: string }>;
}>();

const isEdit = !!props.trade;
const t = props.trade ?? {};

const nowLocal = () => {
    const d = new Date();
    d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
    return d.toISOString().slice(0, 16);
};

const form = useForm({
    member_id: (t.member_id as number) ?? (props.members[0]?.id ?? ''),
    symbol: (t.symbol as string) ?? '',
    market: (t.market as string) ?? 'futures',
    side: (t.side as string) ?? 'long',
    leverage: (t.leverage as number) ?? '',
    entry_price: (t.entry_price as number) ?? '',
    exit_price: (t.exit_price as number) ?? '',
    quantity: (t.quantity as number) ?? '',
    pnl: (t.pnl as number) ?? '',
    roi: (t.roi as number) ?? '',
    fee: (t.fee as number) ?? '',
    status: (t.status as string) ?? 'closed',
    source: (t.source as string) ?? 'manual',
    notes: (t.notes as string) ?? '',
    opened_at: (t.opened_at as string) ?? '',
    closed_at: (t.closed_at as string) ?? nowLocal(),
});

const submit = () => {
    if (isEdit) {
        form.put(`/admin/trades/${t.id}`);
    } else {
        form.post('/admin/trades');
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Trade & PnL', href: '/admin/trades' },
    { title: isEdit ? 'Edit' : 'Input', href: '#' },
];

const inputClass = 'w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20';
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="isEdit ? 'Edit Trade' : 'Input PnL'" />

        <div class="flex w-full flex-col gap-6 p-4 sm:p-6">
            <Link href="/admin/trades" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="size-4" /> Kembali
            </Link>

            <div>
                <h1 class="text-2xl font-bold tracking-tight">{{ isEdit ? 'Edit Trade' : 'Input PnL Manual' }}</h1>
                <p class="text-sm text-muted-foreground">Catat transaksi/PnL yang akan tampil di profil & track record.</p>
            </div>

            <form class="space-y-5" @submit.prevent="submit">
                <div class="rounded-xl border border-border/60 bg-card p-5 sm:p-6">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Member <span class="text-rose-500">*</span></label>
                            <select v-model="form.member_id" :class="inputClass">
                                <option v-for="mm in members" :key="mm.id" :value="mm.id">{{ mm.name }}</option>
                            </select>
                            <p v-if="form.errors.member_id" class="mt-1 text-xs text-rose-500">{{ form.errors.member_id }}</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Simbol <span class="text-rose-500">*</span></label>
                            <input v-model="form.symbol" type="text" :class="inputClass" placeholder="BTCUSDT" />
                            <p v-if="form.errors.symbol" class="mt-1 text-xs text-rose-500">{{ form.errors.symbol }}</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Market</label>
                            <select v-model="form.market" :class="inputClass">
                                <option value="futures">Futures</option>
                                <option value="spot">Spot</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Arah</label>
                            <select v-model="form.side" :class="inputClass">
                                <option value="long">Long</option>
                                <option value="short">Short</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Leverage</label>
                            <input v-model="form.leverage" type="number" step="0.01" :class="inputClass" placeholder="10" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Quantity</label>
                            <input v-model="form.quantity" type="number" step="0.00000001" :class="inputClass" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Entry Price</label>
                            <input v-model="form.entry_price" type="number" step="0.00000001" :class="inputClass" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Exit Price</label>
                            <input v-model="form.exit_price" type="number" step="0.00000001" :class="inputClass" />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-border/60 bg-card p-5 sm:p-6">
                    <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Hasil</h3>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">PnL (USDT) <span class="text-rose-500">*</span></label>
                            <input v-model="form.pnl" type="number" step="0.01" :class="inputClass" placeholder="Positif = profit, negatif = loss" />
                            <p v-if="form.errors.pnl" class="mt-1 text-xs text-rose-500">{{ form.errors.pnl }}</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">ROI (%)</label>
                            <input v-model="form.roi" type="number" step="0.01" :class="inputClass" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Fee (USDT)</label>
                            <input v-model="form.fee" type="number" step="0.01" :class="inputClass" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Status</label>
                            <select v-model="form.status" :class="inputClass">
                                <option value="closed">Closed</option>
                                <option value="open">Open</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Sumber</label>
                            <select v-model="form.source" :class="inputClass">
                                <option value="manual">Manual</option>
                                <option value="binance">Binance</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-border/60 bg-card p-5 sm:p-6">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Waktu Buka</label>
                            <input v-model="form.opened_at" type="datetime-local" :class="inputClass" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Waktu Tutup</label>
                            <input v-model="form.closed_at" type="datetime-local" :class="inputClass" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium">Catatan</label>
                            <textarea v-model="form.notes" rows="3" :class="inputClass" placeholder="Analisis / alasan entry-exit"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <Link href="/admin/trades" class="rounded-lg border border-border px-5 py-2.5 text-sm font-medium hover:bg-muted">Batal</Link>
                    <button type="submit" :disabled="form.processing" class="rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-600 disabled:opacity-60">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
