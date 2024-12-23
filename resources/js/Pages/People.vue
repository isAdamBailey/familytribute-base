<template>
    <AppLayout>
        <template #header> People </template>

        <div class="mb-5 flex items-center justify-between md:mt-5 md:mb-0">
            <div
                class="font-header text-3xl text-gray-800 dark:text-indigo-400 md:text-5xl"
            >
                People
            </div>

            <SearchInput route-name="people.index" />

            <SortDropdown>
                <JetDropdownLink
                    :href="
                        route('people.index', {
                            sort: 'last_name',
                            order: 'desc',
                            search: search,
                        })
                    "
                >
                    <i class="ri-empathize-fill"></i>
                    <i class="ri-arrow-down-line"></i>
                    Last Name DESC
                </JetDropdownLink>
                <JetDropdownLink
                    :href="
                        route('people.index', {
                            sort: 'last_name',
                            order: 'asc',
                            search: search,
                        })
                    "
                >
                    <i class="ri-empathize-fill"></i>
                    <i class="ri-arrow-up-line"></i>
                    Last Name ASC
                </JetDropdownLink>
                <JetDropdownLink
                    :href="
                        route('people.index', {
                            sort: 'death_date',
                            order: 'desc',
                            search: search,
                        })
                    "
                >
                    <i class="ri-calendar-fill"></i>
                    <i class="ri-arrow-down-line"></i>
                    Death Date DESC
                </JetDropdownLink>
                <JetDropdownLink
                    :href="
                        route('people.index', {
                            sort: 'death_date',
                            order: 'asc',
                            search: search,
                        })
                    "
                >
                    <i class="ri-calendar-fill"></i>
                    <i class="ri-arrow-up-line"></i>
                    Death Date ASC
                </JetDropdownLink>
                <JetDropdownLink :href="route('people.index')">
                    <i class="ri-filter-off-fill"></i> Clear
                </JetDropdownLink>
            </SortDropdown>
        </div>

        <SectionBorder />

        <div v-if="people.data.length">
            <PeopleGrid :items="people" />
        </div>
        <div v-else class="text-gray-500 dark:text-indigo-300">
            No people were found.
        </div>
    </AppLayout>
    <ScrollTop />
</template>

<script setup>
import JetDropdownLink from "@/Base/DropdownLink.vue";
import SearchInput from "@/Base/SearchInput.vue";
import SectionBorder from "@/Base/SectionBorder.vue";
import SortDropdown from "@/Base/SortDropdown.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage } from "@inertiajs/vue3";
import PeopleGrid from "../Components/PeopleGrid.vue";

defineProps({
    people: { type: Object, required: true },
});

const search = usePage().props.search;
</script>
