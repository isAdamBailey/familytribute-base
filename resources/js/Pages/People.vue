<template>
    <app-layout
        title="People"
        :description="`The people of ${$inertia.page.props.siteName}`"
    >
        <template #header> People </template>

        <div class="md:mt-5 mb-5 md:mb-0 flex justify-between items-center">
            <div class="text-2xl">People</div>

            <search-input route-name="people.index" />

            <jet-dropdown width="48" arrow-trigger-title="Sort People">
                <template #content>
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        Sort By:
                    </div>
                    <jet-dropdown-link
                        :href="
                            route('people.index', {
                                sort: 'last_name',
                                order: 'desc',
                            })
                        "
                    >
                        Last Name Descending
                    </jet-dropdown-link>
                    <jet-dropdown-link
                        :href="
                            route('people.index', {
                                sort: 'last_name',
                                order: 'asc',
                            })
                        "
                    >
                        Last Name Ascending
                    </jet-dropdown-link>
                    <jet-dropdown-link
                        :href="
                            route('people.index', {
                                sort: 'death_date',
                                order: 'desc',
                            })
                        "
                    >
                        Death Date Descending
                    </jet-dropdown-link>
                    <jet-dropdown-link
                        :href="
                            route('people.index', {
                                sort: 'death_date',
                                order: 'asc',
                            })
                        "
                    >
                        Death Date Ascending
                    </jet-dropdown-link>
                </template>
            </jet-dropdown>
        </div>

        <section-border />

        <div v-if="people.data.length">
            <infinite-scroll component="people-grid" :items="people" />
        </div>
        <div v-else class="text-gray-500">No people were found.</div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetDropdown from "@/Base/Dropdown.vue";
import JetDropdownLink from "@/Base/DropdownLink.vue";
import InfiniteScroll from "@/Components/InfiniteScroll";
import SearchInput from "@/Base/SearchInput";
import SectionBorder from "@/Base/SectionBorder";

export default defineComponent({
    components: {
        SectionBorder,
        SearchInput,
        AppLayout,
        InfiniteScroll,
        JetDropdown,
        JetDropdownLink,
    },

    props: {
        people: Object,
    },
});
</script>
