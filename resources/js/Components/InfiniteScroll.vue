<template>
    <component :is="component" :items="itemsData" />

    <div v-if="items.next_page_url" class="flex justify-center mt-7">
        <base-button @click="loadMore">Load more {{ typeString }}</base-button>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import PicturesGrid from "@/Components/PicturesGrid";
import StoriesGrid from "@/Components/StoriesGrid";
import PeopleGrid from "@/Components/PeopleGrid";

export default defineComponent({
    components: {
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
        };
    },
    computed: {
        typeString() {
            return this.component.split("-")[0];
        },
    },
    watch: {
        items() {
            // if this is a search, then replace all items
            if (this.$inertia.page.props.search) {
                this.itemsData = this.items.data;
            } else {
                this.itemsData.push(...this.items.data);
            }
        },
    },
    methods: {
        loadMore() {
            const links = this.items.links;
            const next = links[links.length - 1];
            if (next.url) {
                this.$inertia.get(
                    next.url,
                    {},
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
