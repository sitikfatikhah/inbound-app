<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen, Building2, CalendarDays, ClipboardList, ContainerIcon, FolderGit2, FolderKanban, LayoutGrid, Package, PackageSearch, Ship, Truck, UserRound, Users, Warehouse } from '@lucide/vue';
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
import { ChartNoAxesCombinedIcon } from 'lucide-vue-next';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
];

const masterDataItems: NavItem[] = [
    {
        title: 'Forwarder',
        href: '/forwarders',
        icon: Truck,
    },
    {
        title: 'Port',
        href: '/ports',
        icon: Ship,
    },
    {
        title: 'Warehouses',
        href: '/warehouses',
        icon: Warehouse,
    },
    {
        title: 'Shipping Line',
        href: '/shippingLine',
        icon: ContainerIcon,
    },
    {
        title: 'Product',
        href: '/products',
        icon: Package,
    },
    {
        title: 'Supplier',
        href: '/suppliers',
        icon: Users,
    },
];

const transactionItems: NavItem[] = [
    {
        title: 'Shipment',
        href: '/shipments',
        icon: Ship,
    },
    {
        title: 'PO',
        href: '/po',
        icon: ClipboardList,
    },
];

const userManagementItems: NavItem[] = [
    {
        title: 'User',
        href: '/users',
        icon: UserRound,
    },
];

const trackingItems: NavItem[] = [
    {
        title: 'Arrival Schedule',
        href: '/tracking/schedule',
        icon: CalendarDays,
    },
    {
        title: 'Tracking Import',
        href: '/tracking/import',
        icon: PackageSearch,
    },
    {
        title: 'Tracking Local',
        href: '/tracking/local',
        icon: Truck,
    },
    {
        title: 'Arrival Warehouse',
        href: '/tracking/warehouse',
        icon: Warehouse,
    },
];

const monitoringItems: NavItem[] = [
    {
        title: 'Analytics',
        href: '/analytics',
        icon: ChartNoAxesCombinedIcon,
    },
    {
        title: 'New Project Monitoring',
        href: '/project',
        icon: FolderKanban,
    },
    {
        title: 'Knowledge Management',
        href: '/knowledge-mgm',
        icon: BookOpen,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
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
            <NavMain :items="mainNavItems" />
            <NavMain :items="masterDataItems" />
            <NavMain :items="transactionItems" />
            <NavMain :items="userManagementItems" />
            <NavMain :items="trackingItems" />
            <NavMain :items="monitoringItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
