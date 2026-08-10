<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Search } from '@lucide/vue';
import { ref } from 'vue';
import ConfirmDelete from '@/components/admin/ConfirmDelete.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface ServiceRow { id: number; title: string; description: string | null; price_label: string | null; is_active: boolean; is_featured: boolean }

const props = defineProps<{
    services: { data: ServiceRow[]; links: Array<{ url: string | null; label: string; active: boolean }>; total: number; from: number | null; to: number | null };
    filters: { search: string | null; status: string | null };
}>();

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');
const applyFilter = () => router.get('/admin/services', {
    search: search.value || undefined,
    status: status.value || undefined,
}, { preserveState: true, replace: true });

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Layanan', href: '/admin/services' },
];

const destroy = (id: number) => router.delete(`/admin/services/${id}`, { preserveScroll: true });
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Kelola Layanan" />

        <div class="flex flex-col gap-6 p-4 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Layanan</h1>
                    <p class="text-sm text-muted-foreground">Daftar layanan yang tampil di landing page.</p>
                </div>
                <Link href="/admin/services/create" class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600">
                    <Plus class="size-4" /> Tambah Layanan
                </Link>
            </div>

            <!-- Filter -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 sm:max-w-xs">
                    <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <input v-model="search" type="text" placeholder="Cari layanan..." class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" @keyup.enter="applyFilter" />
                </div>
                <select v-model="status" class="rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500" @change="applyFilter">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
                <button type="button" class="rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-muted" @click="applyFilter">Terapkan</button>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div v-for="(s, i) in services.data" :key="s.id" class="flex flex-col rounded-xl border border-border/60 bg-card p-5">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="font-semibold"><span class="mr-1 text-muted-foreground">{{ (services.from ?? 1) + i }}.</span>{{ s.title }} <span v-if="s.is_featured" class="ml-1 rounded bg-emerald-500/10 px-1.5 py-0.5 text-[10px] text-emerald-600 dark:text-emerald-400">Populer</span></h3>
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="s.is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-muted text-muted-foreground'">{{ s.is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>
                    <p class="mt-1 flex-1 text-sm text-muted-foreground">{{ s.description }}</p>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">{{ s.price_label ?? '-' }}</span>
                        <div class="flex items-center gap-1">
                            <Link :href="`/admin/services/${s.id}/edit`" class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground"><Pencil class="size-4" /></Link>
                            <ConfirmDelete @confirm="destroy(s.id)" />
                        </div>
                    </div>
                </div>
                <div v-if="!services.data.length" class="col-span-full rounded-xl border border-dashed border-border/60 py-16 text-center text-muted-foreground">Tidak ada layanan pada filter ini.</div>
            </div>

            <!-- Pagination -->
            <div v-if="services.links.length > 3" class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-xs text-muted-foreground">Menampilkan {{ services.from ?? 0 }}–{{ services.to ?? 0 }} dari {{ services.total }}</p>
                <div class="flex flex-wrap items-center justify-center gap-1">
                    <template v-for="(link, i) in services.links" :key="i">
                        <Link v-if="link.url" :href="link.url" preserve-scroll class="rounded-lg border px-3 py-1.5 text-sm" :class="link.active ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-border hover:bg-muted'" v-html="link.label" />
                        <span v-else class="rounded-lg border border-border px-3 py-1.5 text-sm text-muted-foreground opacity-50" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
