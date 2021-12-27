<template>
    <Head>
        <title>{{ $page.props.meta.title }}</title>

        <slot />
    </Head>
</template>

<script>
import { Head } from "@inertiajs/inertia-vue3";

export default {
    components: { Head },
    computed: {
        meta() {
            return this.$inertia.page.props.meta.meta;
        },
        description() {
            return this.meta.find((item) => item.name === "description")
                ?.content;
        },
        ogDescription() {
            return this.meta.find((item) => item.property === "og:description")
                ?.content;
        },
        ogUrl() {
            return this.meta.find((item) => item.property === "og:url")
                ?.content;
        },
        ogImage() {
            return this.meta.find((item) => item.property === "og:image")
                ?.content;
        },
        twitterImage() {
            return this.meta.find((item) => item.name === "twitter:image")
                ?.content;
        },
        twitterDescription() {
            return this.meta.find((item) => item.name === "twitter:description")
                ?.content;
        },
    },
    mounted() {
        document
            .querySelector('meta[name="description"]')
            .setAttribute("content", this.description);
        document
            .querySelector('meta[property="og:image"]')
            .setAttribute("content", this.ogImage);
        document
            .querySelector('meta[property="og:description"]')
            .setAttribute("content", this.ogDescription);
        document
            .querySelector('meta[property="og:url"]')
            .setAttribute("content", this.ogUrl);
        document
            .querySelector('meta[name="twitter:image"]')
            .setAttribute("content", this.twitterImage);
        document
            .querySelector('meta[name="twitter:description"]')
            .setAttribute("content", this.twitterDescription);
    },
};
</script>
