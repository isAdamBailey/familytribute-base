<template>
    <app-layout>
        <template #header> Stories </template>

        <div class="mb-5 flex items-center justify-between md:mt-5 md:mb-0">
            <div
                class="font-header text-3xl text-gray-800 dark:text-indigo-400 md:text-5xl"
            >
                Stories
            </div>

            <search-input route-name="stories.index" />

            <sort-dropdown>
                <dropdown-link
                    :href="
                        route('stories.index', {
                            sort: 'year',
                            order: 'desc',
                            search: $page.props.search,
                        })
                    "
                >
                    <i class="ri-calendar-fill"></i>
                    <i class="ri-arrow-down-line"></i>
                    Year Written DESC
                </dropdown-link>
                <dropdown-link
                    :href="
                        route('stories.index', {
                            sort: 'year',
                            order: 'asc',
                            search: $page.props.search,
                        })
                    "
                >
                    <i class="ri-calendar-fill"></i>
                    <i class="ri-arrow-up-line"></i>
                    Year Written ASC
                </dropdown-link>
                <dropdown-link
                    :href="
                        route('stories.index', {
                            sort: 'created_at',
                            order: 'asc',
                            search: $page.props.search,
                        })
                    "
                >
                    <i class="ri-calendar-fill"></i>
                    <i class="ri-arrow-up-line"></i>
                    Created ASC
                </dropdown-link>
                <dropdown-link
                    :href="
                        route('stories.index', {
                            sort: 'created_at',
                            order: 'desc',
                            search: $page.props.search,
                        })
                    "
                >
                    <i class="ri-calendar-fill"></i>
                    <i class="ri-arrow-down-line"></i>
                    Created DESC
                </dropdown-link>
                <dropdown-link
                    v-if="$inertia.page.props.user"
                    :href="
                        route('stories.index', {
                            sort: 'private',
                            order: 'desc',
                        })
                    "
                >
                    <i class="ri-git-repository-private-fill"></i> Private
                </dropdown-link>
                <dropdown-link :href="route('stories.index')">
                    <i class="ri-filter-off-fill"></i> Clear
                </dropdown-link>
            </sort-dropdown>
        </div>

        <section-border />

        <div v-if="stories.data.length">
            <StoriesGrid :items="stories" />
        </div>

        <div v-else class="text-gray-500 dark:text-indigo-300">
            No stories were found.
        </div>
    </app-layout>
    <ScrollTop />
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import SearchInput from "@/Base/SearchInput.vue";
import SectionBorder from "@/Base/SectionBorder.vue";
import SortDropdown from "@/Base/SortDropdown.vue";
import DropdownLink from "@/Base/DropdownLink.vue";
import StoriesGrid from "@/Components/StoriesGrid.vue";

defineProps({
    stories: {
        type: Object,
        required: true,
    },
});
</script>
