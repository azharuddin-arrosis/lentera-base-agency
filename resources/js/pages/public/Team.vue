<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, Award, MapPin } from '@lucide/vue';
import SecretMoney from '@/components/public/SecretMoney.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

interface MemberCard {
    name: string;
    slug: string;
    title: string | null;
    tagline: string | null;
    specialization: string | null;
    experience_years: number;
    location: string | null;
    avatar_url: string | null;
    stats: { total_pnl: number; win_rate: number; total_trades: number; best_trade: number };
}

defineProps<{ members: MemberCard[] }>();
</script>

<template>
    <Head title="Tim Konsultan" />

    <PublicLayout>
        <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl">Tim Konsultan</h1>
                <p class="mt-4 text-muted-foreground">
                    Kenali trader di balik Lentera Project — masing-masing dengan spesialisasi dan pendekatannya sendiri.
                </p>
            </div>

            <div class="mt-14 grid gap-8 md:grid-cols-2">
                <div
                    v-for="m in members"
                    :key="m.slug"
                    class="flex flex-col overflow-hidden rounded-3xl border border-border/60 bg-card shadow-sm transition-transform hover:-translate-y-1"
                >
                    <div class="flex items-start gap-5 border-b border-border/60 bg-gradient-to-br from-emerald-500/5 to-transparent p-6">
                        <img :src="m.avatar_url ?? ''" :alt="m.name" class="size-24 shrink-0 rounded-2xl bg-muted object-cover ring-1 ring-border" />
                        <div class="min-w-0">
                            <h2 class="text-xl font-bold">{{ m.name }}</h2>
                            <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">{{ m.title }}</p>
                            <p v-if="m.tagline" class="mt-2 text-sm italic text-muted-foreground">“{{ m.tagline }}”</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-x-6 gap-y-2 px-6 py-4 text-sm text-muted-foreground">
                        <span v-if="m.specialization" class="inline-flex items-center gap-1.5"><Award class="size-4 text-emerald-500" /> {{ m.specialization }}</span>
                        <span class="inline-flex items-center gap-1.5"><Award class="size-4 text-emerald-500" /> {{ m.experience_years }} tahun pengalaman</span>
                        <span v-if="m.location" class="inline-flex items-center gap-1.5"><MapPin class="size-4 text-emerald-500" /> {{ m.location }}</span>
                    </div>

                    <div class="grid grid-cols-3 gap-px border-t border-border/60 bg-border/60">
                        <div class="bg-card px-4 py-4 text-center">
                            <p class="text-lg font-bold"><SecretMoney :value="m.stats.total_pnl" with-sign /></p>
                            <p class="text-xs text-muted-foreground">Total PnL</p>
                        </div>
                        <div class="bg-card px-4 py-4 text-center">
                            <p class="text-lg font-bold">{{ m.stats.win_rate }}%</p>
                            <p class="text-xs text-muted-foreground">Win Rate</p>
                        </div>
                        <div class="bg-card px-4 py-4 text-center">
                            <p class="text-lg font-bold">{{ m.stats.total_trades }}</p>
                            <p class="text-xs text-muted-foreground">Trade</p>
                        </div>
                    </div>

                    <div class="p-6 pt-4">
                        <Link :href="`/team/${m.slug}`" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-foreground px-5 py-3 text-sm font-semibold text-background transition-opacity hover:opacity-90">
                            Lihat Profil & Riwayat PnL <ArrowRight class="size-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
