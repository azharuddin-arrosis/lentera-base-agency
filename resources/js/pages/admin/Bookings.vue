<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { CalendarClock, CalendarX, ChevronRight, Clock, User, Wallet, X } from '@lucide/vue';
import { computed, ref } from 'vue';
import ConfirmDelete from '@/components/admin/ConfirmDelete.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatIdr } from '@/lib/format';
import type { BreadcrumbItem } from '@/types';

interface Booking {
    id: number; member_id: number | null; name: string; email: string; phone: string | null;
    consultant: string; date: string; date_raw: string; times: string[]; hours: number;
    rate: number; total_price: number; notes: string | null; status: string; payment_status: string; is_past: boolean;
}

const props = defineProps<{
    bookings: Booking[];
    members: Array<{ id: number; name: string }>;
    counts: { all: number; pending: number; confirmed: number; done: number; cancelled: number; unpaid: number };
    revenue: number;
    blocks: Array<{ id: number; member_id: number | null; consultant: string; date: string; date_raw: string; times: string[] | null; reason: string | null }>;
    consultants: Array<{ id: string; name: string }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Jadwal Konsultasi', href: '/admin/bookings' },
];

// ---- Filter & tabs ----
const tabs = [
    { key: 'all', label: 'Semua' },
    { key: 'pending', label: 'Menunggu' },
    { key: 'confirmed', label: 'Dikonfirmasi' },
    { key: 'done', label: 'Selesai' },
    { key: 'cancelled', label: 'Dibatalkan' },
] as const;
const activeTab = ref<string>('all');
const consultantFilter = ref<string>('');
const unpaidOnly = ref(false);

const filtered = computed(() =>
    props.bookings.filter((b) => {
        if (activeTab.value !== 'all' && b.status !== activeTab.value) return false;
        if (consultantFilter.value) {
            const key = b.member_id ? String(b.member_id) : 'admin';
            if (key !== consultantFilter.value) return false;
        }
        if (unpaidOnly.value && b.payment_status === 'paid') return false;
        return true;
    }),
);
const tabCount = (key: string) => (key === 'all' ? props.counts.all : (props.counts as Record<string, number>)[key] ?? 0);

const statusClass = (s: string) =>
    s === 'pending' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400'
    : s === 'confirmed' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
    : s === 'done' ? 'bg-sky-500/10 text-sky-600 dark:text-sky-400'
    : 'bg-rose-500/10 text-rose-600 dark:text-rose-400';

// ---- Kosongkan jadwal (block) ----
const slotsForDate = (dateStr: string): string[] => {
    if (!dateStr) return [];
    const wd = new Date(dateStr + 'T00:00:00').getDay();
    if (wd === 0 || wd === 6) return [];
    const all: string[] = [];
    for (let h = 9; h <= 20; h++) all.push(String(h).padStart(2, '0') + ':00');
    const breaks = wd === 5 ? ['11:00', '12:00', '13:00'] : ['12:00'];
    return all.filter((t) => !breaks.includes(t));
};
const blockOpen = ref(false);
const blockForm = useForm({ consultant: 'admin', date: '', full_day: true, times: [] as string[], reason: '' });
const blockSlots = computed(() => slotsForDate(blockForm.date));
const toggleBlockTime = (t: string) => {
    const i = blockForm.times.indexOf(t);
    if (i === -1) blockForm.times.push(t); else blockForm.times.splice(i, 1);
};
const submitBlock = () => {
    blockForm.post('/admin/schedule-blocks', {
        preserveScroll: true,
        onSuccess: () => { blockOpen.value = false; blockForm.reset(); blockForm.consultant = 'admin'; blockForm.full_day = true; },
    });
};
const deleteBlock = (id: number) => router.delete(`/admin/schedule-blocks/${id}`, { preserveScroll: true });

const inputClass = 'w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20';
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Jadwal Konsultasi" />

        <div class="flex flex-col gap-5 p-4 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Jadwal Konsultasi</h1>
                    <p class="text-sm text-muted-foreground">Klik satu jadwal untuk lihat detail, konfirmasi bayar, reschedule, & rating.</p>
                </div>
                <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-muted" @click="blockOpen = true">
                    <CalendarX class="size-4" /> Kosongkan Jadwal
                </button>
            </div>

            <!-- KPI -->
            <div class="grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-border/60 bg-card p-4">
                    <p class="text-xs uppercase tracking-wide text-muted-foreground">Pendapatan (Lunas)</p>
                    <p class="mt-1 text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ formatIdr(revenue) }}</p>
                </div>
                <div class="rounded-xl border border-border/60 bg-card p-4">
                    <p class="text-xs uppercase tracking-wide text-muted-foreground">Menunggu Konfirmasi</p>
                    <p class="mt-1 text-xl font-bold">{{ counts.pending }}</p>
                </div>
                <div class="rounded-xl border border-border/60 bg-card p-4">
                    <p class="text-xs uppercase tracking-wide text-muted-foreground">Belum Lunas</p>
                    <p class="mt-1 text-xl font-bold text-amber-600 dark:text-amber-400">{{ counts.unpaid }}</p>
                </div>
            </div>

            <!-- Tabs + filter -->
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap gap-1 rounded-xl border border-border/60 bg-card p-1">
                    <button v-for="t in tabs" :key="t.key" type="button" class="rounded-lg px-3 py-1.5 text-sm font-medium transition-colors" :class="activeTab === t.key ? 'bg-emerald-500 text-white' : 'text-muted-foreground hover:bg-muted'" @click="activeTab = t.key">
                        {{ t.label }} <span class="ml-1 text-xs opacity-70">{{ tabCount(t.key) }}</span>
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <select v-model="consultantFilter" class="rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500">
                        <option value="">Semua konsultan</option>
                        <option v-for="c in consultants" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-border px-3 py-2 text-sm">
                        <input v-model="unpaidOnly" type="checkbox" class="size-4 rounded border-border text-emerald-500 focus:ring-emerald-500" /> Belum lunas
                    </label>
                </div>
            </div>

            <!-- Daftar booking (klik → detail) -->
            <div class="grid gap-3">
                <Link v-for="b in filtered" :key="b.id" :href="`/admin/bookings/${b.id}`" class="flex items-center justify-between gap-4 rounded-xl border border-border/60 bg-card p-4 transition-colors hover:border-emerald-500/40 hover:bg-muted/30" :class="{ 'opacity-60': b.is_past }">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 text-sm font-semibold"><CalendarClock class="size-4 text-emerald-500" /> {{ b.date }}</span>
                            <span class="inline-flex items-center gap-1 text-xs text-muted-foreground"><Clock class="size-3.5" /> {{ b.times.join(', ') }}</span>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-medium capitalize" :class="statusClass(b.status)">{{ b.status }}</span>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-medium" :class="b.payment_status === 'paid' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : b.payment_status === 'awaiting' ? 'bg-sky-500/10 text-sky-600 dark:text-sky-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400'">{{ b.payment_status === 'paid' ? 'Lunas' : b.payment_status === 'awaiting' ? 'Perlu verifikasi' : 'Belum bayar' }}</span>
                        </div>
                        <div class="mt-1 flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-muted-foreground">
                            <span class="font-medium text-foreground">{{ b.name }}</span>
                            <span class="inline-flex items-center gap-1"><User class="size-3.5" /> {{ b.consultant }}</span>
                            <span class="inline-flex items-center gap-1 font-medium text-emerald-600 dark:text-emerald-400"><Wallet class="size-3.5" /> {{ formatIdr(b.total_price) }}</span>
                        </div>
                    </div>
                    <ChevronRight class="size-5 shrink-0 text-muted-foreground" />
                </Link>
                <div v-if="!filtered.length" class="rounded-xl border border-dashed border-border/60 py-16 text-center text-muted-foreground">Tidak ada jadwal pada filter ini.</div>
            </div>

            <!-- Blok jadwal aktif -->
            <div v-if="blocks.length" class="rounded-xl border border-border/60 bg-card">
                <div class="border-b border-border/60 px-5 py-3">
                    <h3 class="font-semibold">Jadwal Dikosongkan</h3>
                    <p class="text-xs text-muted-foreground">Slot ini tidak bisa dipesan pengunjung.</p>
                </div>
                <ul class="divide-y divide-border/40">
                    <li v-for="blk in blocks" :key="blk.id" class="flex items-center justify-between px-5 py-3 text-sm">
                        <div>
                            <p class="font-medium">{{ blk.consultant }} · {{ blk.date }}</p>
                            <p class="text-xs text-muted-foreground">{{ blk.times && blk.times.length ? blk.times.join(', ') + ' WIB' : 'Seharian penuh' }}<span v-if="blk.reason"> · {{ blk.reason }}</span></p>
                        </div>
                        <ConfirmDelete label="Hapus blokir?" @confirm="deleteBlock(blk.id)" />
                    </li>
                </ul>
            </div>
        </div>

        <!-- ===== Modal Kosongkan Jadwal ===== -->
        <teleport to="body">
            <div v-if="blockOpen" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4" @click.self="blockOpen = false">
                <div class="w-full max-w-md rounded-2xl border border-border/60 bg-card p-6 shadow-xl">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold">Kosongkan Jadwal</h3>
                        <button type="button" class="rounded-lg p-1 text-muted-foreground hover:bg-muted" @click="blockOpen = false"><X class="size-4" /></button>
                    </div>
                    <div class="mt-4 space-y-3">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Konsultan</label>
                            <select v-model="blockForm.consultant" :class="inputClass">
                                <option v-for="c in consultants" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Tanggal</label>
                            <input v-model="blockForm.date" type="date" :class="inputClass" @change="blockForm.times = []" />
                            <p v-if="blockForm.errors.date" class="mt-1 text-xs text-rose-500">{{ blockForm.errors.date }}</p>
                        </div>
                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input v-model="blockForm.full_day" type="checkbox" class="size-4 rounded border-border text-emerald-500 focus:ring-emerald-500" /> Seharian penuh
                        </label>
                        <div v-if="!blockForm.full_day">
                            <label class="mb-1.5 block text-sm font-medium">Pilih jam yang dikosongkan</label>
                            <div v-if="blockSlots.length" class="grid grid-cols-4 gap-2">
                                <button v-for="t in blockSlots" :key="t" type="button" class="rounded-lg border py-1.5 text-xs font-medium transition-colors" :class="blockForm.times.includes(t) ? 'border-rose-500 bg-rose-500 text-white' : 'border-border hover:bg-muted'" @click="toggleBlockTime(t)">{{ t }}</button>
                            </div>
                            <p v-else class="text-xs text-muted-foreground">Pilih tanggal hari kerja dulu.</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Alasan (opsional)</label>
                            <input v-model="blockForm.reason" type="text" :class="inputClass" placeholder="mis. Cuti / acara" />
                        </div>
                    </div>
                    <div class="mt-5 flex justify-end gap-2">
                        <button type="button" class="rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-muted" @click="blockOpen = false">Batal</button>
                        <button type="button" :disabled="blockForm.processing || !blockForm.date" class="rounded-lg bg-rose-500 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-600 disabled:opacity-50" @click="submitBlock">Kosongkan</button>
                    </div>
                </div>
            </div>
        </teleport>
    </AppLayout>
</template>
