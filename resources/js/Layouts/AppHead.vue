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
        <meta name="twitter:image:alt" :content="siteTitle">

        <meta head-key="og:site_name" property="og:site_name" :content="siteTitle"/>
        <meta head-key="og:url" property="og:url" :content="currentPage" />
        <meta head-key="og:title" property="og:title" :content="headTitle" />
        <meta
            head-key="og:description"
            property="og:description"
            :content="headDescription"
        />
        <meta head-key="og:type" property="og:type" content="website" />
        <meta
            head-key="og:image"
            property="og:image"
            content="/images/undraw_appreciation_re_p6rl.svg"
        />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />

        <link rel="icon" href="/favicon.ico">

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
                this.description ||
                `Displaying the history of ${this.siteTitle}`
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
