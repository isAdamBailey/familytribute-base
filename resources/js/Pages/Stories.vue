<template>
    <app-layout
        title="Stories"
        :description="`The stories of ${$inertia.page.props.siteName}`"
    >
        <template #header> Stories </template>

        <div class="md:mt-5 mb-5 md:mb-0 flex justify-between items-center">
            <div class="font-header text-3xl md:text-5xl">Stories</div>
            <search-input route-name="stories.index" />
        </div>

        <section-border />

        <div v-if="stories.data.length">
            <infinite-scroll :items="stories" component="stories-grid" />
        </div>

        <div v-else class="text-gray-500">No stories were found.</div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import InfiniteScroll from "@/Components/InfiniteScroll.vue";
import SearchInput from "@/Base/SearchInput";
import SectionBorder from "@/Base/SectionBorder";

export default defineComponent({
    components: {
        SectionBorder,
        SearchInput,
        AppLayout,
        InfiniteScroll,
    },

    props: {
        stories: Object,
    },
});
</script>
