<script setup>
import { ref, computed } from "vue"
import { Link, usePage, router } from "@inertiajs/vue3" // Import usePage and router
import { ArrowRight, Linkedin, Instagram, Mail, Phone } from "lucide-vue-next"

const cols = [
    {
        title: "Portfolio",
        links: [
            { label: "All Portfolio", to: "/portfolio" },
            { label: "Hotels, Resorts & Lodges", to: "/portfolio?cat=Hotels%2C%20Resorts%20%26%20Lodges" },
            { label: "Conference & Unique Venues", to: "/portfolio?cat=Conference%20%26%20Unique%20Venues" },
            { label: "Serviced Residences", to: "/portfolio?cat=Serviced%20Residences" },
            { label: "Destinations & DMOs", to: "/portfolio?cat=Destinations%20%26%20DMOs" },
            { label: "Explore by Location", to: "/portfolio" }
        ]
    },
    {
        title: "Services",
        links: [
            { label: "For Buyers & Planners", to: "/services" },
            { label: "For Represented Members", to: "/services" },
            { label: "Sales Representation", to: "/services" },
            { label: "Commercial Advisory", to: "/services" }
        ]
    },
    {
        title: "Company",
        links: [
            { label: "About Us", to: "/about" },
            { label: "Partners", to: "/partners" },
            { label: "Submit an RFP", to: "/rfp" },
            { label: "Contact Us", to: "/contact" },
            { label: "HMP Agency", to: "/hmp-agency" }
        ]
    },
]

const socialIcons = [
    { icon: Linkedin, link: 'https://www.linkedin.com/company/hmphospitality/' },
    { icon: Instagram, link: 'https://www.instagram.com/hmphospitality.co?stkn=MTNrbjAzbDlod3BkbA==' },
    { icon: Mail, link: 'mailto:hello@hmphospitality.co' }
]

const page = usePage()

const email = ref("")
const done = ref(false)

// Extract the dynamic confirmation message from the controller's session flash
const flashMessage = computed(() => page.props.flash?.message || "Thank you — you're on the list.")

const handleSubscribe = () => {
    if (!email.value) return

    router.post('/newsletter/subscribe', {
        email: email.value
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            done.value = true
            email.value = "" // Clear the input field upon successful subscription
        }
    })
}
</script>

<template>
    <footer class="section-dark">
        <div class="container-wide py-20">
            <div class="grid gap-12 lg:grid-cols-[1.4fr_1fr_1fr_1fr_1.2fr]">
                <!-- Brand and Social Column -->
                <div>
                    <!-- White Image Logo -->
                    <div class="flex items-center">
                        <img src="/logo_white.png" alt="HMP Hospitality" class="h-10 w-auto object-contain" />
                    </div>
                    <p class="mt-5 max-w-xs text-sm leading-relaxed text-ink-fg/70">
                        Bespoke sales & marketing representation connecting independent hotels, resorts, lodges, venues,
                        serviced residences and destinations with qualified global buyers.
                    </p>
                    <div class="mt-6 flex items-center gap-3">
                        <a v-for="(Icon, i) in socialIcons" :key="i" :href="item.link" target="_blank"
                            rel="noopener noreferrer"
                            class="grid place-items-center h-10 w-10 rounded-full border border-white/15 text-ink-fg/80 transition-colors hover:border-primary hover:text-primary">
                            <component :is="Icon" class="h-4 w-4" />
                        </a>
                    </div>
                </div>

                <!-- Navigation Columns -->
                <div v-for="c in cols" :key="c.title">
                    <h4 class="text-xs font-medium uppercase tracking-[0.2em] text-ink-fg/50">{{ c.title }}</h4>
                    <ul class="mt-5 space-y-3">
                        <li v-for="l in c.links" :key="l.label">
                            <Link :href="l.to" class="text-sm text-ink-fg/80 hover:text-white transition-colors">
                                {{ l.label }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Stay in touch Column -->
                <div>
                    <h4 class="text-xs font-medium uppercase tracking-[0.2em] text-ink-fg/50">Stay in touch</h4>
                    <p class="mt-5 text-sm text-ink-fg/70 leading-relaxed">Quarterly insights on African hospitality,
                        destinations and buyer programmes.</p>

                    <!-- Dynamic Post Form -->
                    <form @submit.prevent="handleSubscribe" class="mt-4 flex items-center gap-2">
                        <input v-model="email" type="email" required placeholder="Your email"
                            class="flex-1 rounded-full border border-white/15 bg-white/5 px-4 py-2.5 text-sm text-white placeholder:text-ink-fg/40 focus:border-primary focus:outline-none" />
                        <button
                            class="grid place-items-center h-10 w-10 rounded-full bg-primary text-primary-foreground cursor-pointer">
                            <ArrowRight class="h-4 w-4" />
                        </button>
                    </form>

                    <!-- Renders dynamic success flash message returned from the backend -->
                    <p v-if="done" class="mt-2 text-xs text-primary font-medium leading-relaxed">
                        {{ flashMessage }}
                    </p>

                    <div class="mt-6 space-y-2 text-sm text-ink-fg/70">
                        <p class="flex items-center gap-2">
                            <Mail class="h-4 w-4 text-primary" /> hello@hmphospitality.co
                        </p>
                        <p class="flex items-center gap-2">
                            <Phone class="h-4 w-4 text-primary" /> +254 703 720 000
                        </p>
                        <p class="leading-relaxed">
                            6th Floor, MJ1 Business Park,<br />Westlands Road, Westlands, Nairobi, Kenya
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright & Policies -->
            <div
                class="mt-16 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-white/10 pt-8 text-xs text-ink-fg/50">
                <p>© {{ new Date().getFullYear() }} HMP Hospitality. Where Africa's builders of connection gather.</p>
                <div class="flex items-center gap-6">
                    <Link href="/privacy-policy" class="hover:text-white">Privacy Policy</Link>
                    <Link href="/cookie-policy" class="hover:text-white">Cookie Policy</Link>
                    <Link href="/terms-of-use" class="hover:text-white">Terms of Use</Link>
                </div>
            </div>
        </div>
    </footer>
</template>
