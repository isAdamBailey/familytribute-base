<template>
    <AppLayout>
        <template #header> Stories </template>

        <div class="mb-5 flex items-center justify-between md:mt-5 md:mb-0">
            <div
                class="font-header text-3xl text-gray-800 dark:text-indigo-400 md:text-5xl"
            >
                Stories
            </div>

            <SearchInput route-name="stories.index" />

            <SortDropdown>
                <DropdownLink
                    :href="
                        route('stories.index', {
                            sort: 'year',
                            order: 'desc',
                            search: search,
                        })
                    "
                >
                    <i class="ri-calendar-fill"></i>
                    <i class="ri-arrow-down-line"></i>
                    Year Written DESC
                </DropdownLink>
                <DropdownLink
                    :href="
                        route('stories.index', {
                            sort: 'year',
                            order: 'asc',
                            search: search,
                        })
                    "
                >
                    <i class="ri-calendar-fill"></i>
                    <i class="ri-arrow-up-line"></i>
                    Year Written ASC
                </DropdownLink>
                <DropdownLink
                    :href="
                        route('stories.index', {
                            sort: 'created_at',
                            order: 'asc',
                            search: search,
                        })
                    "
                >
                    <i class="ri-calendar-fill"></i>
                    <i class="ri-arrow-up-line"></i>
                    Created ASC
                </DropdownLink>
                <DropdownLink
                    :href="
                        route('stories.index', {
                            sort: 'created_at',
                            order: 'desc',
                            search: search,
                        })
                    "
                >
                    <i class="ri-calendar-fill"></i>
                    <i class="ri-arrow-down-line"></i>
                    Created DESC
                </DropdownLink>
                <DropdownLink
                    v-if="user"
                    :href="
                        route('stories.index', {
                            sort: 'private',
                            order: 'desc',
                        })
                    "
                >
                    <i class="ri-git-repository-private-fill"></i> Private
                </DropdownLink>
                <DropdownLink :href="route('stories.index')">
                    <i class="ri-filter-off-fill"></i> Clear
                </DropdownLink>
            </SortDropdown>
        </div>

        <SectionBorder />

        <div v-if="stories.data.length">
            <StoriesGrid :items="stories" />
        </div>

        <div v-else class="text-gray-500 dark:text-indigo-300">
            No stories were found.
        </div>
    </AppLayout>
    <ScrollTop />
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";

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

const { user, search } = usePage().props;
</script>
