<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Plus, X } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{ service: Record<string, unknown> | null }>();

const isEdit = !!props.service;
const s = props.service ?? {};

const form = useForm({
    title: (s.title as string) ?? '',
    description: (s.description as string) ?? '',
    icon: (s.icon as string) ?? 'sparkles',
    price_label: (s.price_label as string) ?? '',
    features: ((s.features as string[]) ?? ['']).length ? ((s.features as string[]) ?? ['']) : [''],
    is_active: (s.is_active as boolean) ?? true,
    is_featured: (s.is_featured as boolean) ?? false,
    sort_order: (s.sort_order as number) ?? 0,
});

const addFeature = () => form.features.push('');
const removeFeature = (i: number) => form.features.splice(i, 1);

const submit = () => {
    if (isEdit) {
        form.put(`/admin/services/${s.id}`);
    } else {
        form.post('/admin/services');
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Layanan', href: '/admin/services' },
    { title: isEdit ? 'Edit' : 'Tambah', href: '#' },
];

const inputClass = 'w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20';
const iconOptions = ['sparkles', 'signal', 'graduation-cap', 'chart-pie', 'briefcase'];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="isEdit ? 'Edit Layanan' : 'Tambah Layanan'" />

        <div class="flex w-full flex-col gap-6 p-4 sm:p-6">
            <Link href="/admin/services" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="size-4" /> Kembali
            </Link>

            <h1 class="text-2xl font-bold tracking-tight">{{ isEdit ? 'Edit Layanan' : 'Tambah Layanan' }}</h1>

            <form class="space-y-5" @submit.prevent="submit">
                <div class="rounded-xl border border-border/60 bg-card p-5 sm:p-6">
                    <div class="grid gap-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Judul <span class="text-rose-500">*</span></label>
                            <input v-model="form.title" type="text" :class="inputClass" />
                            <p v-if="form.errors.title" class="mt-1 text-xs text-rose-500">{{ form.errors.title }}</p>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Deskripsi</label>
                            <textarea v-model="form.description" rows="3" :class="inputClass"></textarea>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Ikon</label>
                                <select v-model="form.icon" :class="inputClass">
                                    <option v-for="ic in iconOptions" :key="ic" :value="ic">{{ ic }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Label Harga</label>
                                <input v-model="form.price_label" type="text" :class="inputClass" placeholder="Mulai Rp299rb/bln" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Urutan</label>
                                <input v-model="form.sort_order" type="number" min="0" :class="inputClass" />
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Fitur</label>
                            <div class="space-y-2">
                                <div v-for="(f, i) in form.features" :key="i" class="flex items-center gap-2">
                                    <input v-model="form.features[i]" type="text" :class="inputClass" placeholder="Poin fitur" />
                                    <button type="button" class="inline-flex size-9 shrink-0 items-center justify-center rounded-lg border border-border text-muted-foreground hover:bg-muted" @click="removeFeature(i)"><X class="size-4" /></button>
                                </div>
                            </div>
                            <button type="button" class="mt-2 inline-flex items-center gap-1.5 text-sm font-medium text-emerald-600 hover:underline dark:text-emerald-400" @click="addFeature"><Plus class="size-4" /> Tambah fitur</button>
                        </div>

                        <div class="flex flex-wrap gap-6 pt-2">
                            <label class="flex cursor-pointer items-center gap-2 text-sm">
                                <input v-model="form.is_active" type="checkbox" class="size-4 rounded border-border text-emerald-500 focus:ring-emerald-500" /> Aktif
                            </label>
                            <label class="flex cursor-pointer items-center gap-2 text-sm">
                                <input v-model="form.is_featured" type="checkbox" class="size-4 rounded border-border text-emerald-500 focus:ring-emerald-500" /> Populer
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <Link href="/admin/services" class="rounded-lg border border-border px-5 py-2.5 text-sm font-medium hover:bg-muted">Batal</Link>
                    <button type="submit" :disabled="form.processing" class="rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-600 disabled:opacity-60">{{ form.processing ? 'Menyimpan...' : 'Simpan' }}</button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
