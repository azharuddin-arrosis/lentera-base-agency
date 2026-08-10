<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Star } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{ testimonial: Record<string, unknown> | null }>();

const isEdit = !!props.testimonial;
const t = props.testimonial ?? {};

const form = useForm({
    author_name: (t.author_name as string) ?? '',
    author_role: (t.author_role as string) ?? '',
    quote: (t.quote as string) ?? '',
    rating: (t.rating as number) ?? 5,
    is_active: (t.is_active as boolean) ?? true,
    sort_order: (t.sort_order as number) ?? 0,
});

const submit = () => {
    if (isEdit) {
        form.put(`/admin/testimonials/${t.id}`);
    } else {
        form.post('/admin/testimonials');
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Testimoni', href: '/admin/testimonials' },
    { title: isEdit ? 'Edit' : 'Tambah', href: '#' },
];

const inputClass = 'w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20';
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="isEdit ? 'Edit Testimoni' : 'Tambah Testimoni'" />

        <div class="flex w-full flex-col gap-6 p-4 sm:p-6">
            <Link href="/admin/testimonials" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="size-4" /> Kembali
            </Link>

            <h1 class="text-2xl font-bold tracking-tight">{{ isEdit ? 'Edit Testimoni' : 'Tambah Testimoni' }}</h1>

            <form class="space-y-5" @submit.prevent="submit">
                <div class="rounded-xl border border-border/60 bg-card p-5 sm:p-6">
                    <div class="grid gap-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Nama <span class="text-rose-500">*</span></label>
                                <input v-model="form.author_name" type="text" :class="inputClass" />
                                <p v-if="form.errors.author_name" class="mt-1 text-xs text-rose-500">{{ form.errors.author_name }}</p>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Peran / Keterangan</label>
                                <input v-model="form.author_role" type="text" :class="inputClass" placeholder="Member Sinyal · 8 bulan" />
                            </div>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Ulasan <span class="text-rose-500">*</span></label>
                            <textarea v-model="form.quote" rows="4" :class="inputClass"></textarea>
                            <p v-if="form.errors.quote" class="mt-1 text-xs text-rose-500">{{ form.errors.quote }}</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Rating</label>
                                <div class="flex items-center gap-1">
                                    <button v-for="i in 5" :key="i" type="button" @click="form.rating = i">
                                        <Star class="size-6" :class="i <= form.rating ? 'fill-yellow-400 text-yellow-400' : 'text-muted-foreground'" />
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium">Urutan</label>
                                <input v-model="form.sort_order" type="number" min="0" :class="inputClass" />
                            </div>
                        </div>
                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input v-model="form.is_active" type="checkbox" class="size-4 rounded border-border text-emerald-500 focus:ring-emerald-500" /> Aktif (tampil di website)
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <Link href="/admin/testimonials" class="rounded-lg border border-border px-5 py-2.5 text-sm font-medium hover:bg-muted">Batal</Link>
                    <button type="submit" :disabled="form.processing" class="rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-600 disabled:opacity-60">{{ form.processing ? 'Menyimpan...' : 'Simpan' }}</button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
