<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { onClickOutside } from '@vueuse/core';
import { CalendarClock, ChevronDown, Clock, LogIn, Menu, Moon, Sun, X } from '@lucide/vue';
import { computed, ref } from 'vue';
import AnimatedLogo from '@/components/AnimatedLogo.vue';
import { Toaster } from '@/components/ui/sonner';
import { useAppearance } from '@/composables/useAppearance';

const page = usePage();
const user = computed(() => (page.props.auth as { user?: { name: string } } | undefined)?.user);

const { resolvedAppearance, updateAppearance } = useAppearance();
const toggleTheme = () => updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');

const mobileOpen = ref(false);

// dropdown Jadwal Konsultasi
const scheduleOpen = ref(false);
const scheduleRef = ref<HTMLElement | null>(null);
onClickOutside(scheduleRef, () => (scheduleOpen.value = false));

const scheduleSlots = [
    { day: 'Senin – Jumat', time: '09.00 – 21.00 WIB' },
    { day: 'Sabtu', time: '09.00 – 15.00 WIB' },
    { day: 'Minggu', time: 'Libur (chat tetap dibalas)' },
];
const consultTypes = [
    { title: 'Sinyal Trading Harian', desc: 'Diskusi setup & entry harian' },
    { title: 'Mentoring 1-on-1', desc: 'Sesi privat terjadwal' },
    { title: 'Analisis Portofolio', desc: 'Audit & review posisi Anda' },
];

const nav = [
    { label: 'Beranda', href: '/' },
    { label: 'Tim', href: '/team' },
    { label: 'Signal', href: '/signals' },
    { label: 'Performa', href: '/performance' },
    { label: 'Kontak', href: '/contact' },
];

const currentPath = computed(() => page.url.split('?')[0]);
const isActive = (href: string) => (href === '/' ? currentPath.value === '/' : currentPath.value.startsWith(href));
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <!-- NAVBAR -->
        <header class="sticky top-0 z-50 border-b border-border/60 bg-background/80 backdrop-blur-lg">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
                <!-- Kiri: logo + menu dipepet -->
                <div class="flex items-center gap-5">
                    <Link href="/" class="flex items-center gap-2.5">
                        <AnimatedLogo :size="36" />
                        <span class="text-lg font-bold tracking-tight">Lentera<span class="text-emerald-500">.</span></span>
                    </Link>

                    <nav class="hidden items-center gap-1 md:flex">
                        <Link
                            v-for="item in nav"
                            :key="item.href"
                            :href="item.href"
                            class="rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                            :class="isActive(item.href) ? 'text-emerald-600 dark:text-emerald-400' : 'text-muted-foreground hover:text-foreground'"
                        >
                            {{ item.label }}
                        </Link>

                        <!-- Dropdown Jadwal Konsultasi -->
                        <div ref="scheduleRef" class="relative">
                            <button
                                type="button"
                                class="flex items-center gap-1 rounded-lg px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                                @click="scheduleOpen = !scheduleOpen"
                            >
                                Jadwal Konsultasi
                                <ChevronDown class="size-4 transition-transform" :class="{ 'rotate-180': scheduleOpen }" />
                            </button>

                            <transition
                                enter-active-class="transition duration-150 ease-out"
                                enter-from-class="opacity-0 -translate-y-1"
                                leave-active-class="transition duration-100 ease-in"
                                leave-to-class="opacity-0 -translate-y-1"
                            >
                                <div v-if="scheduleOpen" class="absolute left-0 top-full mt-2 w-80 rounded-2xl border border-border/60 bg-card p-4 shadow-lg">
                                    <div class="flex items-center gap-2 text-sm font-semibold">
                                        <CalendarClock class="size-4 text-emerald-500" /> Jam Konsultasi
                                    </div>
                                    <ul class="mt-3 space-y-1.5">
                                        <li v-for="s in scheduleSlots" :key="s.day" class="flex items-center justify-between text-xs">
                                            <span class="text-muted-foreground">{{ s.day }}</span>
                                            <span class="inline-flex items-center gap-1 font-medium"><Clock class="size-3" /> {{ s.time }}</span>
                                        </li>
                                    </ul>

                                    <div class="my-3 h-px bg-border/60"></div>

                                    <p class="mb-2 text-xs font-semibold text-muted-foreground">Pilih jenis konsultasi</p>
                                    <div class="space-y-1">
                                        <Link
                                            v-for="c in consultTypes"
                                            :key="c.title"
                                            href="/schedule"
                                            class="block rounded-lg px-3 py-2 transition-colors hover:bg-muted"
                                            @click="scheduleOpen = false"
                                        >
                                            <p class="text-sm font-medium">{{ c.title }}</p>
                                            <p class="text-xs text-muted-foreground">{{ c.desc }}</p>
                                        </Link>
                                    </div>

                                    <Link
                                        href="/schedule"
                                        class="mt-3 flex items-center justify-center gap-1.5 rounded-lg bg-gradient-to-br from-emerald-400 to-green-600 px-3 py-2 text-sm font-semibold text-white"
                                        @click="scheduleOpen = false"
                                    >
                                        <CalendarClock class="size-4" /> Jadwalkan Sekarang
                                    </Link>
                                </div>
                            </transition>
                        </div>
                    </nav>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="flex size-9 items-center justify-center rounded-lg text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                        aria-label="Ganti tema"
                        @click="toggleTheme"
                    >
                        <Sun v-if="resolvedAppearance === 'dark'" class="size-5" />
                        <Moon v-else class="size-5" />
                    </button>

                    <Link
                        v-if="!user"
                        href="/login"
                        class="hidden rounded-lg px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground sm:inline-flex sm:items-center sm:gap-1.5"
                    >
                        <LogIn class="size-4" /> Masuk
                    </Link>
                    <Link
                        v-if="user"
                        href="/dashboard"
                        class="hidden rounded-lg bg-foreground px-4 py-2 text-sm font-semibold text-background transition-opacity hover:opacity-90 sm:inline-block"
                    >
                        Dashboard
                    </Link>
                    <Link
                        v-else
                        href="/schedule"
                        class="hidden rounded-lg bg-gradient-to-br from-emerald-400 to-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90 sm:inline-flex sm:items-center sm:gap-1.5"
                    >
                        <CalendarClock class="size-4" /> Konsultasi
                    </Link>

                    <button
                        type="button"
                        class="flex size-9 items-center justify-center rounded-lg text-muted-foreground hover:bg-muted md:hidden"
                        aria-label="Menu"
                        @click="mobileOpen = !mobileOpen"
                    >
                        <X v-if="mobileOpen" class="size-5" />
                        <Menu v-else class="size-5" />
                    </button>
                </div>
            </div>

            <!-- mobile menu -->
            <div v-if="mobileOpen" class="border-t border-border/60 md:hidden">
                <nav class="mx-auto flex max-w-6xl flex-col px-4 py-2">
                    <Link
                        v-for="item in nav"
                        :key="item.href"
                        :href="item.href"
                        class="rounded-lg px-3 py-2.5 text-sm font-medium"
                        :class="isActive(item.href) ? 'bg-muted text-emerald-600 dark:text-emerald-400' : 'text-muted-foreground'"
                        @click="mobileOpen = false"
                    >
                        {{ item.label }}
                    </Link>
                    <Link href="/schedule" class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-medium text-muted-foreground" @click="mobileOpen = false">
                        <CalendarClock class="size-4" /> Jadwal Konsultasi
                    </Link>
                    <Link :href="user ? '/dashboard' : '/schedule'" class="mt-1 rounded-lg bg-gradient-to-br from-emerald-400 to-green-600 px-3 py-2.5 text-center text-sm font-semibold text-white" @click="mobileOpen = false">
                        {{ user ? 'Dashboard' : 'Konsultasi Sekarang' }}
                    </Link>
                </nav>
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <!-- FOOTER -->
        <footer class="border-t border-border/60 bg-muted/30">
            <div class="mx-auto grid max-w-6xl gap-8 px-4 py-12 sm:px-6 md:grid-cols-4">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2.5">
                        <AnimatedLogo :size="36" />
                        <span class="text-lg font-bold">Lentera Project</span>
                    </div>
                    <p class="mt-3 max-w-sm text-sm text-muted-foreground">
                        Konsultan trading yang menerangi jalan investasi Anda — berbasis analisis,
                        disiplin, dan manajemen risiko yang terukur.
                    </p>
                    <p class="mt-4 text-xs text-muted-foreground">
                        Disclaimer: Trading mengandung risiko. Kinerja masa lalu tidak menjamin hasil di masa depan.
                    </p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold">Navigasi</h4>
                    <ul class="mt-3 space-y-2 text-sm text-muted-foreground">
                        <li v-for="item in nav" :key="item.href">
                            <Link :href="item.href" class="hover:text-foreground">{{ item.label }}</Link>
                        </li>
                        <li class="pt-1">
                            <Link :href="user ? '/dashboard' : '/login'" class="inline-flex items-center gap-1.5 font-medium text-emerald-600 hover:text-emerald-500 dark:text-emerald-400">
                                <LogIn class="size-3.5" /> {{ user ? 'Dashboard Admin' : 'Masuk (Admin / Member)' }}
                            </Link>
                        </li>
                        <li v-if="!user">
                            <Link href="/register" class="hover:text-foreground">Daftar akun (lihat PnL penuh)</Link>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold">Kontak</h4>
                    <ul class="mt-3 space-y-2 text-sm text-muted-foreground">
                        <li>Telegram: @lenteraproject</li>
                        <li>Email: halo@lentera.id</li>
                        <li>Indonesia</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-border/60">
                <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-2 px-4 py-4 text-xs text-muted-foreground sm:flex-row sm:px-6">
                    <span>© {{ new Date().getFullYear() }} Lentera Project. Seluruh hak cipta dilindungi.</span>
                    <span>Dibuat dengan Laravel + Vue.</span>
                </div>
            </div>
        </footer>

        <Toaster rich-colors position="top-center" />
    </div>
</template>
