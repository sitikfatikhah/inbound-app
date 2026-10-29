<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import type { NavItem } from '@/types'

import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar'

defineProps<{
    title: string
    items: NavItem[]
}>()

const page = usePage()

const isCurrentUrl = (href: NavItem['href']): boolean => {
    const targetHref = typeof href === 'string' ? href : href?.[0] ?? '/'
    const currentUrl = page.url.split('?')[0].replace(/\/+$/, '') || '/'
    const targetUrl = targetHref.split('?')[0].replace(/\/+$/, '') || '/'

    return (
        currentUrl === targetUrl ||
        (targetUrl !== '/' && currentUrl.startsWith(`${targetUrl}/`))
    )
}
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>
            {{ title }}
        </SidebarGroupLabel>

        <SidebarMenu>
            <SidebarMenuItem
                v-for="item in items"
                :key="item.title"
            >
                <SidebarMenuButton
                    as-child
                    :is-active="isCurrentUrl(item.href)"
                    :tooltip="item.title"
                >
                    <Link :href="item.href">
                        <component
                            :is="item.icon"
                            v-if="item.icon"
                        />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>