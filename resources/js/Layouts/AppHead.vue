<template>
    <Head>
        <title>{{ displayTitle }}</title>

        <slot />
    </Head>
</template>

<script>
import { Head } from "@inertiajs/vue3";
import { defineComponent } from "vue";

export default defineComponent({
    components: { Head },
    props: {
        title: { type: String },
    },
    computed: {
        displayTitle() {
            const metaTitle = this.$page.props.meta?.title;

            return this.title && metaTitle
                ? `${this.title} - ${metaTitle}`
                : metaTitle || this.title;
        },
        meta() {
            return this.$page.props.meta?.meta || [];
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
        const description = document.querySelector('meta[name="description"]');
        description
            ? description.setAttribute("content", this.description)
            : null;

        const ogImage = document.querySelector('meta[property="og:image"]');
        ogImage ? ogImage.setAttribute("content", this.ogImage) : null;
        const ogDescription = document.querySelector(
            'meta[property="og:description"]'
        );
        ogDescription
            ? ogDescription.setAttribute("content", this.ogDescription)
            : null;
        const ogUrl = document.querySelector('meta[property="og:url"]');
        ogUrl ? ogUrl.setAttribute("content", this.ogUrl) : null;
        const twitterImage = document.querySelector(
            'meta[name="twitter:image"]'
        );
        twitterImage
            ? twitterImage.setAttribute("content", this.twitterImage)
            : null;
        const twitterDescription = document.querySelector(
            'meta[name="twitter:description"]'
        );
        twitterDescription
            ? twitterDescription.setAttribute(
                  "content",
                  this.twitterDescription
              )
            : null;
    },
});
</script>
