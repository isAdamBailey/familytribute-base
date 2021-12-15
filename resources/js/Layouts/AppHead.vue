<template>
    <Head>
        <title>{{ headTitle }}</title>
        <meta
            head-key="description"
            name="description"
            :content="headDescription"
        />
        <meta head-key="keywords" name="keywords" :content="headKeywords" />
        <meta
            head-key="twitter:title"
            name="twitter:title"
            :content="headTitle"
        />
        <meta
            head-key="twitter:site"
            name="twitter:site"
            content="@FamilyTribute"
        />
        <meta
            head-key="twitter:creator"
            name="twitter:creator"
            content="@isAdamBailey"
        />
        <meta
            head-key="twitter:description"
            name="twitter:description"
            :content="headDescription"
        />
        <meta
            head-key="twitter:card"
            name="twitter:card"
            content="summary_large_image"
        />

        <meta head-key="og:url" property="og:url" :content="currentPage" />
        <meta head-key="og:title" property="og:title" :content="headTitle" />
        <meta
            head-key="og:description"
            property="og:description"
            :content="headDescription"
        />
        <meta head-key="og:type" property="og:type" content="website" />
        <meta head-key="og:image" property="og:image" content="" />

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
        headTitle() {
            return this.title
                ? `${this.$inertia.page.props.siteName} - ${this.title}`
                : `${this.$inertia.page.props.siteName} - Home`;
        },
        headDescription() {
            return (
                this.description ||
                `Displaying the history of ${this.$inertia.page.props.siteName}`
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
