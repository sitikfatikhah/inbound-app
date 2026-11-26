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
import { BookOpenTextIcon, Box, ChartNetwork, ChartNoAxesCombinedIcon, Form, HatGlasses, MessageSquareDot, Shield, UserRoundKey } from 'lucide-vue-next';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    
    {
        title: 'KN',
        href: '/knowledge-mgm',
        icon: BookOpen,
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
        title: 'Local Shipments',
        href: '/local-shipment',
        icon: Box,
    },
    {
        title: 'PO',
        href: '/po',
        icon: ClipboardList,
    },
    {
        title: 'Arrival Warehouse',
        href: '/tracking/warehouse',
        icon: Warehouse,
    },
    
    {
        title: 'Custom Clearance',
        href: '/customs-clearance/',
        icon: Form,
    },
    {
        title: 'Custom Clearance Response',
        href: '/customs-clearance-response/',
        icon: HatGlasses,
    },

];

const userManagementItems: NavItem[] = [
    {
        title: 'User',
        href: '/user',
        icon: UserRound,
    },
    {
        title: 'Role',
        href: '/roles',
        icon: Shield,
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
        title: 'Tracking Local PO',
        href: '/tracking/local',
        icon: Truck,
    },
];

const monitoringItems: NavItem[] = [
    {
        title: 'Analytics',
        href: '/analytics',
        icon: ChartNoAxesCombinedIcon,
    },
    {
        title: 'Performance',
        href: '/analytics',
        icon: ChartNetwork,
    },
    {
        title: 'New Project Monitoring',
        href: '/project',
        icon: FolderKanban,
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
            <NavMain :items="mainNavItems" title="Overview" />
            <NavMain :items="trackingItems" title="Shipment Tracking" />
            <NavMain :items="monitoringItems" title="Monitoring & Analytics" />
            <NavMain :items="masterDataItems" title="Master Data" />
            <NavMain :items="transactionItems" title="Transactions" />
            <NavMain :items="userManagementItems" title="User Management" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
