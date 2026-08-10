<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{ member: Record<string, unknown> | null }>();

const isEdit = !!props.member;
const m = props.member ?? {};

const form = useForm({
    name: (m.name as string) ?? '',
    title: (m.title as string) ?? '',
    tagline: (m.tagline as string) ?? '',
    bio: (m.bio as string) ?? '',
    avatar: (m.avatar as string) ?? '',
    specialization: (m.specialization as string) ?? '',
    experience_years: (m.experience_years as number) ?? 0,
    location: (m.location as string) ?? '',
    joined_at: m.joined_at ? String(m.joined_at).slice(0, 10) : '',
    consultation_rate: (m.consultation_rate as number) ?? 0,
    telegram: (m.telegram as string) ?? '',
    instagram: (m.instagram as string) ?? '',
    twitter: (m.twitter as string) ?? '',
    tradingview: (m.tradingview as string) ?? '',
    website: (m.website as string) ?? '',
    is_active: (m.is_active as boolean) ?? true,
    is_featured: (m.is_featured as boolean) ?? false,
    sort_order: (m.sort_order as number) ?? 0,
});

const submit = () => {
    if (isEdit) {
        form.put(`/admin/members/${m.id}`);
    } else {
        form.post('/admin/members');
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Team', href: '/admin/members' },
    { title: isEdit ? 'Edit' : 'Tambah', href: '#' },
];

const inputClass = 'w-full rounded-lg border border-border bg-background px-3 py-2 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20';
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="isEdit ? 'Edit Member' : 'Tambah Member'" />

        <div class="flex w-full flex-col gap-6 p-4 sm:p-6">
            <Link href="/admin/members" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground">
                <ArrowLeft class="size-4" /> Kembali
            </Link>

            <div>
                <h1 class="text-2xl font-bold tracking-tight">{{ isEdit ? 'Edit Member' : 'Tambah Member' }}</h1>
                <p class="text-sm text-muted-foreground">Data ini tampil di halaman Tim & profil publik.</p>
            </div>

            <form class="space-y-5" @submit.prevent="submit">
                <div class="rounded-xl border border-border/60 bg-card p-5 sm:p-6">
                    <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Informasi Utama</h3>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Nama <span class="text-rose-500">*</span></label>
                            <input v-model="form.name" type="text" :class="inputClass" />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-rose-500">{{ form.errors.name }}</p>
                        </div>
                        <div class="lg:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium">Jabatan / Peran</label>
                            <input v-model="form.title" type="text" :class="inputClass" placeholder="Lead Consultant · Crypto Futures" />
                        </div>
                        <div class="sm:col-span-2 lg:col-span-3">
                            <label class="mb-1.5 block text-sm font-medium">Tagline</label>
                            <input v-model="form.tagline" type="text" :class="inputClass" placeholder="Kalimat singkat yang menggambarkan gaya trading" />
                        </div>
                        <div class="sm:col-span-2 lg:col-span-3">
                            <label class="mb-1.5 block text-sm font-medium">Bio</label>
                            <textarea v-model="form.bio" rows="4" :class="inputClass"></textarea>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Spesialisasi</label>
                            <input v-model="form.specialization" type="text" :class="inputClass" placeholder="Crypto Futures" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Path Avatar</label>
                            <input v-model="form.avatar" type="text" :class="inputClass" placeholder="/images/team/andra.svg" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Pengalaman (tahun)</label>
                            <input v-model="form.experience_years" type="number" min="0" :class="inputClass" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Lokasi</label>
                            <input v-model="form.location" type="text" :class="inputClass" placeholder="Surabaya, Indonesia" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Tanggal Bergabung</label>
                            <input v-model="form.joined_at" type="date" :class="inputClass" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Urutan Tampil</label>
                            <input v-model="form.sort_order" type="number" min="0" :class="inputClass" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Tarif Konsultasi (Rp/jam)</label>
                            <input v-model="form.consultation_rate" type="number" min="0" step="1000" :class="inputClass" placeholder="350000" />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-border/60 bg-card p-5 sm:p-6">
                    <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-muted-foreground">Media Sosial</h3>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Telegram</label>
                            <input v-model="form.telegram" type="text" :class="inputClass" placeholder="https://t.me/..." />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">Instagram</label>
                            <input v-model="form.instagram" type="text" :class="inputClass" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">X / Twitter</label>
                            <input v-model="form.twitter" type="text" :class="inputClass" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">TradingView</label>
                            <input v-model="form.tradingview" type="text" :class="inputClass" />
                        </div>
                        <div class="sm:col-span-2 lg:col-span-1">
                            <label class="mb-1.5 block text-sm font-medium">Website</label>
                            <input v-model="form.website" type="text" :class="inputClass" />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-border/60 bg-card p-5 sm:p-6">
                    <div class="flex flex-wrap gap-6">
                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input v-model="form.is_active" type="checkbox" class="size-4 rounded border-border text-emerald-500 focus:ring-emerald-500" />
                            Aktif (tampil di website)
                        </label>
                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input v-model="form.is_featured" type="checkbox" class="size-4 rounded border-border text-emerald-500 focus:ring-emerald-500" />
                            Jadikan unggulan
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <Link href="/admin/members" class="rounded-lg border border-border px-5 py-2.5 text-sm font-medium hover:bg-muted">Batal</Link>
                    <button type="submit" :disabled="form.processing" class="rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-600 disabled:opacity-60">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
