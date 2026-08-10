<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { CheckCircle2, Plug, RefreshCw, XCircle } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    configured: boolean;
    members: Array<{ id: number; name: string }>;
    baseUrl: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Sinkron Binance', href: '/admin/binance' },
];

const testConnection = () => router.post('/admin/binance/test', {}, { preserveScroll: true });

const syncForm = useForm({
    member_id: props.members[0]?.id ?? '',
    days: 30,
});
const runSync = () => syncForm.post('/admin/binance/sync', { preserveScroll: true });

const inputClass = 'w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20';
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Sinkron Binance" />

        <div class="mx-auto flex w-full max-w-3xl flex-col gap-6 p-4 sm:p-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Sinkron Binance</h1>
                <p class="text-sm text-muted-foreground">Tarik realized PnL dari Binance USD-M Futures menjadi data trade.</p>
            </div>

            <!-- Status -->
            <div class="rounded-xl border border-border/60 bg-card p-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="flex size-11 items-center justify-center rounded-xl" :class="configured ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400'">
                            <CheckCircle2 v-if="configured" class="size-5" />
                            <XCircle v-else class="size-5" />
                        </span>
                        <div>
                            <p class="font-semibold">{{ configured ? 'API Key Terkonfigurasi' : 'API Key Belum Diisi' }}</p>
                            <p class="text-xs text-muted-foreground">Endpoint: {{ baseUrl }}</p>
                        </div>
                    </div>
                    <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-muted" @click="testConnection">
                        <Plug class="size-4" /> Uji Koneksi
                    </button>
                </div>

                <div v-if="!configured" class="mt-4 rounded-lg bg-muted/40 p-4 text-sm text-muted-foreground">
                    <p class="font-medium text-foreground">Cara mengaktifkan:</p>
                    <p class="mt-1">Isi kredensial di file <code class="rounded bg-background px-1.5 py-0.5 text-xs">.env</code> lalu jalankan <code class="rounded bg-background px-1.5 py-0.5 text-xs">php artisan config:clear</code>:</p>
                    <pre class="mt-2 overflow-x-auto rounded-lg bg-background p-3 text-xs">BINANCE_API_KEY=xxxxxxxx
BINANCE_API_SECRET=xxxxxxxx</pre>
                    <p class="mt-2">Input manual tetap bisa dipakai tanpa API key.</p>
                </div>
            </div>

            <!-- Sync -->
            <div class="rounded-xl border border-border/60 bg-card p-5">
                <h3 class="font-semibold">Tarik Realized PnL</h3>
                <p class="text-sm text-muted-foreground">Data akan disimpan sebagai trade untuk member terpilih (anti-duplikat otomatis).</p>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Simpan ke Member</label>
                        <select v-model="syncForm.member_id" :class="inputClass">
                            <option v-for="mm in members" :key="mm.id" :value="mm.id">{{ mm.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium">Rentang (hari ke belakang)</label>
                        <input v-model="syncForm.days" type="number" min="1" max="200" :class="inputClass" />
                    </div>
                </div>

                <button
                    type="button"
                    :disabled="!configured || syncForm.processing"
                    class="mt-4 inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600 disabled:cursor-not-allowed disabled:opacity-50"
                    @click="runSync"
                >
                    <RefreshCw class="size-4" :class="{ 'animate-spin': syncForm.processing }" /> {{ syncForm.processing ? 'Menyinkronkan...' : 'Mulai Sinkron' }}
                </button>
            </div>
        </div>
    </AppLayout>
</template>
