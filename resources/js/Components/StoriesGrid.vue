<template>
    <div
        class="mx-auto grid grid-cols-1 gap-4 md:grid-cols-[repeat(auto-fit,minmax(22rem,1fr))] md:p-4"
    >
        <StoriesContainer :items="reactiveItems.data" />
    </div>
    <div ref="infiniteScroll"></div>
</template>

<script setup>
import StoriesContainer from "@/Components/StoriesContainer.vue";
import useInfiniteScroll from "@/composables/useInfiniteScroll.js";

const props = defineProps({
    items: {
        type: Object,
        required: true,
    },
});

const { reactiveItems, infiniteScroll } = useInfiniteScroll(
    props.items,
    "stories",
);
</script>
