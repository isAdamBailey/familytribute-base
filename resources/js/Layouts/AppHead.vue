<template>
    <Head>
        <title>{{ displayTitle }}</title>

        <slot />
    </Head>
</template>

<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed, onMounted } from "vue";

const props = defineProps({
    title: { type: String },
});

const page = usePage();

const displayTitle = computed(() => {
    const metaTitle = page.props.meta?.title;

    return props.title && metaTitle
        ? `${props.title} - ${metaTitle}`
        : metaTitle || props.title;
});

const meta = computed(() => page.props.meta?.meta || []);
const description = computed(
    () => meta.value.find((item) => item.name === "description")?.content,
);
const ogDescription = computed(
    () =>
        meta.value.find((item) => item.property === "og:description")?.content,
);
const ogUrl = computed(
    () => meta.value.find((item) => item.property === "og:url")?.content,
);
const ogImage = computed(
    () => meta.value.find((item) => item.property === "og:image")?.content,
);
const twitterImage = computed(
    () => meta.value.find((item) => item.name === "twitter:image")?.content,
);
const twitterDescription = computed(
    () =>
        meta.value.find((item) => item.name === "twitter:description")?.content,
);

onMounted(() => {
    const descriptionEl = document.querySelector('meta[name="description"]');
    descriptionEl
        ? descriptionEl.setAttribute("content", description.value)
        : null;

    const ogImageEl = document.querySelector('meta[property="og:image"]');
    ogImageEl ? ogImageEl.setAttribute("content", ogImage.value) : null;

    const ogDescriptionEl = document.querySelector(
        'meta[property="og:description"]',
    );
    ogDescriptionEl
        ? ogDescriptionEl.setAttribute("content", ogDescription.value)
        : null;

    const ogUrlEl = document.querySelector('meta[property="og:url"]');
    ogUrlEl ? ogUrlEl.setAttribute("content", ogUrl.value) : null;

    const twitterImageEl = document.querySelector('meta[name="twitter:image"]');
    twitterImageEl
        ? twitterImageEl.setAttribute("content", twitterImage.value)
        : null;

    const twitterDescriptionEl = document.querySelector(
        'meta[name="twitter:description"]',
    );
    twitterDescriptionEl
        ? twitterDescriptionEl.setAttribute("content", twitterDescription.value)
        : null;
});
</script>
