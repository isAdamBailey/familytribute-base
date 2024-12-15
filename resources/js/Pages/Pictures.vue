<template>
    <AppLayout>
        <template #header> Pictures </template>

        <div class="mb-5 flex items-center justify-between md:mt-5 md:mb-0">
            <div
                class="font-header text-3xl text-gray-800 dark:text-indigo-400 md:text-5xl"
            >
                Pictures
            </div>

            <SearchInput route-name="pictures.index" />

            <SortDropdown>
                <JetDropdownLink
                    :href="
                        route('pictures.index', {
                            sort: 'year',
                            order: 'desc',
                            search: search,
                        })
                    "
                >
                    <i class="ri-calendar-fill"></i>
                    <i class="ri-arrow-down-line"></i>
                    Year DESC
                </JetDropdownLink>
                <JetDropdownLink
                    :href="
                        route('pictures.index', {
                            sort: 'year',
                            order: 'asc',
                            search: search,
                        })
                    "
                >
                    <i class="ri-calendar-fill"></i>
                    <i class="ri-arrow-up-line"></i>
                    Year ASC
                </JetDropdownLink>
                <JetDropdownLink
                    v-if="user"
                    :href="
                        route('pictures.index', {
                            sort: 'featured',
                            order: 'desc',
                            search: search,
                        })
                    "
                >
                    <i class="ri-star-fill"></i> Featured
                </JetDropdownLink>
                <JetDropdownLink
                    v-if="user"
                    :href="
                        route('pictures.index', {
                            sort: 'private',
                            order: 'desc',
                            search: search,
                        })
                    "
                >
                    <i class="ri-git-repository-private-fill"></i> Private
                </JetDropdownLink>
                <JetDropdownLink :href="route('pictures.index')">
                    <i class="ri-filter-off-fill"></i> Clear
                </JetDropdownLink>
            </SortDropdown>
        </div>

        <section-border />

        <div v-if="pictures.data.length">
            <PicturesGrid :items="pictures" />
        </div>
        <div v-else class="text-gray-500 dark:text-indigo-300">
            No pictures were found.
        </div>
    </AppLayout>
    <ScrollTop />
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
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

const search = usePage().props.search;
const user = usePage().props.user;
</script>
