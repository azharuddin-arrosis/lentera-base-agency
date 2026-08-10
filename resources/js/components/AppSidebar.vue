<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Briefcase, CandlestickChart, Globe, LayoutGrid, MessagesSquare, Quote, Radio, Sparkles, Users, Wallet } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

// ── Sumber tunggal aturan menu per role ──────────────────────────────
// Tambah menu baru (mis. Signal, Watchlist Coin, Langganan) cukup di sini.
type NavGroup = { label: string; items: NavItem[] };

const adminMenu: NavGroup[] = [
    { label: 'Ringkasan', items: [{ title: 'Dashboard', href: dashboard(), icon: LayoutGrid }] },
    {
        label: 'Performa & Tim',
        items: [
            { title: 'Team', href: '/admin/members', icon: Users },
            { title: 'Trade & PnL', href: '/admin/trades', icon: CandlestickChart },
            { title: 'Financial Plan', href: '/admin/financial-plan', icon: Wallet },
            { title: 'Sinkron Binance', href: '/admin/binance', icon: Sparkles },
        ],
    },
    {
        label: 'Sinyal & Market',
        items: [
            { title: 'Signal', href: '/admin/signals', icon: Radio },
        ],
    },
    // Fitur Konsultasi disembunyikan sementara (route tetap aktif, cukup aktifkan lagi item ini).
    {
        label: 'Kotak Masuk',
        items: [
            { title: 'Pesan Masuk', href: '/admin/leads', icon: MessagesSquare },
        ],
    },
    {
        label: 'Konten Web',
        items: [
            { title: 'Layanan', href: '/admin/services', icon: Briefcase },
            { title: 'Testimoni', href: '/admin/testimonials', icon: Quote },
        ],
    },
];

const teamMenu: NavGroup[] = [
    {
        label: 'Team',
        items: [
            { title: 'Panel Saya', href: '/panel', icon: LayoutGrid },
        ],
    },
];

// Role 'user'/klien belum punya backoffice — menu minimal saja.
const userMenu: NavGroup[] = [
    { label: 'Menu', items: [{ title: 'Signal', href: '/signals', icon: Radio }] },
];

const menuByRole: Record<string, NavGroup[]> = {
    admin: adminMenu,
    team: teamMenu,
    user: userMenu,
};

const role = computed(
    () => (usePage().props.auth as { user?: { role?: string } } | undefined)?.user?.role ?? 'user',
);
const menuGroups = computed<NavGroup[]>(() => menuByRole[role.value] ?? userMenu);

const footerNavItems: NavItem[] = [
    {
        title: 'Lihat Website',
        href: '/',
        icon: Globe,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain v-for="group in menuGroups" :key="group.label" :label="group.label" :items="group.items" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
