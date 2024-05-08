<template>
    <app-layout>
        <template #header> Pictures </template>

        <div class="mb-5 flex items-center justify-between md:mt-5 md:mb-0">
            <div
                class="font-header text-3xl text-gray-800 dark:text-indigo-400 md:text-5xl"
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
                            search: $page.props.search,
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
                            search: $page.props.search,
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
                            search: $page.props.search,
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
                            search: $page.props.search,
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
            <PicturesGrid :items="pictures" />
        </div>
        <div v-else class="text-gray-500 dark:text-indigo-300">
            No pictures were found.
        </div>
    </app-layout>
    <scroll-top />
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import JetDropdownLink from "@/Base/DropdownLink.vue";
import SearchInput from "@/Base/SearchInput.vue";
import SectionBorder from "@/Base/SectionBorder.vue";
import SortDropdown from "@/Base/SortDropdown.vue";
import PicturesGrid from "@/Components/PicturesGrid.vue";

defineProps({
    pictures: {
        type: Object,
        required: true,
    },
});
</script>
