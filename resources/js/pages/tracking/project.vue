<script setup lang="ts">
import { computed, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { FolderKanban, Search, Users } from 'lucide-vue-next';

interface Project {
    id: number;
    project_name: string;
    description: string | null;
    start_date: string | null;
    target_date: string | null;
    pic: string | null;
    progress: 'Not Started' | 'In Progress' | 'Completed';
    product?: { product_name: string } | null;
    shipment?: { shipment_number: string } | null;
}
const props = defineProps<{ projects?: Project[] }>();
const search = ref('');
const projects = computed(() => props.projects ?? []);
const filteredProjects = computed(() =>
    projects.value.filter((project) => {
        const keyword = search.value.trim().toLowerCase();
        return (
            !keyword ||
            [
                project.project_name,
                project.product?.product_name,
                project.pic,
                project.progress,
            ].some((value) =>
                String(value ?? '')
                    .toLowerCase()
                    .includes(keyword),
            )
        );
    }),
);
const completed = computed(
    () =>
        projects.value.filter((project) => project.progress === 'Completed')
            .length,
);
const inProgress = computed(
    () =>
        projects.value.filter((project) => project.progress === 'In Progress')
            .length,
);
const overallProgress = computed(() =>
    projects.value.length
        ? Math.round(
              projects.value.reduce(
                  (total, project) => total + progressValue(project.progress),
                  0,
              ) / projects.value.length,
          )
        : 0,
);
function formatDate(value: string | null) {
    return value
        ? new Intl.DateTimeFormat('id-ID', {
              day: '2-digit',
              month: 'short',
              year: 'numeric',
          }).format(new Date(value))
        : '-';
}
function progressValue(value: Project['progress']) {
    return value === 'Completed' ? 100 : value === 'In Progress' ? 50 : 0;
}
</script>

<template>
    <main
        class="min-h-screen w-full bg-slate-50 px-4 pt-8 pb-12 text-slate-900 lg:px-8"
    >
        <div class="mx-auto max-w-[1600px]">
            <section
                class="mb-8 flex flex-col items-start justify-between gap-4 md:flex-row md:items-end"
            >
                <div>
                    <nav
                        class="mb-2 flex items-center gap-2 text-xs text-muted-foreground"
                    >
                        <span>Proyek</span><span>/</span
                        ><span class="font-semibold text-primary"
                            >Monitoring Produk Baru</span
                        >
                    </nav>
                    <h1 class="text-3xl font-bold tracking-tight text-primary">
                        Monitoring Project Produk Baru
                    </h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Lacak kemajuan project, PIC, target, dan shipment
                        terkait secara aktual.
                    </p>
                </div>
                <div
                    class="flex items-center gap-2 rounded-xl border border-border bg-card p-2 shadow-sm"
                >
                    <Users class="size-4 text-primary" /><span
                        class="text-xs font-semibold text-muted-foreground"
                        >{{ projects.length }} project terdata</span
                    >
                </div>
            </section>
            <section class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-4">
                <div
                    class="rounded-xl border border-border bg-card p-5 shadow-sm md:col-span-2"
                >
                    <div class="mb-4 flex items-start justify-between">
                        <div>
                            <p
                                class="text-xs font-bold tracking-wider text-muted-foreground uppercase"
                            >
                                Keseluruhan Progress
                            </p>
                            <h2 class="mt-2 text-4xl font-bold text-primary">
                                {{ overallProgress }}%
                            </h2>
                        </div>
                        <FolderKanban class="size-6 text-primary" />
                    </div>
                    <div class="h-3 overflow-hidden rounded-full bg-muted">
                        <div
                            class="h-full rounded-full bg-primary transition-all"
                            :style="{ width: `${overallProgress}%` }"
                        />
                    </div>
                    <p class="mt-2 text-xs text-muted-foreground">
                        {{ completed }} selesai dari
                        {{ projects.length }} project
                    </p>
                </div>
                <div
                    class="rounded-xl border border-border bg-card p-5 shadow-sm"
                >
                    <p class="text-xs font-bold text-muted-foreground">
                        Level Risiko
                    </p>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="size-3 rounded-full bg-amber-500" /><span
                            class="text-xl font-bold"
                            >{{
                                projects.some(
                                    (project) =>
                                        project.target_date &&
                                        new Date(project.target_date) <
                                            new Date() &&
                                        project.progress !== 'Completed',
                                )
                                    ? 'High'
                                    : 'Low'
                            }}</span
                        >
                    </div>
                    <p class="mt-3 text-xs text-muted-foreground">
                        Berdasarkan target dan progress project.
                    </p>
                </div>
                <div
                    class="rounded-xl bg-primary p-5 text-primary-foreground shadow-sm"
                >
                    <p class="text-xs font-bold uppercase opacity-70">
                        Status Aktif
                    </p>
                    <h2 class="mt-2 text-3xl font-bold">{{ inProgress }}</h2>
                    <p class="mt-2 text-xs opacity-80">
                        Project sedang berjalan
                    </p>
                </div>
            </section>
            <section
                class="mb-8 overflow-hidden rounded-xl border border-border bg-card shadow-sm"
            >
                <div
                    class="flex items-center justify-between border-b border-border p-5"
                >
                    <div>
                        <h2 class="text-lg font-bold text-primary">
                            Project Timeline
                        </h2>
                        <p class="text-xs text-muted-foreground">
                            Timeline diturunkan dari start_date dan target_date.
                        </p>
                    </div>
                    <div class="relative">
                        <Search
                            class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                        /><input
                            v-model="search"
                            class="h-9 w-64 rounded-lg border border-border bg-background pr-3 pl-9 text-xs outline-none focus:ring-2 focus:ring-primary/20"
                            placeholder="Cari project..."
                        />
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <div class="min-w-[900px]">
                        <div
                            class="grid grid-cols-[280px_1fr] border-b border-border bg-muted/30 text-xs font-bold text-muted-foreground"
                        >
                            <div class="p-4">Tahapan & Milestone</div>
                            <div class="grid grid-cols-4">
                                <div class="border-l p-4">START</div>
                                <div class="border-l p-4">PROGRESS</div>
                                <div class="border-l p-4">TARGET</div>
                                <div class="border-l p-4">SHIPMENT</div>
                            </div>
                        </div>
                        <div
                            v-for="project in filteredProjects"
                            :key="project.id"
                            class="grid grid-cols-[280px_1fr] border-b border-border last:border-0"
                        >
                            <div class="p-4">
                                <p class="font-bold text-primary">
                                    {{ project.project_name }}
                                </p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    {{
                                        project.product?.product_name ||
                                        'Produk belum terhubung'
                                    }}
                                </p>
                                <p class="mt-2 text-xs text-muted-foreground">
                                    PIC: {{ project.pic || '-' }}
                                </p>
                            </div>
                            <div class="grid grid-cols-4 items-center">
                                <div class="border-l p-4 text-xs">
                                    {{ formatDate(project.start_date) }}
                                </div>
                                <div class="border-l p-4">
                                    <div
                                        class="flex items-center justify-between text-xs"
                                    >
                                        <span>{{ project.progress }}</span
                                        ><strong
                                            >{{
                                                progressValue(project.progress)
                                            }}%</strong
                                        >
                                    </div>
                                    <div class="mt-2 h-2 rounded-full bg-muted">
                                        <div
                                            class="h-full rounded-full bg-primary"
                                            :style="{
                                                width: `${progressValue(project.progress)}%`,
                                            }"
                                        />
                                    </div>
                                </div>
                                <div class="border-l p-4 text-xs font-semibold">
                                    {{ formatDate(project.target_date) }}
                                </div>
                                <div
                                    class="border-l p-4 text-xs text-muted-foreground"
                                >
                                    {{
                                        project.shipment?.shipment_number ||
                                        'Belum terhubung'
                                    }}
                                </div>
                            </div>
                        </div>
                        <div
                            v-if="!filteredProjects.length"
                            class="p-12 text-center text-sm text-muted-foreground"
                        >
                            Belum ada product project.
                        </div>
                    </div>
                </div>
            </section>
            <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-4 lg:col-span-2">
                    <h2 class="text-lg font-bold text-primary">
                        Project Cards & Status
                    </h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        <Card
                            v-for="project in filteredProjects"
                            :key="`card-${project.id}`"
                            ><CardHeader
                                ><div
                                    class="flex items-start justify-between gap-3"
                                >
                                    <div>
                                        <CardTitle class="text-lg">{{
                                            project.project_name
                                        }}</CardTitle
                                        ><CardDescription>{{
                                            project.product?.product_name ||
                                            'Produk belum terhubung'
                                        }}</CardDescription>
                                    </div>
                                    <Badge variant="secondary">{{
                                        project.progress
                                    }}</Badge>
                                </div></CardHeader
                            ><CardContent
                                ><p
                                    class="line-clamp-2 text-sm text-muted-foreground"
                                >
                                    {{
                                        project.description ||
                                        'Belum ada deskripsi project.'
                                    }}
                                </p>
                                <div
                                    class="mt-4 flex justify-between text-xs text-muted-foreground"
                                >
                                    <span
                                        >Target
                                        {{
                                            formatDate(project.target_date)
                                        }}</span
                                    ><span
                                        >{{
                                            progressValue(project.progress)
                                        }}%</span
                                    >
                                </div></CardContent
                            ></Card
                        >
                    </div>
                </div>
                <div
                    class="rounded-xl border border-border bg-primary p-6 text-primary-foreground shadow-lg"
                >
                    <FolderKanban class="size-8 opacity-80" />
                    <h2 class="mt-5 text-xl font-bold">Ringkasan Project</h2>
                    <p class="mt-2 text-sm opacity-80">
                        Gunakan status progress untuk memantau kesiapan project
                        produk baru.
                    </p>
                    <div class="mt-6 flex flex-col gap-3 text-sm">
                        <div
                            class="flex justify-between border-b border-primary-foreground/20 pb-3"
                        >
                            <span class="opacity-80">Not Started</span
                            ><strong>{{
                                projects.filter(
                                    (project) =>
                                        project.progress === 'Not Started',
                                ).length
                            }}</strong>
                        </div>
                        <div
                            class="flex justify-between border-b border-primary-foreground/20 pb-3"
                        >
                            <span class="opacity-80">In Progress</span
                            ><strong>{{ inProgress }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="opacity-80">Completed</span
                            ><strong>{{ completed }}</strong>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</template>
