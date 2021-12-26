<template>
    <Head>
        <title>{{ headTitle }}</title>
        <meta name="description" :content="headDescription" />
        <meta name="keywords" :content="headKeywords" />

        <slot />
    </Head>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";

export default {
    components: { Head },
    props: {
        title: String,
        description: String,
        keywords: String,
    },
    computed: {
        siteTitle() {
            return this.$inertia.page.props.settings.title;
        },
        headTitle() {
            return this.title
                ? `${this.siteTitle} - ${this.title}`
                : `${this.siteTitle} - Home`;
        },
        headDescription() {
            return (
                this.description || `Discover the history of ${this.siteTitle}`
            );
        },
        headKeywords() {
            return (
                this.keywords || "history,genealogy,obituaries,family history"
            );
        },
        currentPage() {
            return window.location.href;
        },
    },
};
</script>
