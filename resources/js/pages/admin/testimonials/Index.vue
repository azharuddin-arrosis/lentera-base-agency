<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Search, Star } from '@lucide/vue';
import { ref } from 'vue';
import ConfirmDelete from '@/components/admin/ConfirmDelete.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

interface TestimonialRow { id: number; author_name: string; author_role: string | null; quote: string; rating: number; is_active: boolean }

const props = defineProps<{
    testimonials: { data: TestimonialRow[]; links: Array<{ url: string | null; label: string; active: boolean }>; total: number; from: number | null; to: number | null };
    filters: { search: string | null; rating: number | null };
}>();

const search = ref(props.filters.search ?? '');
const rating = ref(props.filters.rating ? String(props.filters.rating) : '');
const applyFilter = () => router.get('/admin/testimonials', {
    search: search.value || undefined,
    rating: rating.value || undefined,
}, { preserveState: true, replace: true });

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Testimoni', href: '/admin/testimonials' },
];

const destroy = (id: number) => router.delete(`/admin/testimonials/${id}`, { preserveScroll: true });
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Kelola Testimoni" />

        <div class="flex flex-col gap-6 p-4 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Testimoni</h1>
                    <p class="text-sm text-muted-foreground">Ulasan yang tampil di landing page.</p>
                </div>
                <Link href="/admin/testimonials/create" class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-600">
                    <Plus class="size-4" /> Tambah Testimoni
                </Link>
            </div>

            <!-- Filter -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 sm:max-w-xs">
                    <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <input v-model="search" type="text" placeholder="Cari nama atau isi ulasan..." class="w-full rounded-lg border border-border bg-background py-2 pl-9 pr-3 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" @keyup.enter="applyFilter" />
                </div>
                <select v-model="rating" class="rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500" @change="applyFilter">
                    <option value="">Semua Rating</option>
                    <option v-for="r in [5, 4, 3, 2, 1]" :key="r" :value="String(r)">{{ r }} bintang</option>
                </select>
                <button type="button" class="rounded-lg border border-border px-4 py-2 text-sm font-medium hover:bg-muted" @click="applyFilter">Terapkan</button>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div v-for="(t, idx) in testimonials.data" :key="t.id" class="flex flex-col rounded-xl border border-border/60 bg-card p-5">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-0.5">
                            <Star v-for="i in t.rating" :key="i" class="size-4 fill-yellow-400 text-yellow-400" />
                        </div>
                        <span class="text-xs text-muted-foreground">#{{ (testimonials.from ?? 1) + idx }}</span>
                    </div>
                    <p class="mt-2 flex-1 text-sm text-foreground/90">“{{ t.quote }}”</p>
                    <div class="mt-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold">{{ t.author_name }}</p>
                            <p class="text-xs text-muted-foreground">{{ t.author_role }}</p>
                        </div>
                        <div class="flex items-center gap-1">
                            <Link :href="`/admin/testimonials/${t.id}/edit`" class="inline-flex size-8 items-center justify-center rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground"><Pencil class="size-4" /></Link>
                            <ConfirmDelete @confirm="destroy(t.id)" />
                        </div>
                    </div>
                </div>
                <div v-if="!testimonials.data.length" class="col-span-full rounded-xl border border-dashed border-border/60 py-16 text-center text-muted-foreground">Tidak ada testimoni pada filter ini.</div>
            </div>

            <!-- Pagination -->
            <div v-if="testimonials.links.length > 3" class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-xs text-muted-foreground">Menampilkan {{ testimonials.from ?? 0 }}–{{ testimonials.to ?? 0 }} dari {{ testimonials.total }}</p>
                <div class="flex flex-wrap items-center justify-center gap-1">
                    <template v-for="(link, i) in testimonials.links" :key="i">
                        <Link v-if="link.url" :href="link.url" preserve-scroll class="rounded-lg border px-3 py-1.5 text-sm" :class="link.active ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-border hover:bg-muted'" v-html="link.label" />
                        <span v-else class="rounded-lg border border-border px-3 py-1.5 text-sm text-muted-foreground opacity-50" v-html="link.label" />
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
