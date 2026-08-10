<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { CheckCircle2, ShieldCheck, TrendingUp } from '@lucide/vue';
import AnimatedLogo from '@/components/AnimatedLogo.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();

const highlights = [
    { icon: TrendingUp, text: 'Pantau rekam jejak PnL tim secara penuh' },
    { icon: ShieldCheck, text: 'Data & angka performa yang transparan' },
    { icon: CheckCircle2, text: 'Kelola jadwal konsultasi & anggota' },
];
</script>

<template>
    <div class="grid min-h-svh lg:grid-cols-2">
        <!-- ===== Panel brand ===== -->
        <div class="relative hidden flex-col justify-between overflow-hidden bg-gradient-to-br from-emerald-500 via-green-600 to-emerald-700 p-12 text-white lg:flex">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -right-20 -top-20 size-80 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute -bottom-24 -left-10 size-80 rounded-full bg-black/10 blur-3xl"></div>
                <svg class="absolute inset-0 size-full opacity-[0.1]" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="auth-grid" width="34" height="34" patternUnits="userSpaceOnUse">
                            <path d="M34 0H0V34" fill="none" stroke="white" stroke-width="1" />
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#auth-grid)" />
                </svg>
            </div>

            <Link :href="home()" class="relative flex items-center gap-2.5">
                <AnimatedLogo :size="40" />
                <span class="text-lg font-bold">Lentera Project</span>
            </Link>

            <div class="relative">
                <h2 class="text-3xl font-extrabold leading-tight">Menerangi jalan<br />trading Anda.</h2>
                <p class="mt-3 max-w-sm text-white/80">Portal untuk mengelola profil tim, PnL, jadwal konsultasi, dan melihat angka performa secara penuh.</p>
                <ul class="mt-8 space-y-3">
                    <li v-for="h in highlights" :key="h.text" class="flex items-center gap-3 text-sm text-white/90">
                        <span class="flex size-8 items-center justify-center rounded-lg bg-white/15"><component :is="h.icon" class="size-4" /></span>
                        {{ h.text }}
                    </li>
                </ul>
            </div>

            <p class="relative text-xs text-white/60">© {{ new Date().getFullYear() }} Lentera Project. Trading mengandung risiko.</p>
        </div>

        <!-- ===== Panel konten ===== -->
        <div class="flex items-center justify-center bg-background p-6 sm:p-10">
            <div class="w-full max-w-sm">
                <Link :href="home()" class="mb-8 flex items-center gap-2.5 lg:hidden">
                    <AnimatedLogo :size="40" />
                    <span class="text-lg font-bold">Lentera Project</span>
                </Link>

                <div v-if="title || description" class="mb-6">
                    <h1 v-if="title" class="text-2xl font-bold tracking-tight">{{ title }}</h1>
                    <p v-if="description" class="mt-1 text-sm text-muted-foreground">{{ description }}</p>
                </div>

                <slot />
            </div>
        </div>
    </div>
</template>
