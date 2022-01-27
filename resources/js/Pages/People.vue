<template>
    <app-layout>
        <template #header> People </template>

        <div class="mb-5 flex items-center justify-between md:mt-5 md:mb-0">
            <div
                class="font-header text-3xl text-gray-800 dark:text-indigo-400 md:text-5xl"
            >
                People
            </div>

            <search-input route-name="people.index" />

            <sort-dropdown>
                <jet-dropdown-link
                    :href="
                        route('people.index', {
                            sort: 'last_name',
                            order: 'desc',
                            search: $page.props.search,
                        })
                    "
                >
                    <i class="ri-empathize-fill"></i>
                    <i class="ri-arrow-down-line"></i>
                    Last Name DESC
                </jet-dropdown-link>
                <jet-dropdown-link
                    :href="
                        route('people.index', {
                            sort: 'last_name',
                            order: 'asc',
                            search: $page.props.search,
                        })
                    "
                >
                    <i class="ri-empathize-fill"></i>
                    <i class="ri-arrow-up-line"></i>
                    Last Name ASC
                </jet-dropdown-link>
                <jet-dropdown-link
                    :href="
                        route('people.index', {
                            sort: 'death_date',
                            order: 'desc',
                            search: $page.props.search,
                        })
                    "
                >
                    <i class="ri-calendar-fill"></i>
                    <i class="ri-arrow-down-line"></i>
                    Death Date DESC
                </jet-dropdown-link>
                <jet-dropdown-link
                    :href="
                        route('people.index', {
                            sort: 'death_date',
                            order: 'asc',
                            search: $page.props.search,
                        })
                    "
                >
                    <i class="ri-calendar-fill"></i>
                    <i class="ri-arrow-up-line"></i>
                    Death Date ASC
                </jet-dropdown-link>
                <jet-dropdown-link :href="route('people.index')">
                    <i class="ri-filter-off-fill"></i> Clear
                </jet-dropdown-link>
            </sort-dropdown>
        </div>

        <section-border />

        <div v-if="people.data.length">
            <infinite-scroll component="people-grid" :items="people" />
        </div>
        <div v-else class="text-gray-500 dark:text-indigo-300">
            No people were found.
        </div>
    </app-layout>
    <scroll-top />
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetDropdownLink from "@/Base/DropdownLink.vue";
import InfiniteScroll from "@/Components/InfiniteScroll";
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
        people: Object,
    },
});
</script>
