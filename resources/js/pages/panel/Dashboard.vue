<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { CalendarCheck, CalendarX, CircleDollarSign, Clock, Hourglass, Mail, Phone, Users } from '@lucide/vue';
import { computed, ref } from 'vue';
import ConfirmDelete from '@/components/admin/ConfirmDelete.vue';
import MiniCalendar from '@/components/MiniCalendar.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatIdr } from '@/lib/format';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    member: { id: number; name: string; title: string | null; avatar_url: string | null; consultation_rate: number; is_active: boolean };
    stats: { total_pnl: number; total_trades: number; win_rate: number };
    consultStats: { total: number; upcoming: number; done: number; hours: number; earnings: number; unpaid: number };
    consultations: Array<{ id: number; name: string; email: string; phone: string | null; date: string; times: string[]; hours: number; total_price: number; status: string; payment_status: string; is_upcoming: boolean }>;
    blocks: Array<{ id: number; date: string; date_raw: string; times: string[] | null; reason: string | null }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Panel Saya', href: '/panel' }];

const today = new Date();
const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

const slotsForDate = (dateStr: string): string[] => {
    if (!dateStr) return [];
    const wd = new Date(dateStr + 'T00:00:00').getDay();
    if (wd === 0 || wd === 6) return [];
    const all: string[] = [];
    for (let h = 9; h <= 20; h++) all.push(String(h).padStart(2, '0') + ':00');
    const breaks = wd === 5 ? ['11:00', '12:00', '13:00'] : ['12:00'];
    return all.filter((t) => !breaks.includes(t));
};

const mode = ref<'full' | 'range' | 'custom'>('full');
const blockForm = useForm({ date: '', full_day: true, times: [] as string[], reason: 'Cuti' });
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
const canSubmit = computed(() => !!blockForm.date && (mode.value === 'full' || computedTimes.value.length > 0));

const submitBlock = () => {
    blockForm.full_day = mode.value === 'full';
    blockForm.times = computedTimes.value;
    blockForm.post('/panel/blocks', {
        preserveScroll: true,
        onSuccess: () => {
            blockForm.reset(); mode.value = 'full'; rangeFrom.value = ''; rangeTo.value = ''; customTimes.value = []; blockForm.reason = 'Cuti';
        },
    });
};
const deleteBlock = (id: number) => router.delete(`/panel/blocks/${id}`, { preserveScroll: true });

const statusClass = (s: string) =>
    s === 'pending' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400'
    : s === 'confirmed' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
    : s === 'done' ? 'bg-sky-500/10 text-sky-600 dark:text-sky-400'
    : 'bg-rose-500/10 text-rose-600 dark:text-rose-400';
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Panel Saya" />

        <div class="flex flex-col gap-6 p-4 sm:p-6">
            <!-- Greeting -->
            <div class="relative overflow-hidden rounded-3xl border border-border/60 bg-gradient-to-br from-emerald-500/10 via-card to-card p-6">
                <div class="flex items-center gap-4">
                    <img :src="member.avatar_url ?? ''" :alt="member.name" class="size-16 rounded-2xl bg-muted object-cover ring-2 ring-background" />
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">Halo, {{ member.name }} 👋</h1>
                        <p class="text-sm text-muted-foreground">{{ member.title }} · Tarif {{ formatIdr(member.consultation_rate) }}/jam</p>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/5 p-4">
                    <div class="flex items-center gap-1.5 text-xs uppercase tracking-wide text-muted-foreground"><CircleDollarSign class="size-3.5 text-emerald-500" /> Pendapatan</div>
                    <p class="mt-1 text-xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ formatIdr(consultStats.earnings) }}</p>
                    <p class="text-[11px] text-muted-foreground">Belum bayar: {{ formatIdr(consultStats.unpaid) }}</p>
                </div>
                <div class="rounded-2xl border border-border/60 bg-card p-4">
                    <div class="flex items-center gap-1.5 text-xs uppercase tracking-wide text-muted-foreground"><Users class="size-3.5 text-sky-500" /> Konsultasi</div>
                    <p class="mt-1 text-xl font-extrabold">{{ consultStats.total }}</p>
                    <p class="text-[11px] text-muted-foreground">{{ consultStats.upcoming }} mendatang · {{ consultStats.done }} selesai</p>
                </div>
                <div class="rounded-2xl border border-border/60 bg-card p-4">
                    <div class="flex items-center gap-1.5 text-xs uppercase tracking-wide text-muted-foreground"><Hourglass class="size-3.5 text-amber-500" /> Total Jam</div>
                    <p class="mt-1 text-xl font-extrabold">{{ consultStats.hours }} jam</p>
                </div>
                <div class="rounded-2xl border border-border/60 bg-card p-4">
                    <div class="text-xs uppercase tracking-wide text-muted-foreground">Win Rate</div>
                    <p class="mt-1 text-xl font-extrabold">{{ stats.win_rate }}%</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Jadwal konsultasi -->
                <div class="rounded-3xl border border-border/60 bg-card lg:col-span-2">
                    <div class="flex items-center justify-between border-b border-border/60 px-6 py-4">
                        <div class="flex items-center gap-2"><CalendarCheck class="size-5 text-emerald-500" /><h3 class="font-semibold">Jadwal Konsultasi Saya</h3></div>
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
                        <li v-if="!consultations.length" class="px-6 py-14 text-center text-sm text-muted-foreground">Belum ada konsultasi.</li>
                    </ul>
                </div>

                <!-- Ketersediaan -->
                <div class="flex flex-col gap-4">
                    <div class="rounded-3xl border border-border/60 bg-card p-6">
                        <div class="flex items-center gap-2"><CalendarX class="size-5 text-rose-500" /><h3 class="font-semibold">Cuti / Kosongkan Jadwal</h3></div>
                        <p class="mt-1 text-xs text-muted-foreground">Atur kapan Anda tidak menerima konsultasi.</p>

                        <div class="mt-4 space-y-3">
                            <MiniCalendar v-model="blockForm.date" :min="todayStr" />
                            <p v-if="blockForm.errors.date" class="text-xs text-rose-500">{{ blockForm.errors.date }}</p>

                            <div class="grid grid-cols-3 gap-1 rounded-lg border border-border/60 p-1 text-xs">
                                <button type="button" class="rounded-md py-1.5 font-medium transition-colors" :class="mode === 'full' ? 'bg-rose-500 text-white' : 'hover:bg-muted'" @click="mode = 'full'">Seharian</button>
                                <button type="button" class="rounded-md py-1.5 font-medium transition-colors" :class="mode === 'range' ? 'bg-rose-500 text-white' : 'hover:bg-muted'" @click="mode = 'range'">Rentang jam</button>
                                <button type="button" class="rounded-md py-1.5 font-medium transition-colors" :class="mode === 'custom' ? 'bg-rose-500 text-white' : 'hover:bg-muted'" @click="mode = 'custom'">Pilih jam</button>
                            </div>

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
                                <p v-if="computedTimes.length" class="mt-1.5 text-xs text-muted-foreground">Akan dikosongkan: <span class="font-medium text-foreground">{{ computedTimes.join(', ') }}</span></p>
                            </div>

                            <div v-if="mode === 'custom'">
                                <div v-if="daySlots.length" class="grid grid-cols-4 gap-2">
                                    <button v-for="t in daySlots" :key="t" type="button" class="rounded-lg border py-1.5 text-xs font-medium transition-colors" :class="customTimes.includes(t) ? 'border-rose-500 bg-rose-500 text-white' : 'border-border hover:bg-muted'" @click="toggleCustom(t)">{{ t }}</button>
                                </div>
                                <p v-else class="text-xs text-muted-foreground">Pilih tanggal hari kerja dulu.</p>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Alasan</label>
                                <input v-model="blockForm.reason" type="text" class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500" placeholder="Cuti / acara / dsb" />
                            </div>
                            <button type="button" :disabled="blockForm.processing || !canSubmit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-rose-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-rose-600 disabled:opacity-50" @click="submitBlock">
                                <CalendarX class="size-4" /> Kosongkan Jadwal
                            </button>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-border/60 bg-card p-6">
                        <h3 class="font-semibold">Jadwal Dikosongkan</h3>
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

            <p class="text-center text-xs text-muted-foreground">
                Butuh ubah profil atau tarif? Hubungi admin. <Link href="/" class="text-emerald-600 hover:underline dark:text-emerald-400">Lihat website</Link>
            </p>
        </div>
    </AppLayout>
</template>
