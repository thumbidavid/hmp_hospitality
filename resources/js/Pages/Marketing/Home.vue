<script setup>
import { Head } from "@inertiajs/vue3"
import Layout from '@/Components/Public/Layout.vue'
import Hero from '@/Components/Public/home/Hero.vue'
import IntroBrand from '@/Components/Public/home/IntroBrand.vue'
import DestinationDiscovery from '@/Components/Public/home/DestinationDiscovery.vue'
import FeaturedCollection from '@/Components/Public/home/FeaturedCollection.vue'
import CampaignPanel from '@/Components/Public/home/CampaignPanel.vue'
import BuyerJourney from '@/Components/Public/home/BuyerJourney.vue'
import RepresentationSection from '@/Components/Public/home/RepresentationSection.vue'
import StoriesSection from '@/Components/Public/home/StoriesSection.vue'
import AgencyCrossPromo from '@/Components/Public/home/AgencyCrossPromo.vue'
import FinalRfp from '@/Components/Public/home/FinalRfp.vue'

const PANEL_MEETINGS = "/assets/public/images/panel_meetings.jpeg"
const PANEL_RESIDENCES = "/assets/public/images/panel_residences.jpeg"

// Set persistent layout for Inertia
defineOptions({
    layout: Layout
})
</script>

<template>

    <Head>
        <title>HMP Hospitality</title>
        <meta name="description"
            content="HMP Hospitality represents a curated collection of independent hotels, resorts, venues, serviced residences, and destinations in priority global markets." />
        <meta name="keywords"
            content="luxury hotels, safari lodges, serviced residences, MICE venues, corporate event planners, hotel representation Africa" />
        <meta property="og:title" content="HMP Hospitality — Exceptional Places. Meaningful Connections." />
        <meta property="og:description"
            content="Bespoke sales and marketing representation connecting independent hotels, resorts, venues, serviced residences, and destinations with qualified global buyers." />
        <meta property="og:image"
            content="https://media.base44.com/images/public/6a0f7d8de59727e2def89502/89a0e9bff_generated_image.png" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
    </Head>

    <Hero />
    <IntroBrand />
    <DestinationDiscovery />
    <FeaturedCollection />
    <CampaignPanel :image="PANEL_MEETINGS" label="Meetings & Venues"
        headline="Places Designed for Meaningful Gatherings"
        copy="From executive retreats to international conferences, discover settings that bring people and ideas together."
        buttonLabel="Explore Meetings & Venues" to="/portfolio?cat=Conference%20%26%20Unique%20Venues" />
    <BuyerJourney />
    <CampaignPanel :image="PANEL_RESIDENCES" label="Serviced Residences" headline="Stay Longer. Feel at Home."
        copy="Discover serviced residences designed for business travellers, project teams and extended stays."
        buttonLabel="Explore Serviced Residences" to="/portfolio?cat=Serviced%20Residences" reverse />
    <RepresentationSection />
    <StoriesSection />
    <AgencyCrossPromo />
    <FinalRfp />
</template>
