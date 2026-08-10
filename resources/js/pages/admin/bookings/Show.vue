<script setup lang="ts">
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, CalendarClock, CheckCircle2, Clock, Copy, Hourglass, ImageOff, Link2, Mail, Phone, RefreshCw, Star, User } from '@lucide/vue';
import { computed, ref } from 'vue';
import ConfirmDelete from '@/components/admin/ConfirmDelete.vue';
import MiniCalendar from '@/components/MiniCalendar.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatIdr } from '@/lib/format';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    booking: {
        id: number; consultant: string; name: string; email: string; phone: string | null;
        date: string; date_raw: string; times: string[]; hours: number; rate: number; total_price: number;
        notes: string | null; status: string; payment_status: string; paid_at: string | null;
        payment_proof_url: string | null; proof_uploaded_at: string | null;
        rating: number | null; review: string | null; created_at: string | null; status_url: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Jadwal Konsultasi', href: '/admin/bookings' },
    { title: props.booking.name, href: `/admin/bookings/${props.booking.id}` },
];

const today = new Date();
const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

const statusClass = (s: string) =>
    s === 'pending' ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400'
    : s === 'confirmed' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
    : s === 'done' ? 'bg-sky-500/10 text-sky-600 dark:text-sky-400'
    : 'bg-rose-500/10 text-rose-600 dark:text-rose-400';

const payMeta = computed(() => {
    if (props.booking.payment_status === 'paid') return { label: 'Lunas', class: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' };
    if (props.booking.payment_status === 'awaiting') return { label: 'Menunggu Verifikasi', class: 'bg-amber-500/10 text-amber-600 dark:text-amber-400' };
    return { label: 'Belum bayar', class: 'bg-rose-500/10 text-rose-600 dark:text-rose-400' };
});

const setStatus = (status: string) => router.patch(`/admin/bookings/${props.booking.id}`, { status }, { preserveScroll: true });
const togglePaid = () => router.post(`/admin/bookings/${props.booking.id}/paid`, {}, { preserveScroll: true });
const destroy = () => router.delete(`/admin/bookings/${props.booking.id}`);

// ---- Link privat klien ----
const copied = ref(false);
const copyLink = async () => {
    try {
        await navigator.clipboard.writeText(props.booking.status_url);
        copied.value = true;
        setTimeout(() => (copied.value = false), 1800);
    } catch {
        /* clipboard tidak tersedia */
    }
};

// ---- Reschedule ----
const slotsForDate = (dateStr: string): string[] => {
    if (!dateStr) return [];
    const wd = new Date(dateStr + 'T00:00:00').getDay();
    if (wd === 0 || wd === 6) return [];
    const all: string[] = [];
    for (let h = 9; h <= 20; h++) all.push(String(h).padStart(2, '0') + ':00');
    const breaks = wd === 5 ? ['11:00', '12:00', '13:00'] : ['12:00'];
    return all.filter((t) => !breaks.includes(t));
};
const rsOpen = ref(false);
const rsForm = useForm({ date: props.booking.date_raw, times: [...props.booking.times] });
const rsSlots = computed(() => slotsForDate(rsForm.date));
const toggleRs = (t: string) => {
    const i = rsForm.times.indexOf(t);
    if (i === -1) rsForm.times.push(t); else rsForm.times.splice(i, 1);
    rsForm.times.sort();
};
const submitReschedule = () => rsForm.post(`/admin/bookings/${props.booking.id}/reschedule`, {
    preserveScroll: true,
    onSuccess: () => (rsOpen.value = false),
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Booking — ${booking.name}`" />

        <div class="flex flex-col gap-6 p-4 sm:p-6">
            <div class="flex items-center justify-between">
                <Link href="/admin/bookings" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground"><ArrowLeft class="size-4" /> Kembali</Link>
                <ConfirmDelete label="Hapus booking?" @confirm="destroy" />
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- KIRI: info + reschedule + ulasan -->
                <div class="flex flex-col gap-6 lg:col-span-2">
                    <!-- Info -->
                    <div class="rounded-3xl border border-border/60 bg-gradient-to-br from-emerald-500/10 via-card to-card p-6">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-500/10 px-2.5 py-1 text-sm font-semibold text-emerald-700 dark:text-emerald-300"><CalendarClock class="size-4" /> {{ booking.date }}</span>
                            <span class="inline-flex items-center gap-1 text-sm font-medium"><Clock class="size-3.5" /> {{ booking.times.join(', ') }} WIB</span>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium capitalize" :class="statusClass(booking.status)">{{ booking.status }}</span>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="payMeta.class">{{ payMeta.label }}</span>
                        </div>
                        <h1 class="mt-3 text-2xl font-bold tracking-tight">{{ booking.name }}</h1>
                        <div class="mt-2 flex flex-wrap gap-x-5 gap-y-1.5 text-sm text-muted-foreground">
                            <span class="inline-flex items-center gap-1.5"><User class="size-4 text-emerald-500" /> Konsultan: {{ booking.consultant }}</span>
                            <a :href="`mailto:${booking.email}`" class="inline-flex items-center gap-1.5 hover:text-foreground"><Mail class="size-4 text-emerald-500" /> {{ booking.email }}</a>
                            <span v-if="booking.phone" class="inline-flex items-center gap-1.5"><Phone class="size-4 text-emerald-500" /> {{ booking.phone }}</span>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center gap-6 rounded-xl bg-muted/40 p-4 text-sm">
                            <div><span class="text-muted-foreground">Total</span><p class="text-lg font-extrabold text-emerald-600 dark:text-emerald-400">{{ formatIdr(booking.total_price) }}</p></div>
                            <div><span class="text-muted-foreground">Rincian</span><p class="font-medium">{{ booking.hours }} jam × {{ formatIdr(booking.rate) }}</p></div>
                            <div v-if="booking.paid_at"><span class="text-muted-foreground">Dibayar</span><p class="font-medium">{{ booking.paid_at }}</p></div>
                        </div>
                        <p v-if="booking.notes" class="mt-3 whitespace-pre-line rounded-xl bg-muted/40 p-3 text-sm text-foreground/90"><span class="font-medium">Catatan:</span> {{ booking.notes }}</p>
                    </div>

                    <!-- Aksi status + reschedule -->
                    <div class="rounded-3xl border border-border/60 bg-card p-6">
                        <h3 class="font-semibold">Kelola Booking</h3>
                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            <div>
                                <label class="mb-1 block text-xs text-muted-foreground">Status</label>
                                <select :value="booking.status" class="rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500" @change="setStatus(($event.target as HTMLSelectElement).value)">
                                    <option value="pending">Menunggu</option>
                                    <option value="confirmed">Dikonfirmasi</option>
                                    <option value="done">Selesai</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                            </div>
                            <button type="button" class="mt-4 inline-flex items-center gap-1.5 rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-muted" @click="rsOpen = !rsOpen">
                                <RefreshCw class="size-4" /> Reschedule
                            </button>
                            <button type="button" class="mt-4 inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold transition-colors" :class="booking.payment_status === 'paid' ? 'border border-border hover:bg-muted' : 'bg-emerald-500 text-white hover:bg-emerald-600'" @click="togglePaid">
                                <CheckCircle2 class="size-4" /> {{ booking.payment_status === 'paid' ? 'Batalkan Lunas' : 'Konfirmasi Lunas' }}
                            </button>
                        </div>

                        <div v-if="rsOpen" class="mt-4 rounded-2xl border border-border/60 p-4">
                            <p class="mb-3 text-sm font-medium">Pilih jadwal baru</p>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <MiniCalendar v-model="rsForm.date" :min="todayStr" />
                                <div>
                                    <div v-if="rsSlots.length" class="grid grid-cols-3 gap-2">
                                        <button v-for="t in rsSlots" :key="t" type="button" class="rounded-lg border py-1.5 text-xs font-medium transition-colors" :class="rsForm.times.includes(t) ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-border hover:bg-muted'" @click="toggleRs(t)">{{ t }}</button>
                                    </div>
                                    <p v-else class="text-xs text-muted-foreground">Tanggal libur — pilih hari kerja.</p>
                                    <p v-if="rsForm.errors.times" class="mt-1 text-xs text-rose-500">{{ rsForm.errors.times }}</p>
                                    <p class="mt-3 text-sm">Total baru: <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ formatIdr(booking.rate * rsForm.times.length) }}</span> <span class="text-muted-foreground">({{ rsForm.times.length }} jam)</span></p>
                                    <button type="button" :disabled="rsForm.processing || !rsForm.times.length" class="mt-3 w-full rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600 disabled:opacity-50" @click="submitReschedule">Simpan Reschedule</button>
                                </div>
                            </div>
                        </div>

                        <!-- Link privat untuk klien -->
                        <div class="mt-4 rounded-2xl border border-dashed border-border/60 bg-muted/30 p-4">
                            <p class="flex items-center gap-1.5 text-xs font-medium text-muted-foreground"><Link2 class="size-3.5" /> Link status untuk klien (upload bukti bayar & ulasan)</p>
                            <div class="mt-2 flex items-center gap-2">
                                <input :value="booking.status_url" readonly class="flex-1 truncate rounded-lg border border-border bg-background px-3 py-1.5 text-xs text-muted-foreground" />
                                <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3 py-1.5 text-xs font-medium hover:bg-muted" @click="copyLink">
                                    <Copy class="size-3.5" /> {{ copied ? 'Tersalin' : 'Salin' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Ulasan konsultasi (read-only, dari klien) -->
                    <div class="rounded-3xl border border-border/60 bg-card p-6">
                        <div class="flex items-center gap-2"><Star class="size-5 text-yellow-400" /><h3 class="font-semibold">Ulasan Konsultasi</h3></div>
                        <p class="mt-1 text-xs text-muted-foreground">Diisi oleh klien dari halaman status booking setelah konsultasi selesai. Tampil di profil publik konsultan.</p>

                        <div v-if="booking.rating" class="mt-4">
                            <div class="flex items-center gap-1">
                                <Star v-for="i in 5" :key="i" class="size-6" :class="i <= booking.rating ? 'fill-yellow-400 text-yellow-400' : 'text-muted-foreground/30'" />
                                <span class="ml-2 text-sm font-medium">{{ booking.rating }}/5</span>
                            </div>
                            <p v-if="booking.review" class="mt-3 rounded-xl bg-muted/40 p-3 text-sm text-foreground/90">"{{ booking.review }}"</p>
                        </div>
                        <p v-else class="mt-4 rounded-xl border border-dashed border-border/60 p-4 text-center text-sm text-muted-foreground">
                            Klien belum memberi ulasan.
                        </p>
                    </div>
                </div>

                <!-- KANAN: bukti pembayaran (read-only, dari klien) -->
                <div class="rounded-3xl border border-border/60 bg-card p-6">
                    <div class="flex items-center gap-2"><CheckCircle2 class="size-5 text-emerald-500" /><h3 class="font-semibold">Bukti Pembayaran</h3></div>
                    <p class="mt-1 text-xs text-muted-foreground">Diunggah oleh klien. Verifikasi lalu klik <span class="font-medium">Konfirmasi Lunas</span> di panel Kelola Booking.</p>

                    <div v-if="booking.payment_status === 'awaiting'" class="mt-3 flex items-center gap-2 rounded-lg bg-amber-500/10 p-2.5 text-xs font-medium text-amber-600 dark:text-amber-400">
                        <Hourglass class="size-4" /> Menunggu verifikasi Anda
                    </div>

                    <div v-if="booking.payment_proof_url" class="mt-4">
                        <a :href="booking.payment_proof_url" target="_blank" rel="noopener">
                            <img :src="booking.payment_proof_url" alt="Bukti bayar" class="w-full rounded-xl border border-border object-cover" />
                        </a>
                        <p v-if="booking.proof_uploaded_at" class="mt-2 text-xs text-muted-foreground">Diunggah {{ booking.proof_uploaded_at }}. Klik gambar untuk memperbesar.</p>
                    </div>
                    <div v-else class="mt-4 flex h-40 flex-col items-center justify-center gap-2 rounded-xl border border-dashed border-border/60 text-sm text-muted-foreground">
                        <ImageOff class="size-6 opacity-60" />
                        Klien belum mengunggah bukti bayar
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
