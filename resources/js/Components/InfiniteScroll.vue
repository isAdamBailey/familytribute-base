<template>
    <component :is="component" :items="itemsData" />

    <div v-if="items.next_page_url" class="flex justify-center mt-7">
        <attention-button @click="loadMore"
            >Load More {{ typeString }}</attention-button
        >
    </div>
</template>

<script>
import { defineComponent } from "vue";
import PicturesGrid from "@/Components/PicturesGrid";
import StoriesGrid from "@/Components/StoriesGrid";
import PeopleGrid from "@/Components/PeopleGrid";
import AttentionButton from "../Base/AttentionButton";

export default defineComponent({
    components: {
        AttentionButton,
        PicturesGrid,
        StoriesGrid,
        PeopleGrid,
    },
    props: {
        items: Object,
        component: String,
    },
    data() {
        return {
            itemsData: this.items.data,
            loadingMore: false,
        };
    },
    computed: {
        typeString() {
            return this.component.split("-")[0];
        },
    },
    watch: {
        items() {
            if (this.loadingMore) {
                this.itemsData.push(...this.items.data);
                this.loadingMore = false;
            }
        },
    },
    methods: {
        loadMore() {
            this.loadingMore = true;

            const links = this.items.links;
            const next = links[links.length - 1];
            if (next.url) {
                this.$inertia.get(
                    next.url,
                    {
                        search: this.$inertia.page.props.search,
                        sort: this.$inertia.page.props.sort,
                        order: this.$inertia.page.props.order,
                    },
                    {
                        preserveScroll: true,
                        preserveState: true,
                    }
                );
            }
        },
    },
});
</script>
