<script setup>
import { computed } from "vue"
import { Link, usePage, Head } from "@inertiajs/vue3"
import { ArrowRight, ArrowLeft, Clock, Calendar, ArrowUpRight } from "lucide-vue-next"
import Layout from "@/Components/Public/Layout.vue"
import Reveal from "@/Components/Public/shared/Reveal.vue"

const page = usePage()

const article = computed(() => page.props.article)
const related = computed(() => page.props.related || [])

// Dynamic parser to extract headings (H2/H3) and inject scroll-anchors natively
const parsedContent = computed(() => {
    if (!article.value?.content) return { html: "", headings: [] }

    const parser = new DOMParser()
    const doc = parser.parseFromString(article.value.content, 'text/html')
    const headingsList = doc.querySelectorAll('h2, h3')

    // Extract labels and inject relative IDs directly into the DOM
    const headings = Array.from(headingsList).map((h, i) => {
        const id = `s${i}`
        h.setAttribute('id', id)
        h.setAttribute('class', 'scroll-mt-28 font-heading text-2xl lg:text-3xl text-ink mt-8 mb-4')
        return { id, h: h.textContent || '' }
    })

    return {
        html: doc.body.innerHTML,
        headings
    }
})

defineOptions({
    layout: Layout
})
</script>

<template>
    <article v-if="article">
        <!-- World-Class SEO Metadata -->

        <Head>
            <title>{{ article.title }}</title>
            <meta name="description" :content="article.excerpt" />
            <meta property="og:title" :content="`${article.title} — HMP Hospitality`" />
            <meta property="og:description" :content="article.excerpt" />
            <meta property="og:image" :content="article.image" />
            <meta property="og:type" content="article" />
            <meta name="twitter:card" content="summary_large_image" />
        </Head>

        <!-- Gallery Hero -->
        <section class="relative h-[58vh] min-h-[26rem] flex items-end overflow-hidden">
            <img :src="article.image" :alt="article.title"
                class="absolute inset-0 h-full w-full object-cover animate-kenburns" />
            <div class="absolute inset-0 bg-gradient-to-t from-ink/90 via-ink/45 to-ink/15" />
            <div class="container-wide relative pt-28 pb-12 text-white">
                <Link href="/stories"
                    class="inline-flex items-center gap-2 text-sm text-white/70 hover:text-white transition-colors">
                    <ArrowLeft class="h-4 w-4" /> Stories from Our Collection
                </Link>
                <div>
                    <span
                        class="mt-6 inline-block rounded-full bg-white/90 px-3 py-1 text-[11px] font-medium uppercase tracking-wider text-ink">
                        {{ article.category }}
                    </span>
                </div>
                <h1 class="mt-5 max-w-3xl font-heading text-3xl sm:text-5xl lg:text-6xl leading-[1.05] text-balance">
                    {{ article.title }}
                </h1>
            </div>
        </section>

        <!-- Metadata bar -->
        <div class="border-b border-border bg-card">
            <div class="container-wide flex flex-wrap items-center gap-6 py-5 text-sm text-muted-foreground">
                <span class="flex items-center gap-2">
                    <Calendar class="h-4 w-4 text-primary" /> {{ article.date }}
                </span>
                <span class="flex items-center gap-2">
                    <Clock class="h-4 w-4 text-primary" /> {{ article.read }} read
                </span>
                <span class="flex items-center gap-2">By {{ article.author }}</span>
            </div>
        </div>

        <!-- Article Body -->
        <section class="container-wide py-16 lg:py-24">
            <div class="grid gap-12 lg:grid-cols-[1fr_2.4fr]">
                <!-- Sidebar Table of Contents (Desktop only) -->
                <aside class="hidden lg:block">
                    <div class="sticky top-28 space-y-3">
                        <p class="text-xs font-medium uppercase tracking-[0.2em] text-ink/50 mb-3">In this story</p>
                        <a v-for="s in parsedContent.headings" :key="s.id" :href="`#${s.id}`"
                            class="block text-sm text-ink/70 hover:text-primary transition-colors py-0.5">
                            {{ s.h }}
                        </a>

                        <div class="mt-6 rounded-2xl border border-border bg-sand/40 p-5">
                            <p class="text-sm text-muted-foreground leading-relaxed">
                                Planning a programme? We connect briefs with the right members.
                            </p>
                            <Link href="/rfp" class="btn-primary mt-4 w-full justify-center">
                                Submit an RFP
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </div>
                    </div>
                </aside>

                <!-- Editorial Content Column -->
                <Reveal class="max-w-2xl">
                    <p class="font-heading text-2xl leading-relaxed text-ink/90 mb-10">
                        {{ article.excerpt }}
                    </p>

                    <!-- Render dynamic HTML content parsed with coordinates -->
                    <div v-html="parsedContent.html"
                        class="prose max-w-none text-foreground/80 leading-[1.8] space-y-6 text-[17px]" />

                    <!-- Bottom Nav -->
                    <div class="mt-14 flex items-center justify-between border-t border-border pt-8">
                        <Link href="/stories" class="editorial-link">
                            <ArrowLeft class="h-4 w-4 arr rotate-180" /> All stories
                        </Link>
                        <Link href="/rfp" class="btn-primary">
                            Submit an RFP
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </Reveal>
            </div>
        </section>

        <!-- Related Stories -->
        <section v-if="related.length > 0" class="bg-sand/40">
            <div class="container-wide py-16 lg:py-24">
                <div class="flex items-end justify-between gap-6">
                    <h2 class="font-heading text-2xl lg:text-3xl">More from the collection</h2>
                    <Link href="/" class="editorial-link hidden sm:inline-flex">
                        All stories
                        <ArrowUpRight class="h-4 w-4 arr" />
                    </Link>
                </div>

                <div class="mt-10 grid gap-6 sm:grid-cols-2">
                    <Reveal v-for="(a, i) in related" :key="a.id" :delay="i * 0.06">
                        <Link :href="`/stories/${a.id}`" class="group block h-full">
                            <div class="relative overflow-hidden rounded-2xl aspect-[16/10]">
                                <img :src="a.image" :alt="a.title" loading="lazy"
                                    class="h-full w-full object-cover img-rise" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/45 to-transparent" />
                                <span
                                    class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-[11px] font-medium text-ink shadow-sm">
                                    {{ a.category }}
                                </span>
                            </div>
                            <h3
                                class="mt-4 font-heading text-xl group-hover:text-primary transition-colors leading-snug">
                                {{ a.title }}
                            </h3>
                            <span class="mt-2 inline-flex items-center gap-2 text-xs text-muted-foreground">
                                <Clock class="h-3 w-3 text-primary" /> {{ a.read }} · {{ a.date }}
                            </span>
                        </Link>
                    </Reveal>
                </div>
            </div>
        </section>
    </article>
</template>