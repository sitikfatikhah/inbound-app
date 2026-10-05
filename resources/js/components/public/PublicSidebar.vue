<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import {
    BarChart3,
    Boxes,
    CalendarDays,
    ClipboardList,
    LayoutDashboard,
    Package,
    Warehouse,
    Wrench,
    X,
    Menu,
} from 'lucide-vue-next'
import { computed, ref } from 'vue'

import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar'

interface NavigationItem {
    title: string
    href: string
    icon: any
}

const page = usePage()

const sidebarOpen = ref(false)

const navigation: NavigationItem[] = [
    {
        title: 'Dashboard',
        href: '/tracking-portal',
        icon: LayoutDashboard,
    },
    {
        title: 'Tracking Import',
        href: '/tracking-import',
        icon: Package,
    },
    {
        title: 'Tracking Local',
        href: '/tracking-local',
        icon: ClipboardList,
    },

    {
        title: 'Barang Masuk Gudang',
        href: '/arrival-warehouse',
        icon: Warehouse,
    },
    {
        title: 'Knowledge Management',
        href: '/knowledge-mgm',
        icon: Boxes,
    },
    {
        title: 'Monitoring Proyek Produk Baru',
        href: '/new-project-monitoring',
        icon: Wrench,
    },
    {
        title: 'Analisis & Insight',
        href: '/analytics',
        icon: BarChart3,
    },
    
]

const managementNavigation: NavigationItem[] = [
    // Aktifkan jika menu management sudah digunakan
    // {
    //     title: 'Master Data',
    //     href: '/control-tower/master-data',
    //     icon: Database,
    // },
    // {
    //     title: 'Log Aktivitas',
    //     href: '/control-tower/activity-logs',
    //     icon: History,
    // },
    // {
    //     title: 'Pengaturan',
    //     href: '/control-tower/settings',
    //     icon: Settings,
    // },
]

const isActive = (href: string) => {
    const currentUrl = page.url

    if (href === '/tracking-portal') {
        return currentUrl === href || currentUrl === `${href}/`
    }

    return currentUrl === href || currentUrl.startsWith(`${href}/`)
}

const currentUser = computed(() => {
    return page.props.auth?.user ?? null
})

const closeSidebar = () => {
    sidebarOpen.value = false
}
</script>

<template>
    <!-- =========================================================
         MOBILE HEADER
    ========================================================== -->
    <header
        class="fixed inset-x-0 top-0 z-40 flex h-16 items-center justify-between border-b bg-background px-4 shadow-sm lg:hidden">
        <Link href="/tracking-portal" class="flex min-w-0 items-center gap-2" @click="closeSidebar">
            <div
                class="flex size-9 shrink-0 items-center justify-center rounded-md bg-[#00355f] text-lg font-semibold text-white">
                C
            </div>

            <div class="min-w-0">
                <h1 class="text-base font-semibold leading-tight text-[#00355f]">
                    PT. CSA
                </h1>

                <p class="truncate text-[8px] font-medium uppercase tracking-[0.12em] text-muted-foreground">
                    Supply Chain Control Tower
                </p>
            </div>
        </Link>

        <button type="button"
            class="inline-flex size-9 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
            aria-label="Buka menu" @click="sidebarOpen = true">
            <Menu class="size-5" />
        </button>
    </header>

    <!-- =========================================================
         MOBILE SIDEBAR
    ========================================================== -->
    <Transition name="fade">
        <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-black/40 backdrop-blur-[1px] lg:hidden"
            @click="closeSidebar" />
    </Transition>

    <!-- =========================================================
         SHADCN SIDEBAR
    ========================================================== -->
    <Sidebar collapsible="icon" variant="inset" class="hidden lg:flex">
        <!-- =====================================================
             BRAND
        ====================================================== -->
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton as-child size="lg" tooltip="PT. CSA - Supply Chain Control Tower">
                        <Link href="/tracking-portal" @click="closeSidebar">
                            <div
                                class="flex size-8 shrink-0 items-center justify-center rounded-md bg-[#00355f] text-base font-semibold text-white">
                                C
                            </div>

                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold text-[#00355f]">
                                    PT. CSA
                                </span>

                                <span
                                    class="truncate text-[9px] font-medium uppercase tracking-[0.1em] text-muted-foreground">
                                    Supply Chain Control Tower
                                </span>
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <!-- =====================================================
             CONTENT
        ====================================================== -->
        <SidebarContent>
            <!-- MAIN NAVIGATION -->
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel>
                    Control Tower
                </SidebarGroupLabel>

                <SidebarMenu>
                    <SidebarMenuItem v-for="item in navigation" :key="item.href">
                        <SidebarMenuButton as-child :is-active="isActive(item.href)" :tooltip="item.title">
                            <Link :href="item.href" @click="closeSidebar">
                                <component :is="item.icon" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- MANAGEMENT -->
            <SidebarGroup v-if="managementNavigation.length" class="px-2 py-0">
                <SidebarGroupLabel>
                    Manajemen Sistem
                </SidebarGroupLabel>

                <SidebarMenu>
                    <SidebarMenuItem v-for="item in managementNavigation" :key="item.href">
                        <SidebarMenuButton as-child :is-active="isActive(item.href)" :tooltip="item.title">
                            <Link :href="item.href" @click="closeSidebar">
                                <component :is="item.icon" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- SUPPORT -->
            <SidebarGroup class="mt-auto px-2 py-0">
                <div class="rounded-lg bg-[#0f4c81] p-3 text-white">
                    <p class="mb-1 text-[10px] font-medium uppercase tracking-[0.12em] text-blue-100">
                        Pusat Bantuan
                    </p>

                    <p class="mb-3 text-sm font-medium">
                        Butuh bantuan teknis?
                    </p>

                    <button type="button"
                        class="w-full rounded-md bg-white/10 px-3 py-2 text-xs font-medium transition-colors hover:bg-white/20">
                        Hubungi Support
                    </button>
                </div>
            </SidebarGroup>
        </SidebarContent>

        <!-- =====================================================
             USER
        ====================================================== -->
        <SidebarFooter v-if="currentUser">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" tooltip="Profil pengguna">
                        <div
                            class="flex size-8 shrink-0 items-center justify-center rounded-full bg-muted text-sm font-semibold text-[#00355f]">
                            {{
                                currentUser.name
                                    ?.charAt(0)
                                    ?.toUpperCase()
                            }}
                        </div>

                        <div class="grid flex-1 text-left text-sm leading-tight">
                            <span class="truncate font-medium">
                                {{ currentUser.name }}
                            </span>

                            <span class="truncate text-xs text-muted-foreground">
                                {{ currentUser.email }}
                            </span>
                        </div>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarFooter>
    </Sidebar>

    <!-- =========================================================
         MOBILE SIDEBAR
    ========================================================== -->
    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-72 max-w-[85vw] flex-col border-r bg-background shadow-xl transition-transform duration-300 ease-in-out lg:hidden"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <!-- MOBILE BRAND -->
        <div class="flex h-16 items-center justify-between border-b px-4">
            <Link href="/tracking-portal" class="flex min-w-0 items-center gap-3" @click="closeSidebar">
                <div
                    class="flex size-9 shrink-0 items-center justify-center rounded-md bg-[#00355f] text-lg font-semibold text-white">
                    C
                </div>

                <div class="grid min-w-0 text-left leading-tight">
                    <span class="truncate text-sm font-semibold text-[#00355f]">
                        PT. CSA
                    </span>

                    <span class="truncate text-[9px] font-medium uppercase tracking-[0.1em] text-muted-foreground">
                        Supply Chain Control Tower
                    </span>
                </div>
            </Link>

            <button type="button"
                class="inline-flex size-9 shrink-0 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                aria-label="Tutup menu" @click="closeSidebar">
                <X class="size-5" />
            </button>
        </div>

        <!-- MOBILE NAVIGATION -->
        <div class="flex-1 overflow-y-auto px-2 py-4">
            <div class="mb-4">
                <p class="mb-2 px-2 text-xs font-medium text-muted-foreground">
                    Control Tower
                </p>

                <div class="space-y-1">
                    <Link v-for="item in navigation" :key="item.href" :href="item.href"
                        class="flex h-8 w-full items-center gap-2 rounded-md px-2 text-left text-sm font-medium outline-none transition-colors focus-visible:ring-2 focus-visible:ring-ring"
                        :class="isActive(item.href)
                            ? 'bg-accent text-accent-foreground'
                            : 'text-foreground hover:bg-accent hover:text-accent-foreground'
                            " @click="closeSidebar">
                        <component :is="item.icon" class="size-4 shrink-0" />

                        <span class="truncate">
                            {{ item.title }}
                        </span>
                    </Link>
                </div>
            </div>

            <div v-if="managementNavigation.length" class="border-t pt-4">
                <p class="mb-2 px-2 text-xs font-medium text-muted-foreground">
                    Manajemen Sistem
                </p>

                <div class="space-y-1">
                    <Link v-for="item in managementNavigation" :key="item.href" :href="item.href"
                        class="flex h-8 w-full items-center gap-2 rounded-md px-2 text-left text-sm font-medium outline-none transition-colors focus-visible:ring-2 focus-visible:ring-ring"
                        :class="isActive(item.href)
                            ? 'bg-accent text-accent-foreground'
                            : 'text-foreground hover:bg-accent hover:text-accent-foreground'
                            " @click="closeSidebar">
                        <component :is="item.icon" class="size-4 shrink-0" />

                        <span class="truncate">
                            {{ item.title }}
                        </span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- MOBILE SUPPORT -->
        <div class="px-3 pb-3">
            <div class="rounded-lg bg-[#0f4c81] p-3 text-white">
                <p class="mb-1 text-[10px] font-medium uppercase tracking-[0.12em] text-blue-100">
                    Pusat Bantuan
                </p>

                <p class="mb-3 text-sm font-medium">
                    Butuh bantuan teknis?
                </p>

                <button type="button"
                    class="w-full rounded-md bg-white/10 px-3 py-2 text-xs font-medium transition-colors hover:bg-white/20">
                    Hubungi Support
                </button>
            </div>
        </div>

        <!-- MOBILE USER -->
        <div v-if="currentUser" class="border-t px-3 py-3">
            <div class="flex items-center gap-3">
                <div
                    class="flex size-8 shrink-0 items-center justify-center rounded-full bg-muted text-sm font-semibold text-[#00355f]">
                    {{
                        currentUser.name
                            ?.charAt(0)
                            ?.toUpperCase()
                    }}
                </div>

                <div class="grid min-w-0 text-left leading-tight">
                    <span class="truncate text-sm font-medium">
                        {{ currentUser.name }}
                    </span>

                    <span class="truncate text-xs text-muted-foreground">
                        {{ currentUser.email }}
                    </span>
                </div>
            </div>
        </div>
    </aside>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>