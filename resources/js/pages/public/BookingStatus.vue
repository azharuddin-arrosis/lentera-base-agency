<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import {
    CalendarCheck,
    CheckCircle2,
    Clock,
    CreditCard,
    Hourglass,
    ImageUp,
    Info,
    Star,
    User,
    XCircle,
} from '@lucide/vue';
import { computed, ref } from 'vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { formatIdr } from '@/lib/format';

interface BookingStatus {
    token: string;
    consultant: string;
    name: string;
    email: string;
    phone: string | null;
    date: string;
    times: string[];
    hours: number;
    rate: number;
    total_price: number;
    notes: string | null;
    status: 'pending' | 'confirmed' | 'done' | 'cancelled';
    payment_status: 'unpaid' | 'awaiting' | 'paid';
    paid_at: string | null;
    payment_proof_url: string | null;
    proof_uploaded_at: string | null;
    rating: number | null;
    review: string | null;
    can_pay: boolean;
    can_review: boolean;
}

const props = defineProps<{ booking: BookingStatus }>();

const STATUS: Record<string, { label: string; class: string }> = {
    pending: { label: 'Menunggu Konfirmasi', class: 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' },
    confirmed: { label: 'Terkonfirmasi', class: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' },
    done: { label: 'Selesai', class: 'bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300' },
    cancelled: { label: 'Dibatalkan', class: 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300' },
};

const PAYMENT: Record<string, { label: string; class: string }> = {
    unpaid: { label: 'Belum Bayar', class: 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300' },
    awaiting: { label: 'Menunggu Verifikasi', class: 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' },
    paid: { label: 'Lunas', class: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' },
};

// ── Upload bukti bayar ──────────────────────────────────────────────
const proofForm = useForm({ payment_proof: null as File | null });
const previewUrl = ref<string | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

function onPickFile(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0] ?? null;
    proofForm.payment_proof = file;
    previewUrl.value = file ? URL.createObjectURL(file) : null;
}

function submitProof() {
    proofForm.post(`/booking/${props.booking.token}/proof`, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            proofForm.reset();
            previewUrl.value = null;
            if (fileInput.value) fileInput.value.value = '';
        },
    });
}

// ── Ulasan / rating ─────────────────────────────────────────────────
const reviewForm = useForm({ rating: props.booking.rating ?? 0, review: props.booking.review ?? '' });
const hovered = ref(0);

function submitReview() {
    reviewForm.post(`/booking/${props.booking.token}/review`, { preserveScroll: true });
}

const alreadyReviewed = computed(() => props.booking.rating !== null);
</script>

<template>
    <Head title="Status Booking" />

    <PublicLayout>
        <div class="mx-auto max-w-3xl px-4 py-10 sm:py-14">
            <div class="mb-6">
                <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Status Booking</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">Konsultasi dengan {{ booking.consultant }}</h1>
                <p class="mt-2 text-sm text-muted-foreground">
                    Simpan halaman ini — semua info, pembayaran, dan ulasan konsultasi Anda ada di sini.
                </p>
            </div>

            <!-- Ringkasan -->
            <div class="rounded-2xl border bg-card p-5 shadow-sm sm:p-6">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold" :class="STATUS[booking.status]?.class">
                        <CheckCircle2 v-if="booking.status === 'confirmed' || booking.status === 'done'" class="size-3.5" />
                        <XCircle v-else-if="booking.status === 'cancelled'" class="size-3.5" />
                        <Clock v-else class="size-3.5" />
                        {{ STATUS[booking.status]?.label }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold" :class="PAYMENT[booking.payment_status]?.class">
                        <CreditCard class="size-3.5" />
                        {{ PAYMENT[booking.payment_status]?.label }}
                    </span>
                </div>

                <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="flex items-start gap-3">
                        <CalendarCheck class="mt-0.5 size-5 text-muted-foreground" />
                        <div>
                            <dt class="text-xs text-muted-foreground">Tanggal</dt>
                            <dd class="font-medium">{{ booking.date }}</dd>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <Clock class="mt-0.5 size-5 text-muted-foreground" />
                        <div>
                            <dt class="text-xs text-muted-foreground">Jam ({{ booking.hours }} jam)</dt>
                            <dd class="font-medium">{{ booking.times.join(', ') }}</dd>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <User class="mt-0.5 size-5 text-muted-foreground" />
                        <div>
                            <dt class="text-xs text-muted-foreground">Atas Nama</dt>
                            <dd class="font-medium">{{ booking.name }}</dd>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <CreditCard class="mt-0.5 size-5 text-muted-foreground" />
                        <div>
                            <dt class="text-xs text-muted-foreground">Total</dt>
                            <dd class="font-semibold text-emerald-600 dark:text-emerald-400">{{ formatIdr(booking.total_price) }}</dd>
                        </div>
                    </div>
                </dl>

                <div v-if="booking.notes" class="mt-4 flex items-start gap-2 rounded-lg bg-muted/60 p-3 text-sm">
                    <Info class="mt-0.5 size-4 shrink-0 text-muted-foreground" />
                    <span>{{ booking.notes }}</span>
                </div>
            </div>

            <!-- Pembayaran -->
            <div class="mt-6 rounded-2xl border bg-card p-5 shadow-sm sm:p-6">
                <h2 class="flex items-center gap-2 text-lg font-semibold">
                    <CreditCard class="size-5 text-emerald-600 dark:text-emerald-400" /> Pembayaran
                </h2>

                <!-- sudah lunas -->
                <div v-if="booking.payment_status === 'paid'" class="mt-3 flex items-center gap-2 rounded-lg bg-emerald-50 p-3 text-sm text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300">
                    <CheckCircle2 class="size-4" /> Pembayaran sudah dikonfirmasi lunas<span v-if="booking.paid_at"> pada {{ booking.paid_at }}</span>.
                </div>

                <!-- menunggu verifikasi -->
                <div v-else-if="booking.payment_status === 'awaiting'" class="mt-3 flex items-center gap-2 rounded-lg bg-amber-50 p-3 text-sm text-amber-700 dark:bg-amber-500/10 dark:text-amber-300">
                    <Hourglass class="size-4" /> Bukti pembayaran sudah terkirim<span v-if="booking.proof_uploaded_at"> ({{ booking.proof_uploaded_at }})</span>. Menunggu verifikasi tim kami.
                </div>

                <!-- instruksi transfer -->
                <div v-if="booking.can_pay" class="mt-4 rounded-lg border border-dashed bg-muted/40 p-4 text-sm">
                    <p class="font-medium">Silakan transfer ke:</p>
                    <p class="mt-1 text-muted-foreground">BCA <span class="font-semibold text-foreground">1234567890</span> a.n. Lentera Project</p>
                    <p class="mt-1">Nominal: <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ formatIdr(booking.total_price) }}</span></p>
                </div>

                <!-- bukti terunggah -->
                <div v-if="booking.payment_proof_url" class="mt-4">
                    <p class="mb-2 text-xs text-muted-foreground">Bukti yang Anda unggah:</p>
                    <a :href="booking.payment_proof_url" target="_blank" rel="noopener">
                        <img :src="booking.payment_proof_url" alt="Bukti pembayaran" class="max-h-56 rounded-lg border object-contain" />
                    </a>
                </div>

                <!-- form upload bukti -->
                <form v-if="booking.can_pay" class="mt-4" @submit.prevent="submitProof">
                    <label class="mb-1.5 block text-sm font-medium">
                        {{ booking.payment_proof_url ? 'Ganti bukti pembayaran' : 'Unggah bukti pembayaran' }}
                    </label>
                    <input
                        ref="fileInput"
                        type="file"
                        accept="image/*"
                        class="block w-full text-sm text-muted-foreground file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-emerald-700"
                        @change="onPickFile"
                    />
                    <p v-if="proofForm.errors.payment_proof" class="mt-1 text-xs text-rose-600">{{ proofForm.errors.payment_proof }}</p>

                    <img v-if="previewUrl" :src="previewUrl" alt="Pratinjau" class="mt-3 max-h-56 rounded-lg border object-contain" />

                    <button
                        type="submit"
                        :disabled="!proofForm.payment_proof || proofForm.processing"
                        class="mt-3 inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <ImageUp class="size-4" /> {{ proofForm.processing ? 'Mengirim…' : 'Kirim Bukti Pembayaran' }}
                    </button>
                </form>
            </div>

            <!-- Ulasan (setelah selesai) -->
            <div v-if="booking.can_review" class="mt-6 rounded-2xl border bg-card p-5 shadow-sm sm:p-6">
                <h2 class="flex items-center gap-2 text-lg font-semibold">
                    <Star class="size-5 text-amber-500" /> Beri Ulasan
                </h2>
                <p class="mt-1 text-sm text-muted-foreground">Bagaimana pengalaman konsultasi Anda bersama {{ booking.consultant }}?</p>

                <form class="mt-4" @submit.prevent="submitReview">
                    <div class="flex items-center gap-1" @mouseleave="hovered = 0">
                        <button
                            v-for="n in 5"
                            :key="n"
                            type="button"
                            class="transition"
                            @mouseenter="hovered = n"
                            @click="reviewForm.rating = n"
                        >
                            <Star
                                class="size-8"
                                :class="(hovered || reviewForm.rating) >= n ? 'fill-amber-400 text-amber-400' : 'text-muted-foreground/40'"
                            />
                        </button>
                        <span v-if="reviewForm.rating" class="ml-2 text-sm font-medium">{{ reviewForm.rating }}/5</span>
                    </div>
                    <p v-if="reviewForm.errors.rating" class="mt-1 text-xs text-rose-600">{{ reviewForm.errors.rating }}</p>

                    <textarea
                        v-model="reviewForm.review"
                        rows="3"
                        placeholder="Ceritakan pengalaman Anda (opsional)…"
                        class="mt-3 w-full rounded-lg border bg-background px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-emerald-500/40"
                    ></textarea>

                    <button
                        type="submit"
                        :disabled="!reviewForm.rating || reviewForm.processing"
                        class="mt-3 inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-amber-600 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <Star class="size-4" /> {{ alreadyReviewed ? 'Perbarui Ulasan' : 'Kirim Ulasan' }}
                    </button>
                    <span v-if="alreadyReviewed" class="ml-2 text-xs text-muted-foreground">Terima kasih, ulasan Anda sudah tersimpan.</span>
                </form>
            </div>
        </div>
    </PublicLayout>
</template>
