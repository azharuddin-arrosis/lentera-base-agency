<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Mail, Phone, Search } from '@lucide/vue';
import { ref } from 'vue';
import ConfirmDelete from '@/components/admin/ConfirmDelete.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface LeadRow { id: number; name: string; email: string; phone: string | null; subject: string | null; message: string; status: string; created_at: string | null }

const props = defineProps<{
    leads: { data: LeadRow[]; links: Array<{ url: string | null; label: string; active: boolean }>; total: number; from: number | null; to: number | null };
    filters: { search: string | null; status: string | null };
}>();

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');
const applyFilter = () => router.get('/admin/leads', {
    search: search.value || undefined,
    status: status.value || undefined,
}, { preserveState: true, replace: true });

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Pesan Masuk', href: '/admin/leads' },
];

const setStatus = (id: number, status: string) => router.patch(`/admin/leads/${id}`, { status }, { preserveScroll: true });
const destroy = (id: number) => router.delete(`/admin/leads/${id}`, { preserveScroll: true });

const statusClass = (s: string) =>
    s === 'new'
        ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
        : s === 'done'
          ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
          : 'bg-muted text-muted-foreground';
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Pesan Masuk" />

        <div class="flex flex-col gap-6 p-4 sm:p-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Pesan Masuk</h1>
                <p class="text-sm text-muted-foreground">Lead dari form kontak website.</p>
            </div>

            <!-- Filter -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 sm:max-w-xs">
                    <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <input v-model="search" type="text" placeholder="Cari nama, email, subjek..." class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" @keyup.enter="applyFilter" />
                </div>
                <select v-model="status" class="rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500" @change="applyFilter">
                    <option value="">Semua Status</option>
                    <option value="new">Baru</option>
                    <option value="read">Dibaca</option>
                    <option value="done">Selesai</option>
                </select>
                <button type="button" class="rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-muted" @click="applyFilter">Terapkan</button>
            </div>

            <div class="grid gap-4">
                <div v-for="(l, i) in leads.data" :key="l.id" class="rounded-xl border border-border/60 bg-card p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-muted-foreground">{{ (leads.from ?? 1) + i }}.</span>
                                <h3 class="font-semibold">{{ l.name }}</h3>
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium capitalize" :class="statusClass(l.status)">{{ l.status }}</span>
                            </div>
                            <p v-if="l.subject" class="text-sm text-muted-foreground">{{ l.subject }}</p>
                            <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-xs text-muted-foreground">
                                <a :href="`mailto:${l.email}`" class="inline-flex items-center gap-1 hover:text-foreground"><Mail class="size-3.5" /> {{ l.email }}</a>
                                <span v-if="l.phone" class="inline-flex items-center gap-1"><Phone class="size-3.5" /> {{ l.phone }}</span>
                                <span>{{ l.created_at }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <select :value="l.status" class="rounded-lg border border-border bg-background px-2.5 py-1.5 text-xs outline-none focus:border-emerald-500" @change="setStatus(l.id, ($event.target as HTMLSelectElement).value)">
                                <option value="new">Baru</option>
                                <option value="read">Dibaca</option>
                                <option value="done">Selesai</option>
                            </select>
                            <ConfirmDelete @confirm="destroy(l.id)" />
                        </div>
                    </div>
                    <p class="mt-3 whitespace-pre-line rounded-lg bg-muted/40 p-3 text-sm text-foreground/90">{{ l.message }}</p>
                </div>
                <div v-if="!leads.data.length" class="rounded-xl border border-dashed border-border/60 py-16 text-center text-muted-foreground">
                    Tidak ada pesan pada filter ini.
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="leads.links.length > 3" class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-xs text-muted-foreground">Menampilkan {{ leads.from ?? 0 }}–{{ leads.to ?? 0 }} dari {{ leads.total }}</p>
                <div class="flex flex-wrap items-center justify-center gap-1">
                    <template v-for="(link, i) in leads.links" :key="i">
                        <Link v-if="link.url" :href="link.url" preserve-scroll class="rounded-lg border px-3 py-1.5 text-sm" :class="link.active ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-border hover:bg-muted'" v-html="link.label" />
                        <span v-else class="rounded-lg border border-border px-3 py-1.5 text-sm text-muted-foreground opacity-50" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
