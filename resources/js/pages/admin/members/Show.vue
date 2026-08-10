<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Award, CalendarCheck, CalendarX, ChevronRight, CircleDollarSign, Clock, Crown, Hourglass, Lock, LockOpen, Mail, MapPin, Pencil, Phone, Plus, Radio, TrendingUp, Users, Wallet } from '@lucide/vue';
import { computed, ref } from 'vue';
import ConfirmDelete from '@/components/admin/ConfirmDelete.vue';
import MiniCalendar from '@/components/MiniCalendar.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatIdr, formatUsd, pnlColor } from '@/lib/format';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    member: {
        id: number; name: string; slug: string; title: string | null; tagline: string | null; bio: string | null;
        avatar_url: string | null; specialization: string | null; experience_years: number; location: string | null;
        joined_at: string | null; consultation_rate: number; is_active: boolean; is_featured: boolean; socials: Record<string, string>;
    };
    stats: { total_pnl: number; total_trades: number; win_rate: number; winning_trades: number; best_trade: number; avg_roi: number };
    blocks: Array<{ id: number; date: string; date_raw: string; times: string[] | null; reason: string | null }>;
    consultations: Array<{ id: number; name: string; email: string; phone: string | null; date: string; date_raw: string; times: string[]; hours: number; total_price: number; status: string; payment_status: string; is_upcoming: boolean }>;
    consultStats: { total: number; upcoming: number; done: number; hours: number; earnings: number; unpaid: number };
    signals: Array<{ id: number; pair: string; market: string; side: string; timeframe: string | null; status: string; result: string | null; pnl_percent: number | null; is_premium: boolean; is_published: boolean; published_at: string | null }>;
    signalStats: { total: number; premium: number; free: number };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Team', href: '/admin/members' },
    { title: props.member.name, href: `/admin/members/${props.member.id}` },
];

const toggleActive = () => router.post(`/admin/members/${props.member.id}/toggle-active`, {}, { preserveScroll: true });

const today = new Date();
const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

// ---- Slot helper ----
const slotsForDate = (dateStr: string): string[] => {
    if (!dateStr) return [];
    const wd = new Date(dateStr + 'T00:00:00').getDay();
    if (wd === 0 || wd === 6) return [];
    const all: string[] = [];
    for (let h = 9; h <= 20; h++) all.push(String(h).padStart(2, '0') + ':00');
    const breaks = wd === 5 ? ['11:00', '12:00', '13:00'] : ['12:00'];
    return all.filter((t) => !breaks.includes(t));
};

// ---- Kosongkan jadwal (block) ----
const mode = ref<'full' | 'range' | 'custom'>('full');
const blockForm = useForm({ consultant: String(props.member.id), date: '', full_day: true, times: [] as string[], reason: 'Cuti' });
const rangeFrom = ref('');
const rangeTo = ref('');
const customTimes = ref<string[]>([]);
const daySlots = computed(() => slotsForDate(blockForm.date));

const computedTimes = computed(() => {
    if (mode.value === 'full') return [];
    if (mode.value === 'range') {
        if (!rangeFrom.value || !rangeTo.value) return [];
        return daySlots.value.filter((t) => t >= rangeFrom.value && t <= rangeTo.value);
    }
    return customTimes.value;
});

const toggleCustom = (t: string) => {
    const i = customTimes.value.indexOf(t);
    if (i === -1) customTimes.value.push(t); else customTimes.value.splice(i, 1);
    customTimes.value.sort();
};

const canSubmit = computed(() => {
    if (!blockForm.date) return false;
    if (mode.value === 'full') return true;
    return computedTimes.value.length > 0;
});

const submitBlock = () => {
    blockForm.consultant = String(props.member.id);
    blockForm.full_day = mode.value === 'full';
    blockForm.times = computedTimes.value;
    blockForm.post('/admin/schedule-blocks', {
        preserveScroll: true,
        onSuccess: () => {
            blockForm.reset();
            blockForm.consultant = String(props.member.id);
            mode.value = 'full';
            rangeFrom.value = ''; rangeTo.value = ''; customTimes.value = [];
            blockForm.reason = 'Cuti';
        },
    });
};
const deleteBlock = (id: number) => router.delete(`/admin/schedule-blocks/${id}`, { preserveScroll: true });

const statusClass = (s: string) =>
    s === 'pending' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400'
    : s === 'confirmed' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
    : s === 'done' ? 'bg-sky-500/10 text-sky-600 dark:text-sky-400'
    : 'bg-rose-500/10 text-rose-600 dark:text-rose-400';
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="member.name" />

        <div class="flex flex-col gap-6 p-4 sm:p-6">
            <Link href="/admin/members" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="size-4" /> Kembali ke Team
            </Link>

            <!-- ===== HERO (full width) ===== -->
            <div class="relative overflow-hidden rounded-3xl border border-border/60 bg-gradient-to-br from-emerald-500/10 via-card to-card">
                <div class="pointer-events-none absolute -right-16 -top-16 size-64 rounded-full bg-emerald-500/10 blur-3xl"></div>
                <div class="relative flex flex-col gap-5 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                    <div class="flex items-center gap-5">
                        <img :src="member.avatar_url ?? ''" :alt="member.name" class="size-24 rounded-3xl bg-muted object-cover shadow-lg ring-2 ring-background" />
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h1 class="text-3xl font-extrabold tracking-tight">{{ member.name }}</h1>
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold" :class="member.is_active ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400' : 'bg-muted text-muted-foreground'">{{ member.is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </div>
                            <p class="mt-0.5 font-medium text-emerald-600 dark:text-emerald-400">{{ member.title }}</p>
                            <p v-if="member.tagline" class="mt-1 text-sm italic text-muted-foreground">“{{ member.tagline }}”</p>
                            <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1.5 text-xs text-muted-foreground">
                                <span v-if="member.specialization" class="inline-flex items-center gap-1.5"><Award class="size-3.5 text-emerald-500" /> {{ member.specialization }}</span>
                                <span class="inline-flex items-center gap-1.5"><TrendingUp class="size-3.5 text-emerald-500" /> {{ member.experience_years }} tahun</span>
                                <span v-if="member.location" class="inline-flex items-center gap-1.5"><MapPin class="size-3.5 text-emerald-500" /> {{ member.location }}</span>
                                <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-600 dark:text-emerald-400"><Wallet class="size-3.5" /> {{ formatIdr(member.consultation_rate) }}/jam</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex shrink-0 gap-2">
                        <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-2 text-sm font-medium transition-colors" :class="member.is_active ? 'border-border hover:bg-amber-500/10 hover:text-amber-600 dark:hover:text-amber-400' : 'border-emerald-500/40 text-emerald-600 hover:bg-emerald-500/10 dark:text-emerald-400'" @click="toggleActive">
                            <component :is="member.is_active ? Lock : LockOpen" class="size-4" /> {{ member.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                        <Link :href="`/admin/members/${member.id}/edit`" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600">
                            <Pencil class="size-4" /> Edit
                        </Link>
                    </div>
                </div>
            </div>

            <!-- ===== STAT CARDS ===== -->
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4 xl:grid-cols-6">
                <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-4">
                    <div class="flex items-center gap-1.5 text-xs font-medium uppercase tracking-wide text-muted-foreground"><CircleDollarSign class="size-3.5 text-emerald-500" /> Pendapatan</div>
                    <p class="mt-1 text-xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ formatIdr(consultStats.earnings) }}</p>
                    <p class="text-[11px] text-muted-foreground">Belum bayar: {{ formatIdr(consultStats.unpaid) }}</p>
                </div>
                <div class="rounded-2xl border border-border/60 bg-card p-4">
                    <div class="flex items-center gap-1.5 text-xs font-medium uppercase tracking-wide text-muted-foreground"><Users class="size-3.5 text-sky-500" /> Konsultasi</div>
                    <p class="mt-1 text-xl font-extrabold">{{ consultStats.total }}</p>
                    <p class="text-[11px] text-muted-foreground">{{ consultStats.upcoming }} mendatang · {{ consultStats.done }} selesai</p>
                </div>
                <div class="rounded-2xl border border-border/60 bg-card p-4">
                    <div class="flex items-center gap-1.5 text-xs font-medium uppercase tracking-wide text-muted-foreground"><Hourglass class="size-3.5 text-amber-500" /> Total Jam</div>
                    <p class="mt-1 text-xl font-extrabold">{{ consultStats.hours }} jam</p>
                </div>
                <div class="rounded-2xl border border-border/60 bg-card p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Total PnL</div>
                    <p class="mt-1 text-xl font-extrabold" :class="pnlColor(stats.total_pnl)">{{ formatUsd(stats.total_pnl, true) }}</p>
                </div>
                <div class="rounded-2xl border border-border/60 bg-card p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Win Rate</div>
                    <p class="mt-1 text-xl font-extrabold">{{ stats.win_rate }}%</p>
                </div>
                <div class="rounded-2xl border border-border/60 bg-card p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-muted-foreground">Total Trade</div>
                    <p class="mt-1 text-xl font-extrabold">{{ stats.total_trades }}</p>
                </div>
            </div>

            <!-- ===== MAIN GRID ===== -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Jadwal konsultasi -->
                <div class="rounded-3xl border border-border/60 bg-card lg:col-span-2">
                    <div class="flex items-center justify-between border-b border-border/60 px-6 py-4">
                        <div class="flex items-center gap-2">
                            <CalendarCheck class="size-5 text-emerald-500" />
                            <h3 class="font-semibold">Jadwal Konsultasi</h3>
                        </div>
                        <span class="text-xs text-muted-foreground">{{ consultations.length }} entri</span>
                    </div>
                    <ul class="divide-y divide-border/40">
                        <li v-for="c in consultations" :key="c.id" class="flex flex-wrap items-center justify-between gap-3 px-6 py-4">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-medium">{{ c.name }}</span>
                                    <span v-if="c.is_upcoming" class="rounded-full bg-emerald-500/10 px-2 py-0.5 text-[10px] font-medium text-emerald-600 dark:text-emerald-400">Mendatang</span>
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-medium capitalize" :class="statusClass(c.status)">{{ c.status }}</span>
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-medium" :class="c.payment_status === 'paid' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-amber-500/10 text-amber-600 dark:text-amber-400'">{{ c.payment_status === 'paid' ? 'Lunas' : 'Belum' }}</span>
                                </div>
                                <div class="mt-1 flex flex-wrap gap-x-4 gap-y-0.5 text-xs text-muted-foreground">
                                    <span class="inline-flex items-center gap-1"><CalendarCheck class="size-3.5" /> {{ c.date }}</span>
                                    <span class="inline-flex items-center gap-1"><Clock class="size-3.5" /> {{ c.times.join(', ') }} WIB</span>
                                    <a :href="`mailto:${c.email}`" class="inline-flex items-center gap-1 hover:text-foreground"><Mail class="size-3.5" /> {{ c.email }}</a>
                                    <span v-if="c.phone" class="inline-flex items-center gap-1"><Phone class="size-3.5" /> {{ c.phone }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-emerald-600 dark:text-emerald-400">{{ formatIdr(c.total_price) }}</p>
                                <p class="text-[11px] text-muted-foreground">{{ c.hours }} jam</p>
                            </div>
                        </li>
                        <li v-if="!consultations.length" class="px-6 py-14 text-center text-sm text-muted-foreground">Belum ada konsultasi untuk team ini.</li>
                    </ul>
                </div>

                <!-- Ketersediaan / cuti -->
                <div class="flex flex-col gap-4">
                    <div class="rounded-3xl border border-border/60 bg-card p-6">
                        <div class="flex items-center gap-2">
                            <CalendarX class="size-5 text-rose-500" />
                            <h3 class="font-semibold">Cuti / Kosongkan Jadwal</h3>
                        </div>
                        <p class="mt-1 text-xs text-muted-foreground">Slot yang dikosongkan tak bisa dipesan pengunjung.</p>

                        <div class="mt-4 space-y-3">
                            <MiniCalendar v-model="blockForm.date" :min="todayStr" />
                            <p v-if="blockForm.errors.date" class="text-xs text-rose-500">{{ blockForm.errors.date }}</p>

                            <!-- Mode -->
                            <div class="grid grid-cols-3 gap-1 rounded-lg border border-border/60 p-1 text-xs">
                                <button type="button" class="rounded-md py-1.5 font-medium transition-colors" :class="mode === 'full' ? 'bg-rose-500 text-white' : 'hover:bg-muted'" @click="mode = 'full'">Seharian</button>
                                <button type="button" class="rounded-md py-1.5 font-medium transition-colors" :class="mode === 'range' ? 'bg-rose-500 text-white' : 'hover:bg-muted'" @click="mode = 'range'">Rentang jam</button>
                                <button type="button" class="rounded-md py-1.5 font-medium transition-colors" :class="mode === 'custom' ? 'bg-rose-500 text-white' : 'hover:bg-muted'" @click="mode = 'custom'">Pilih jam</button>
                            </div>

                            <!-- Rentang jam -->
                            <div v-if="mode === 'range'">
                                <div v-if="daySlots.length" class="flex items-center gap-2">
                                    <select v-model="rangeFrom" class="flex-1 rounded-lg border border-border bg-background px-2 py-2 text-sm">
                                        <option value="">Dari jam</option>
                                        <option v-for="t in daySlots" :key="t" :value="t">{{ t }}</option>
                                    </select>
                                    <span class="text-muted-foreground">–</span>
                                    <select v-model="rangeTo" class="flex-1 rounded-lg border border-border bg-background px-2 py-2 text-sm">
                                        <option value="">Sampai jam</option>
                                        <option v-for="t in daySlots" :key="t" :value="t" :disabled="!!rangeFrom && t < rangeFrom">{{ t }}</option>
                                    </select>
                                </div>
                                <p v-else class="text-xs text-muted-foreground">Pilih tanggal hari kerja dulu.</p>
                                <p v-if="computedTimes.length" class="mt-1.5 text-xs text-muted-foreground">Akan dikosongkan: <span class="font-medium text-foreground">{{ computedTimes.join(', ') }}</span> ({{ computedTimes.length }} jam)</p>
                            </div>

                            <!-- Pilih jam -->
                            <div v-if="mode === 'custom'">
                                <div v-if="daySlots.length" class="grid grid-cols-4 gap-2">
                                    <button v-for="t in daySlots" :key="t" type="button" class="rounded-lg border py-1.5 text-xs font-medium transition-colors" :class="customTimes.includes(t) ? 'border-rose-500 bg-rose-500 text-white' : 'border-border hover:bg-muted'" @click="toggleCustom(t)">{{ t }}</button>
                                </div>
                                <p v-else class="text-xs text-muted-foreground">Pilih tanggal hari kerja dulu.</p>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Alasan</label>
                                <input v-model="blockForm.reason" type="text" class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" placeholder="Cuti / acara / dsb" />
                            </div>
                            <button type="button" :disabled="blockForm.processing || !canSubmit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-rose-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-rose-600 disabled:opacity-50" @click="submitBlock">
                                <CalendarX class="size-4" /> Kosongkan Jadwal
                            </button>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-border/60 bg-card p-6">
                        <h3 class="font-semibold">Jadwal Dikosongkan (aktif)</h3>
                        <ul class="mt-3 divide-y divide-border/40">
                            <li v-for="blk in blocks" :key="blk.id" class="flex items-center justify-between py-2.5 text-sm">
                                <div>
                                    <p class="font-medium">{{ blk.date }}</p>
                                    <p class="text-xs text-muted-foreground">{{ blk.times && blk.times.length ? blk.times.join(', ') + ' WIB' : 'Seharian penuh' }}<span v-if="blk.reason"> · {{ blk.reason }}</span></p>
                                </div>
                                <ConfirmDelete label="Hapus?" @confirm="deleteBlock(blk.id)" />
                            </li>
                            <li v-if="!blocks.length" class="py-8 text-center text-sm text-muted-foreground">Belum ada.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ===== SIGNAL KONSULTAN ===== -->
            <div class="rounded-3xl border border-border/60 bg-card">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-border/60 px-6 py-4">
                    <div class="flex items-center gap-2">
                        <Radio class="size-5 text-emerald-500" />
                        <h3 class="font-semibold">Signal Konsultan</h3>
                        <span class="text-xs text-muted-foreground">{{ signalStats.total }} total · {{ signalStats.free }} free · {{ signalStats.premium }} premium</span>
                    </div>
                    <Link :href="`/admin/signals/create?member=${member.id}`" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600">
                        <Plus class="size-4" /> Buat Signal
                    </Link>
                </div>
                <ul class="divide-y divide-border/40">
                    <li v-for="sg in signals" :key="sg.id">
                        <Link :href="`/admin/signals/${sg.id}/edit`" class="flex items-center justify-between gap-3 px-6 py-4 transition-colors hover:bg-muted/40">
                            <div class="flex min-w-0 flex-wrap items-center gap-2">
                                <span class="font-semibold">{{ sg.pair }}</span>
                                <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-medium" :class="sg.side === 'long' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400'">{{ sg.side === 'long' ? 'Long' : 'Short' }}</span>
                                <span class="rounded bg-muted px-1.5 py-0.5 text-[10px] uppercase text-muted-foreground">{{ sg.market }}</span>
                                <span v-if="sg.timeframe" class="text-xs text-muted-foreground">{{ sg.timeframe }}</span>
                                <span v-if="sg.is_premium" class="inline-flex items-center gap-0.5 rounded-full bg-amber-500/10 px-2 py-0.5 text-[10px] font-medium text-amber-600 dark:text-amber-400"><Crown class="size-3" /> Premium</span>
                                <span v-else class="rounded-full bg-emerald-500/10 px-2 py-0.5 text-[10px] font-medium text-emerald-600 dark:text-emerald-400">Free</span>
                                <span v-if="!sg.is_published" class="rounded bg-amber-500/10 px-1.5 py-0.5 text-[10px] text-amber-600 dark:text-amber-400">Draft</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span v-if="sg.pnl_percent !== null" class="text-sm font-bold" :class="sg.pnl_percent > 0 ? 'text-emerald-600 dark:text-emerald-400' : sg.pnl_percent < 0 ? 'text-rose-600 dark:text-rose-400' : ''">{{ sg.pnl_percent > 0 ? '+' : '' }}{{ sg.pnl_percent }}%</span>
                                <span class="hidden text-xs text-muted-foreground sm:inline">{{ sg.published_at ?? '—' }}</span>
                                <ChevronRight class="size-4 shrink-0 text-muted-foreground" />
                            </div>
                        </Link>
                    </li>
                    <li v-if="!signals.length" class="px-6 py-14 text-center text-sm text-muted-foreground">
                        Belum ada signal dari konsultan ini. Klik <span class="font-medium">Buat Signal</span> untuk membagikan sinyal (free atau premium).
                    </li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
