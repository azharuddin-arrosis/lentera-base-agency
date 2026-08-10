<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowDownRight, ArrowLeft, ArrowUpRight, Check, ChevronDown, Crown, Info, Plus, Radio, Save, Search, Target, TrendingUp, X } from '@lucide/vue';
import { computed, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    signal: Record<string, unknown> | null;
    members: Array<{ id: number; name: string }>;
    presetMember?: number | null;
}>();

const isEdit = !!props.signal;
const s = props.signal ?? {};

const form = useForm({
    member_id: (s.member_id as number | null) ?? props.presetMember ?? null,
    pair: (s.pair as string) ?? '',
    market: (s.market as string) ?? 'futures',
    side: (s.side as string) ?? 'long',
    timeframe: (s.timeframe as string) ?? '',
    leverage: (s.leverage as string) ?? '',
    entry: (s.entry as number | string) ?? '',
    entry_max: (s.entry_max as number | string) ?? '',
    stop_loss: (s.stop_loss as number | string) ?? '',
    take_profits: ((s.take_profits as Array<number | string>) ?? ['']).map((v) => String(v)) as string[],
    analysis: (s.analysis as string) ?? '',
    status: (s.status as string) ?? 'open',
    result: (s.result as string) ?? '',
    pnl_percent: (s.pnl_percent as number | string) ?? '',
    is_premium: (s.is_premium as boolean) ?? false,
    published_at: (s.published_at as string) ?? '',
});

const addTp = () => form.take_profits.push('');
const removeTp = (i: number) => form.take_profits.splice(i, 1);

const submit = () => {
    const opts = { preserveScroll: true };
    if (isEdit) form.put(`/admin/signals/${s.id}`, opts);
    else form.post('/admin/signals', opts);
};

const authorName = computed(() => props.members.find((m) => m.id === form.member_id)?.name ?? 'Tim Lentera (Admin)');
const previewTps = computed(() => form.take_profits.filter((t) => t !== '' && t !== null));

// ---- Dropdown Penulis (searchable) ----
const authorOpen = ref(false);
const authorSearch = ref('');
const authorOptions = computed(() => [{ id: null as number | null, name: 'Tim Lentera (Admin)' }, ...props.members]);
const filteredAuthors = computed(() => {
    const q = authorSearch.value.trim().toLowerCase();
    if (!q) return authorOptions.value;
    return authorOptions.value.filter((o) => o.name.toLowerCase().includes(q));
});
const chooseAuthor = (id: number | null) => {
    form.member_id = id;
    authorOpen.value = false;
    authorSearch.value = '';
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Signal', href: '/admin/signals' },
    { title: isEdit ? 'Edit' : 'Buat', href: '#' },
];

const inputClass = 'w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20';
const labelClass = 'mb-1.5 block text-sm font-medium';
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="isEdit ? 'Edit Signal' : 'Buat Signal'" />

        <form class="flex flex-col gap-6 p-4 sm:p-6" @submit.prevent="submit">
            <!-- Top bar -->
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <Link href="/admin/signals" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
                        <ArrowLeft class="size-4" /> Kembali
                    </Link>
                    <h1 class="mt-1 flex items-center gap-2 text-2xl font-bold tracking-tight">
                        <Radio class="size-6 text-emerald-500" /> {{ isEdit ? 'Edit Signal' : 'Buat Signal' }}
                    </h1>
                </div>
                <div class="flex items-center gap-2">
                    <Link href="/admin/signals" class="rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-muted">Batal</Link>
                    <button type="submit" :disabled="form.processing" class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-5 py-2 text-sm font-semibold text-white hover:bg-emerald-600 disabled:opacity-60">
                        <Save class="size-4" /> {{ form.processing ? 'Menyimpan...' : 'Simpan Signal' }}
                    </button>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[1fr_380px]">
                <!-- KIRI: form -->
                <div class="flex flex-col gap-6">
                    <!-- Instrumen -->
                    <section class="rounded-2xl border border-border/60 bg-card p-5 sm:p-6">
                        <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Instrumen</h2>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="lg:col-span-1">
                                <label :class="labelClass">Pair <span class="text-rose-500">*</span></label>
                                <input v-model="form.pair" type="text" :class="[inputClass, 'font-semibold uppercase']" placeholder="BTCUSDT" />
                                <p v-if="form.errors.pair" class="mt-1 text-xs text-rose-500">{{ form.errors.pair }}</p>
                            </div>
                            <div>
                                <label :class="labelClass">Penulis</label>
                                <div class="relative">
                                    <button
                                        type="button"
                                        class="flex w-full items-center justify-between gap-2 rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none transition hover:border-emerald-500"
                                        :class="authorOpen && 'border-emerald-500 ring-2 ring-emerald-500/20'"
                                        @click="authorOpen = !authorOpen"
                                    >
                                        <span class="truncate">{{ authorName }}</span>
                                        <ChevronDown class="size-4 shrink-0 text-muted-foreground transition-transform" :class="authorOpen && 'rotate-180'" />
                                    </button>

                                    <div v-if="authorOpen">
                                        <button type="button" class="fixed inset-0 z-40 cursor-default" aria-hidden="true" @click="authorOpen = false"></button>
                                        <div class="absolute left-0 right-0 z-50 mt-1 overflow-hidden rounded-lg border border-border bg-card shadow-xl">
                                            <div class="border-b border-border p-2">
                                                <div class="relative">
                                                    <Search class="pointer-events-none absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                                    <input v-model="authorSearch" type="text" placeholder="Cari nama..." class="w-full rounded-md border border-border bg-background py-1.5 pl-8 pr-2 text-sm outline-none focus:border-emerald-500" />
                                                </div>
                                            </div>
                                            <ul class="max-h-56 overflow-y-auto p-1">
                                                <li v-for="o in filteredAuthors" :key="o.id ?? 'admin'">
                                                    <button
                                                        type="button"
                                                        class="flex w-full items-center justify-between gap-2 rounded-md px-2.5 py-2 text-left text-sm transition hover:bg-muted"
                                                        :class="form.member_id === o.id && 'bg-emerald-500/5'"
                                                        @click="chooseAuthor(o.id)"
                                                    >
                                                        <span class="truncate">{{ o.name }}</span>
                                                        <Check v-if="form.member_id === o.id" class="size-4 shrink-0 text-emerald-500" />
                                                    </button>
                                                </li>
                                                <li v-if="!filteredAuthors.length" class="px-2.5 py-4 text-center text-sm text-muted-foreground">Tidak ditemukan.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label :class="labelClass">Market</label>
                                <select v-model="form.market" :class="inputClass">
                                    <option value="futures">Futures</option>
                                    <option value="spot">Spot</option>
                                </select>
                            </div>
                            <div>
                                <label :class="labelClass">Arah</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-lg border py-2 text-sm font-semibold transition" :class="form.side === 'long' ? 'border-emerald-500 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'border-border text-muted-foreground hover:bg-muted'" @click="form.side = 'long'">
                                        <ArrowUpRight class="size-4" /> Long
                                    </button>
                                    <button type="button" class="inline-flex items-center justify-center gap-1.5 rounded-lg border py-2 text-sm font-semibold transition" :class="form.side === 'short' ? 'border-rose-500 bg-rose-500/10 text-rose-600 dark:text-rose-400' : 'border-border text-muted-foreground hover:bg-muted'" @click="form.side = 'short'">
                                        <ArrowDownRight class="size-4" /> Short
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label :class="labelClass">Timeframe</label>
                                <input v-model="form.timeframe" type="text" :class="inputClass" placeholder="4H, 1D" />
                            </div>
                            <div>
                                <label :class="labelClass">Leverage</label>
                                <input v-model="form.leverage" type="text" :class="inputClass" placeholder="10x" />
                            </div>
                        </div>
                    </section>

                    <!-- Level harga -->
                    <section class="rounded-2xl border border-border/60 bg-card p-5 sm:p-6">
                        <h2 class="mb-4 flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-muted-foreground"><Target class="size-4" /> Level Harga</h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label :class="labelClass">Entry</label>
                                <input v-model="form.entry" type="number" step="0.00000001" :class="inputClass" placeholder="62000" />
                                <p v-if="form.errors.entry" class="mt-1 text-xs text-rose-500">{{ form.errors.entry }}</p>
                            </div>
                            <div>
                                <label :class="labelClass">Entry Maks <span class="text-muted-foreground">(opsional, zona)</span></label>
                                <input v-model="form.entry_max" type="number" step="0.00000001" :class="inputClass" placeholder="62500" />
                            </div>
                            <div class="sm:col-span-2">
                                <label :class="labelClass">Stop Loss</label>
                                <input v-model="form.stop_loss" type="number" step="0.00000001" :class="[inputClass, 'border-rose-500/30 focus:border-rose-500 focus:ring-rose-500/20']" placeholder="60500" />
                            </div>
                            <div class="sm:col-span-2">
                                <label :class="labelClass">Take Profit</label>
                                <div class="space-y-2">
                                    <div v-for="(tp, i) in form.take_profits" :key="i" class="flex items-center gap-2">
                                        <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-lg bg-emerald-500/10 text-xs font-bold text-emerald-600 dark:text-emerald-400">TP{{ i + 1 }}</span>
                                        <input v-model="form.take_profits[i]" type="number" step="0.00000001" :class="[inputClass, 'border-emerald-500/30 focus:border-emerald-500']" :placeholder="`Target ${i + 1}`" />
                                        <button type="button" class="inline-flex size-9 shrink-0 items-center justify-center rounded-lg border border-border text-muted-foreground hover:bg-muted" @click="removeTp(i)">
                                            <X class="size-4" />
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="mt-2 inline-flex items-center gap-1.5 text-sm font-medium text-emerald-600 hover:underline dark:text-emerald-400" @click="addTp">
                                    <Plus class="size-4" /> Tambah target
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Analisa -->
                    <section class="rounded-2xl border border-border/60 bg-card p-5 sm:p-6">
                        <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Analisa</h2>
                        <textarea v-model="form.analysis" rows="5" :class="inputClass" placeholder="Alasan/analisa entry, konfluensi, invalidasi..."></textarea>
                    </section>
                </div>

                <!-- KANAN: publikasi + preview (sticky) -->
                <div class="flex flex-col gap-6">
                    <div class="xl:sticky xl:top-6 flex flex-col gap-6">
                        <!-- Berbagi & publikasi -->
                        <section class="rounded-2xl border border-border/60 bg-card p-5 sm:p-6">
                            <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Berbagi & Publikasi</h2>

                            <!-- Premium / Free -->
                            <label class="mb-2 block text-sm font-medium">Tipe Akses</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" class="rounded-xl border p-3 text-left transition" :class="!form.is_premium ? 'border-emerald-500 bg-emerald-500/5' : 'border-border hover:bg-muted'" @click="form.is_premium = false">
                                    <p class="text-sm font-semibold">Free</p>
                                    <p class="mt-0.5 text-xs text-muted-foreground">Detail terbuka untuk semua</p>
                                </button>
                                <button type="button" class="rounded-xl border p-3 text-left transition" :class="form.is_premium ? 'border-amber-500 bg-amber-500/5' : 'border-border hover:bg-muted'" @click="form.is_premium = true">
                                    <p class="flex items-center gap-1 text-sm font-semibold"><Crown class="size-3.5 text-amber-500" /> Premium</p>
                                    <p class="mt-0.5 text-xs text-muted-foreground">Detail dikunci untuk non-pelanggan</p>
                                </button>
                            </div>

                            <div class="mt-4 grid gap-4">
                                <div>
                                    <label :class="labelClass">Waktu Publikasi</label>
                                    <input v-model="form.published_at" type="datetime-local" :class="inputClass" />
                                    <p class="mt-1 flex items-start gap-1 text-xs text-muted-foreground"><Info class="mt-0.5 size-3 shrink-0" /> Kosongkan untuk simpan sebagai draft (belum tampil ke publik).</p>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label :class="labelClass">Status</label>
                                        <select v-model="form.status" :class="inputClass">
                                            <option value="open">Aktif</option>
                                            <option value="closed">Selesai</option>
                                            <option value="cancelled">Dibatalkan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label :class="labelClass">Hasil</label>
                                        <select v-model="form.result" :class="inputClass">
                                            <option value="">—</option>
                                            <option value="running">Berjalan</option>
                                            <option value="tp_hit">TP Kena</option>
                                            <option value="sl_hit">SL Kena</option>
                                            <option value="breakeven">Breakeven</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <label :class="labelClass">PnL (%)</label>
                                        <input v-model="form.pnl_percent" type="number" step="0.01" :class="inputClass" placeholder="12.5" />
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Preview kartu -->
                        <section class="rounded-2xl border border-dashed border-border/60 bg-muted/20 p-4">
                            <p class="mb-3 text-xs font-medium uppercase tracking-wide text-muted-foreground">Pratinjau kartu</p>
                            <div class="overflow-hidden rounded-2xl border bg-card shadow-sm">
                                <div class="flex items-start justify-between gap-2 border-b p-4">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg font-bold tracking-tight">{{ form.pair || 'PAIR' }}</span>
                                            <Crown v-if="form.is_premium" class="size-4 text-amber-500" />
                                        </div>
                                        <div class="mt-1 flex flex-wrap items-center gap-1.5 text-xs text-muted-foreground">
                                            <span class="rounded bg-muted px-1.5 py-0.5 uppercase">{{ form.market }}</span>
                                            <span v-if="form.timeframe">{{ form.timeframe }}</span>
                                            <span v-if="form.leverage">· {{ form.leverage }}</span>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold" :class="form.side === 'long' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300'">
                                        <ArrowUpRight v-if="form.side === 'long'" class="size-3.5" /><ArrowDownRight v-else class="size-3.5" />
                                        {{ form.side === 'long' ? 'LONG' : 'SHORT' }}
                                    </span>
                                </div>
                                <div class="space-y-2 p-4 text-sm">
                                    <div class="flex items-center justify-between rounded-lg bg-muted/40 px-3 py-2">
                                        <span class="flex items-center gap-1.5 text-muted-foreground"><TrendingUp class="size-4" /> Entry</span>
                                        <span class="font-semibold">{{ form.entry || '—' }}<span v-if="form.entry_max"> – {{ form.entry_max }}</span></span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-lg bg-rose-500/5 px-3 py-2">
                                        <span class="text-rose-600 dark:text-rose-400">Stop Loss</span>
                                        <span class="font-semibold text-rose-600 dark:text-rose-400">{{ form.stop_loss || '—' }}</span>
                                    </div>
                                    <div v-if="previewTps.length" class="rounded-lg bg-emerald-500/5 px-3 py-2">
                                        <span class="mb-1 flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400"><Target class="size-4" /> Target</span>
                                        <span class="flex flex-wrap gap-1.5">
                                            <span v-for="(tp, i) in previewTps" :key="i" class="rounded-md bg-emerald-500/10 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-300">TP{{ i + 1 }}: {{ tp }}</span>
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between border-t pt-2 text-xs text-muted-foreground">
                                        <span>{{ authorName }}</span>
                                        <span v-if="form.is_premium" class="font-medium text-amber-500">Premium</span>
                                        <span v-else class="font-medium text-emerald-600 dark:text-emerald-400">Free</span>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </form>
    </AppLayout>
</template>
