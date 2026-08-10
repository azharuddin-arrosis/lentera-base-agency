<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Clock, Mail, MessageCircle, Send } from '@lucide/vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
});

const submit = () => {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Kontak & Konsultasi" />

    <PublicLayout>
        <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <div class="grid gap-10 lg:grid-cols-2">
                <!-- Info -->
                <div>
                    <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl">Mari Bicara</h1>
                    <p class="mt-4 text-muted-foreground">
                        Punya pertanyaan soal layanan, mentoring, atau ingin konsultasi tentang gaya trading Anda?
                        Kirim pesan — tim kami akan membalas secepatnya.
                    </p>

                    <div class="mt-8 space-y-4">
                        <div class="flex items-start gap-4 rounded-2xl border border-border/60 bg-card p-4">
                            <span class="flex size-10 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400"><MessageCircle class="size-5" /></span>
                            <div>
                                <p class="font-semibold">Telegram</p>
                                <p class="text-sm text-muted-foreground">@lenteraproject — respon paling cepat</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 rounded-2xl border border-border/60 bg-card p-4">
                            <span class="flex size-10 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400"><Mail class="size-5" /></span>
                            <div>
                                <p class="font-semibold">Email</p>
                                <p class="text-sm text-muted-foreground">halo@lentera.id</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 rounded-2xl border border-border/60 bg-card p-4">
                            <span class="flex size-10 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400"><Clock class="size-5" /></span>
                            <div>
                                <p class="font-semibold">Jam Operasional</p>
                                <p class="text-sm text-muted-foreground">Senin–Sabtu, 09.00–21.00 WIB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="rounded-3xl border border-border/60 bg-card p-6 shadow-sm sm:p-8">
                    <form class="space-y-4" @submit.prevent="submit">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium" for="name">Nama <span class="text-rose-500">*</span></label>
                                <input id="name" v-model="form.name" type="text" class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" placeholder="Nama lengkap" />
                                <p v-if="form.errors.name" class="mt-1 text-xs text-rose-500">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium" for="email">Email <span class="text-rose-500">*</span></label>
                                <input id="email" v-model="form.email" type="email" class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" placeholder="email@contoh.com" />
                                <p v-if="form.errors.email" class="mt-1 text-xs text-rose-500">{{ form.errors.email }}</p>
                            </div>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium" for="phone">No. WhatsApp</label>
                                <input id="phone" v-model="form.phone" type="text" class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" placeholder="0812xxxx" />
                                <p v-if="form.errors.phone" class="mt-1 text-xs text-rose-500">{{ form.errors.phone }}</p>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium" for="subject">Subjek</label>
                                <input id="subject" v-model="form.subject" type="text" class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" placeholder="Mis. Tanya mentoring" />
                                <p v-if="form.errors.subject" class="mt-1 text-xs text-rose-500">{{ form.errors.subject }}</p>
                            </div>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" for="message">Pesan <span class="text-rose-500">*</span></label>
                            <textarea id="message" v-model="form.message" rows="5" class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20" placeholder="Ceritakan kebutuhan Anda..."></textarea>
                            <p v-if="form.errors.message" class="mt-1 text-xs text-rose-500">{{ form.errors.message }}</p>
                        </div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-br from-emerald-400 to-green-600 px-5 py-3 text-sm font-semibold text-white shadow-md transition-transform hover:-translate-y-0.5 disabled:opacity-60"
                        >
                            <Send class="size-4" /> {{ form.processing ? 'Mengirim...' : 'Kirim Pesan' }}
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
