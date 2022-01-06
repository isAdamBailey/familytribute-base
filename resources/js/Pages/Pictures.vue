<template>
    <app-layout>
        <template #header> Pictures </template>

        <div class="md:mt-5 mb-5 md:mb-0 flex justify-between items-center">
            <div
                class="font-header text-gray-800 dark:text-indigo-400 text-3xl md:text-5xl"
            >
                Pictures
            </div>

            <search-input route-name="pictures.index" />

            <sort-dropdown>
                <jet-dropdown-link
                    :href="
                        route('pictures.index', {
                            sort: 'year',
                            order: 'desc',
                        })
                    "
                >
                    <i class="ri-calendar-fill"></i>
                    <i class="ri-arrow-down-line"></i>
                    Year DESC
                </jet-dropdown-link>
                <jet-dropdown-link
                    :href="
                        route('pictures.index', {
                            sort: 'year',
                            order: 'asc',
                        })
                    "
                >
                    <i class="ri-calendar-fill"></i>
                    <i class="ri-arrow-up-line"></i>
                    Year ASC
                </jet-dropdown-link>
                <jet-dropdown-link
                    v-if="$inertia.page.props.user"
                    :href="
                        route('pictures.index', {
                            sort: 'featured',
                            order: 'desc',
                        })
                    "
                >
                    <i class="ri-star-fill"></i> Featured
                </jet-dropdown-link>
                <jet-dropdown-link
                    v-if="$inertia.page.props.user"
                    :href="
                        route('pictures.index', {
                            sort: 'private',
                            order: 'desc',
                        })
                    "
                >
                    <i class="ri-git-repository-private-fill"></i> Private
                </jet-dropdown-link>
                <jet-dropdown-link :href="route('pictures.index')">
                    <i class="ri-filter-off-fill"></i> Clear
                </jet-dropdown-link>
            </sort-dropdown>
        </div>

        <section-border />

        <div v-if="pictures.data.length">
            <infinite-scroll :items="pictures" component="pictures-grid" />
        </div>
        <div v-else class="text-gray-500 dark:text-indigo-300">
            No pictures were found.
        </div>
    </app-layout>
    <scroll-top />
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import InfiniteScroll from "@/Components/InfiniteScroll.vue";
import JetDropdownLink from "@/Base/DropdownLink.vue";
import SearchInput from "@/Base/SearchInput";
import SectionBorder from "@/Base/SectionBorder";
import SortDropdown from "@/Base/SortDropdown";

export default defineComponent({
    components: {
        SortDropdown,
        SectionBorder,
        SearchInput,
        AppLayout,
        InfiniteScroll,
        JetDropdownLink,
    },

    props: {
        pictures: Object,
    },
});
</script>
