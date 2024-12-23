<template>
    <div
        class="mx-auto grid max-w-7xl grid-cols-[repeat(auto-fit,minmax(18rem,1fr))] gap-2 md:p-4"
    >
        <PicturesContainer :items="reactiveItems.data" />
    </div>
    <div ref="infiniteScroll"></div>
</template>

<script setup>
import PicturesContainer from "@/Components/PicturesContainer.vue";
import useInfiniteScroll from "@/composables/useInfiniteScroll";

const props = defineProps({
    items: {
        type: Object,
        required: true,
    },
});

const { reactiveItems, infiniteScroll } = useInfiniteScroll(
    props.items,
    "pictures",
);
</script>
