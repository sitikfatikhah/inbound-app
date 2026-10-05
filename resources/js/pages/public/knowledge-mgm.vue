<script setup lang="ts">
import { computed, ref } from 'vue';
import {
    BookOpen,
    ChevronRight,
    Gavel,
    Search,
    ShieldCheck,
} from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

interface Article {
    id: number;
    title: string;
    slug: string;
    content: string;
    category: string | null;
    author: string | null;
    is_published: boolean;
}
const props = defineProps<{ articles?: Article[] }>();
const search = ref('');
const category = ref('all');
const articles = computed(() => props.articles ?? []);
const categories = computed(
    () =>
        [
            ...new Set(
                articles.value
                    .map((article) => article.category)
                    .filter(Boolean),
            ),
        ] as string[],
);
const filteredArticles = computed(() =>
    articles.value.filter((article) => {
        const keyword = search.value.trim().toLowerCase();
        const matchesSearch =
            !keyword ||
            [
                article.title,
                article.category,
                article.author,
                article.content,
            ].some((value) =>
                String(value ?? '')
                    .toLowerCase()
                    .includes(keyword),
            );
        return (
            matchesSearch &&
            (category.value === 'all' || article.category === category.value)
        );
    }),
);
const featured = computed(() => filteredArticles.value[0]);
function excerpt(content: string, length = 220) {
    return content.length > length ? `${content.slice(0, length)}...` : content;
}
</script>

<template>
    <main
        class="min-h-screen w-full bg-slate-50 px-4 pt-8 pb-12 text-slate-900 lg:px-8"
    >
        <div class="mx-auto max-w-[1440px]">
            <section
                class="relative mb-10 flex min-h-[280px] items-center overflow-hidden rounded-3xl bg-primary p-8 shadow-xl md:p-12"
            >
                <div class="relative z-10 max-w-3xl text-primary-foreground">
                    <p
                        class="mb-3 text-xs font-bold tracking-[0.18em] uppercase opacity-75"
                    >
                        Knowledge Management
                    </p>
                    <h1 class="text-4xl font-bold tracking-tight md:text-5xl">
                        Pusat Pengetahuan CSA
                    </h1>
                    <p class="mt-4 max-w-2xl text-base leading-7 opacity-85">
                        Panduan regulasi, prosedur, dan insight operasional yang
                        dipublikasikan untuk membantu tim mengambil keputusan
                        lebih cepat.
                    </p>
                    <div class="mt-6 flex items-center gap-3">
                        <BookOpen class="size-6 opacity-80" /><span
                            class="text-sm"
                            >{{ articles.length }} artikel published</span
                        >
                    </div>
                </div>
                <BookOpen
                    class="absolute -right-8 -bottom-8 size-72 text-white/10"
                />
            </section>
            <section class="mb-10 grid grid-cols-1 gap-6 md:grid-cols-3">
                <div
                    class="rounded-2xl border border-border bg-card p-8 shadow-sm md:col-span-2"
                >
                    <div v-if="featured">
                        <div
                            class="mb-6 flex items-start justify-between gap-4"
                        >
                            <Badge>{{
                                featured.category || 'Artikel pilihan'
                            }}</Badge
                            ><span class="text-xs text-muted-foreground"
                                >Oleh {{ featured.author || 'Tim CSA' }}</span
                            >
                        </div>
                        <h2 class="text-2xl font-bold text-primary">
                            {{ featured.title }}
                        </h2>
                        <p
                            class="mt-3 max-w-2xl text-sm leading-7 text-muted-foreground"
                        >
                            {{ excerpt(featured.content) }}
                        </p>
                        <button
                            class="mt-6 inline-flex items-center gap-2 text-sm font-bold text-primary hover:gap-3"
                        >
                            Baca selengkapnya <ChevronRight class="size-4" />
                        </button>
                    </div>
                    <div v-else class="flex min-h-40 flex-col justify-center">
                        <BookOpen class="size-10 text-muted-foreground" />
                        <h2 class="mt-4 text-xl font-bold">
                            Belum ada artikel published
                        </h2>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Artikel yang dipublikasikan akan tampil sebagai
                            highlight.
                        </p>
                    </div>
                </div>
                <div
                    class="rounded-2xl border border-border bg-muted/30 p-8 shadow-sm"
                >
                    <Gavel class="size-10 text-destructive" />
                    <h2 class="mt-6 text-xl font-bold">Regulasi & Kepatuhan</h2>
                    <p class="mt-3 text-sm leading-6 text-muted-foreground">
                        Cari artikel berdasarkan kategori untuk menemukan
                        prosedur yang relevan.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-2">
                        <button
                            v-for="item in categories.slice(0, 4)"
                            :key="item"
                            class="rounded-full border border-border bg-card px-3 py-1.5 text-xs font-semibold hover:border-primary hover:text-primary"
                            @click="category = item"
                        >
                            {{ item }}
                        </button>
                    </div>
                </div>
            </section>
            <section class="mb-10 grid grid-cols-1 gap-8 lg:grid-cols-12">
                <div class="lg:col-span-8">
                    <div
                        class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end"
                    >
                        <div>
                            <h2 class="text-2xl font-bold text-primary">
                                Resource & Best Practices
                            </h2>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Artikel terbaru dari knowledge_articles.
                            </p>
                        </div>
                        <div class="relative">
                            <Search
                                class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                            /><input
                                v-model="search"
                                class="h-10 w-64 rounded-lg border border-border bg-card pr-3 pl-9 text-sm outline-none focus:ring-2 focus:ring-primary/20"
                                placeholder="Cari artikel..."
                            />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <article
                            v-for="article in filteredArticles"
                            :key="article.id"
                            class="group overflow-hidden rounded-2xl border border-border bg-card shadow-sm transition hover:-translate-y-1 hover:shadow-md"
                        >
                            <div
                                class="flex h-28 items-center justify-center bg-primary/10 text-primary"
                            >
                                <BookOpen
                                    class="size-12 transition-transform group-hover:scale-110"
                                />
                            </div>
                            <div class="p-5">
                                <div
                                    class="mb-3 flex items-center justify-between gap-3"
                                >
                                    <Badge variant="outline">{{
                                        article.category || 'Umum'
                                    }}</Badge
                                    ><span
                                        class="text-[10px] text-muted-foreground"
                                        >{{ article.author || 'Tim CSA' }}</span
                                    >
                                </div>
                                <h3
                                    class="text-lg font-bold text-foreground group-hover:text-primary"
                                >
                                    {{ article.title }}
                                </h3>
                                <p
                                    class="mt-2 text-sm leading-6 text-muted-foreground"
                                >
                                    {{ excerpt(article.content, 130) }}
                                </p>
                            </div>
                        </article>
                    </div>
                    <div
                        v-if="!filteredArticles.length"
                        class="rounded-xl border border-dashed p-12 text-center"
                    >
                        <BookOpen
                            class="mx-auto size-10 text-muted-foreground"
                        />
                        <p class="mt-3 font-semibold">
                            Artikel tidak ditemukan
                        </p>
                    </div>
                </div>
                <aside class="space-y-6 lg:col-span-4">
                    <Card
                        ><CardHeader
                            ><div class="flex items-center gap-3">
                                <ShieldCheck
                                    class="size-5 text-primary"
                                /><CardTitle>Filter Artikel</CardTitle>
                            </div>
                            <CardDescription
                                >Pilih kategori artikel.</CardDescription
                            ></CardHeader
                        ><CardContent
                            ><select
                                v-model="category"
                                class="h-10 w-full rounded-lg border border-border bg-background px-3 text-sm"
                            >
                                <option value="all">Semua kategori</option>
                                <option
                                    v-for="item in categories"
                                    :key="item"
                                    :value="item"
                                >
                                    {{ item }}
                                </option>
                            </select></CardContent
                        ></Card
                    ><Card
                        ><CardHeader
                            ><CardTitle>Ringkasan</CardTitle></CardHeader
                        ><CardContent
                            ><div class="flex flex-col gap-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground"
                                        >Published</span
                                    ><strong>{{ articles.length }}</strong>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground"
                                        >Kategori</span
                                    ><strong>{{ categories.length }}</strong>
                                </div>
                            </div></CardContent
                        ></Card
                    >
                </aside>
            </section>
            <section
                class="rounded-3xl border border-border bg-card p-8 md:p-12"
            >
                <div class="mb-8 text-center">
                    <h2 class="text-2xl font-bold text-primary">
                        Frequently Asked Questions
                    </h2>
                    <p class="mt-2 text-sm text-muted-foreground">
                        Pertanyaan dapat ditambahkan sebagai artikel knowledge
                        base.
                    </p>
                </div>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <details
                        v-for="article in filteredArticles.slice(0, 4)"
                        :key="`faq-${article.id}`"
                        class="group border-b border-border pb-4"
                    >
                        <summary
                            class="flex cursor-pointer list-none items-center justify-between gap-4 text-sm font-bold"
                        >
                            <span>{{ article.title }}</span
                            ><ChevronRight
                                class="size-4 transition-transform group-open:rotate-90"
                            />
                        </summary>
                        <p class="mt-3 text-sm leading-6 text-muted-foreground">
                            {{ excerpt(article.content, 240) }}
                        </p>
                    </details>
                    <p
                        v-if="!filteredArticles.length"
                        class="text-sm text-muted-foreground md:col-span-2"
                    >
                        Belum ada FAQ dari artikel published.
                    </p>
                </div>
            </section>
        </div>
    </main>
</template>
