<script setup lang="ts">
import { ChevronLeft, ChevronRight, Search, X } from '@lucide/vue';
import { computed, ref } from 'vue';
import { formatPercent, formatUsd, pnlColor } from '@/lib/format';

interface Trade {
    id: number;
    member: string;
    member_slug: string;
    symbol: string;
    side: string;
    leverage: number | null;
    pnl: number;
    roi: number | null;
    date: string | null; // Y-m-d
    closed_at: string | null;
}

const props = withDefaults(defineProps<{ trades: Trade[]; authed?: boolean }>(), { authed: false });
const maskClass = computed(() => (props.authed ? '' : 'blur-[4px] select-none'));

const WEEKDAYS = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

// Kelompokkan trade per tanggal
const byDate = computed(() => {
    const map = new Map<string, Trade[]>();
    for (const t of props.trades) {
        if (!t.date) continue;
        if (!map.has(t.date)) map.set(t.date, []);
        map.get(t.date)!.push(t);
    }
    return map;
});

const dailyTotal = (date: string) => {
    const list = byDate.value.get(date);
    if (!list) return null;
    return { pnl: list.reduce((s, t) => s + t.pnl, 0), count: list.length };
};

// Bulan yang sedang ditampilkan (default: bulan trade terbaru, atau bulan ini)
const latestDate = computed(() => {
    const dates = props.trades.map((t) => t.date).filter(Boolean).sort() as string[];
    return dates.length ? dates[dates.length - 1] : null;
});

const init = latestDate.value ? new Date(latestDate.value + 'T00:00:00') : new Date();
const viewYear = ref(init.getFullYear());
const viewMonth = ref(init.getMonth()); // 0-based

const monthLabel = computed(() =>
    new Date(viewYear.value, viewMonth.value, 1).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' }),
);

const prevMonth = () => {
    if (viewMonth.value === 0) {
        viewMonth.value = 11;
        viewYear.value--;
    } else {
        viewMonth.value--;
    }
};
const nextMonth = () => {
    if (viewMonth.value === 11) {
        viewMonth.value = 0;
        viewYear.value++;
    } else {
        viewMonth.value++;
    }
};

const pad = (n: number) => String(n).padStart(2, '0');

// Sel kalender (Senin sebagai awal minggu)
const cells = computed(() => {
    const y = viewYear.value;
    const m = viewMonth.value;
    const firstDow = new Date(y, m, 1).getDay(); // 0=Min
    const offset = (firstDow + 6) % 7; // jadikan Senin=0
    const daysInMonth = new Date(y, m + 1, 0).getDate();

    const out: Array<{ date: string | null; day: number | null }> = [];
    for (let i = 0; i < offset; i++) out.push({ date: null, day: null });
    for (let d = 1; d <= daysInMonth; d++) {
        out.push({ date: `${y}-${pad(m + 1)}-${pad(d)}`, day: d });
    }
    while (out.length % 7 !== 0) out.push({ date: null, day: null });
    return out;
});

const monthSummary = computed(() => {
    let pnl = 0;
    let count = 0;
    const prefix = `${viewYear.value}-${pad(viewMonth.value + 1)}`;
    for (const [date, list] of byDate.value) {
        if (date.startsWith(prefix)) {
            pnl += list.reduce((s, t) => s + t.pnl, 0);
            count += list.length;
        }
    }
    return { pnl, count };
});

// ---- Modal ----
const selectedDate = ref<string | null>(null);
const search = ref('');
const page = ref(1);
const perPage = 6;

const openDay = (date: string | null) => {
    if (!date || !byDate.value.has(date)) return;
    selectedDate.value = date;
    search.value = '';
    page.value = 1;
};
const closeModal = () => (selectedDate.value = null);

const selectedLabel = computed(() =>
    selectedDate.value ? new Date(selectedDate.value + 'T00:00:00').toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) : '',
);

const dayTradesAll = computed(() => (selectedDate.value ? byDate.value.get(selectedDate.value) ?? [] : []));
const daySummary = computed(() => ({
    pnl: dayTradesAll.value.reduce((s, t) => s + t.pnl, 0),
    count: dayTradesAll.value.length,
}));

const filtered = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (!q) return dayTradesAll.value;
    return dayTradesAll.value.filter(
        (t) => t.symbol.toLowerCase().includes(q) || t.member.toLowerCase().includes(q) || t.side.toLowerCase().includes(q),
    );
});

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage)));
const paged = computed(() => {
    const start = (page.value - 1) * perPage;
    return filtered.value.slice(start, start + perPage);
});

const goPage = (p: number) => {
    if (p >= 1 && p <= totalPages.value) page.value = p;
};
</script>

<template>
    <div>
        <!-- Header kalender -->
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold">Kalender PnL Harian</h3>
                <p class="text-xs text-muted-foreground">Klik tanggal untuk melihat detail transaksi hari itu.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="inline-flex size-8 items-center justify-center rounded-lg border border-border hover:bg-muted" @click="prevMonth"><ChevronLeft class="size-4" /></button>
                <span class="min-w-[130px] text-center text-sm font-semibold capitalize">{{ monthLabel }}</span>
                <button type="button" class="inline-flex size-8 items-center justify-center rounded-lg border border-border hover:bg-muted" @click="nextMonth"><ChevronRight class="size-4" /></button>
            </div>
        </div>

        <!-- Ringkasan bulan -->
        <div class="mt-3 flex flex-wrap gap-4 rounded-lg bg-muted/40 px-4 py-2 text-sm">
            <span>PnL bulan ini: <span class="font-semibold" :class="[pnlColor(monthSummary.pnl), maskClass]">{{ formatUsd(monthSummary.pnl, true) }}</span><span v-if="!authed" class="ml-1 text-xs text-muted-foreground">🔒</span></span>
            <span class="text-muted-foreground">{{ monthSummary.count }} transaksi</span>
        </div>

        <!-- Grid -->
        <div class="mt-4 grid grid-cols-7 gap-1.5">
            <div v-for="d in WEEKDAYS" :key="d" class="pb-1 text-center text-xs font-medium text-muted-foreground">{{ d }}</div>

            <template v-for="(cell, i) in cells" :key="i">
                <div v-if="!cell.date" class="aspect-square rounded-lg"></div>
                <button
                    v-else
                    type="button"
                    class="flex aspect-square flex-col items-center justify-center rounded-lg border p-1 text-center transition-colors"
                    :class="[
                        dailyTotal(cell.date)
                            ? (dailyTotal(cell.date)!.pnl >= 0
                                ? 'border-emerald-500/30 bg-emerald-500/10 hover:bg-emerald-500/20 cursor-pointer'
                                : 'border-rose-500/30 bg-rose-500/10 hover:bg-rose-500/20 cursor-pointer')
                            : 'border-border/50 text-muted-foreground',
                    ]"
                    :disabled="!dailyTotal(cell.date)"
                    @click="openDay(cell.date)"
                >
                    <span class="text-xs font-medium">{{ cell.day }}</span>
                    <span v-if="dailyTotal(cell.date)" class="mt-0.5 text-[10px] font-semibold leading-tight sm:text-xs" :class="[pnlColor(dailyTotal(cell.date)!.pnl), maskClass]">
                        {{ formatUsd(dailyTotal(cell.date)!.pnl, true) }}
                    </span>
                    <span v-if="dailyTotal(cell.date)" class="text-[9px] text-muted-foreground">{{ dailyTotal(cell.date)!.count }} trade</span>
                </button>
            </template>
        </div>

        <!-- Legend -->
        <div class="mt-4 flex items-center justify-center gap-4 text-xs text-muted-foreground">
            <span class="inline-flex items-center gap-1.5"><span class="size-3 rounded bg-emerald-500/40"></span> Profit</span>
            <span class="inline-flex items-center gap-1.5"><span class="size-3 rounded bg-rose-500/40"></span> Loss</span>
        </div>

        <!-- MODAL -->
        <teleport to="body">
            <transition
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="opacity-0"
                leave-active-class="transition duration-100 ease-in"
                leave-to-class="opacity-0"
            >
                <div v-if="selectedDate" class="fixed inset-0 z-[100] flex items-end justify-center bg-black/50 p-0 sm:items-center sm:p-4" @click.self="closeModal">
                    <div class="flex max-h-[90vh] w-full max-w-lg flex-col overflow-hidden rounded-t-2xl border border-border/60 bg-card shadow-xl sm:rounded-2xl">
                        <!-- Header -->
                        <div class="flex items-start justify-between gap-3 border-b border-border/60 p-5">
                            <div>
                                <h4 class="font-semibold capitalize">{{ selectedLabel }}</h4>
                                <div class="mt-1 flex items-center gap-3 text-sm">
                                    <span class="font-semibold" :class="[pnlColor(daySummary.pnl), maskClass]">{{ formatUsd(daySummary.pnl, true) }}</span>
                                    <span v-if="!authed" class="text-xs text-muted-foreground">🔒 login untuk lihat</span>
                                    <span class="text-muted-foreground">{{ daySummary.count }} transaksi</span>
                                </div>
                            </div>
                            <button type="button" class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground hover:bg-muted" @click="closeModal"><X class="size-4" /></button>
                        </div>

                        <!-- Search -->
                        <div class="border-b border-border/60 p-3">
                            <div class="relative">
                                <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Cari simbol, konsultan, atau arah..."
                                    class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                                    @input="page = 1"
                                />
                            </div>
                        </div>

                        <!-- List -->
                        <div class="flex-1 overflow-y-auto">
                            <ul class="divide-y divide-border/40">
                                <li v-for="t in paged" :key="t.id" class="flex items-center justify-between gap-3 px-5 py-3">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium">{{ t.symbol }}</span>
                                            <span v-if="t.leverage" class="rounded bg-muted px-1.5 py-0.5 text-[10px] text-muted-foreground">{{ t.leverage }}x</span>
                                            <span class="rounded-full px-2 py-0.5 text-[10px] font-medium" :class="t.side === 'long' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400'">
                                                {{ t.side === 'long' ? 'Long' : 'Short' }}
                                            </span>
                                        </div>
                                        <p class="truncate text-xs text-muted-foreground">{{ t.member }}<span v-if="t.roi !== null"> · ROI {{ formatPercent(t.roi, true) }}</span></p>
                                    </div>
                                    <span class="shrink-0 font-semibold" :class="[pnlColor(t.pnl), maskClass]">{{ formatUsd(t.pnl, true) }}</span>
                                </li>
                                <li v-if="!filtered.length" class="px-5 py-10 text-center text-sm text-muted-foreground">Tidak ada transaksi yang cocok.</li>
                            </ul>
                        </div>

                        <!-- Pagination -->
                        <div v-if="totalPages > 1" class="flex items-center justify-between border-t border-border/60 px-5 py-3 text-sm">
                            <button type="button" class="inline-flex items-center gap-1 rounded-lg border border-border px-3 py-1.5 disabled:opacity-40" :disabled="page === 1" @click="goPage(page - 1)">
                                <ChevronLeft class="size-4" /> Sebelumnya
                            </button>
                            <span class="text-muted-foreground">Hal {{ page }} / {{ totalPages }}</span>
                            <button type="button" class="inline-flex items-center gap-1 rounded-lg border border-border px-3 py-1.5 disabled:opacity-40" :disabled="page === totalPages" @click="goPage(page + 1)">
                                Berikutnya <ChevronRight class="size-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </transition>
        </teleport>
    </div>
</template>
