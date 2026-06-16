<template>
    <AppLayout>
        <template #header>People</template>

        <!-- Toolbar -->
        <div
            class="mt-5 flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
        >
            <h1
                class="font-header text-3xl text-gray-800 dark:text-amber-400 md:text-5xl"
            >
                People
            </h1>
            <div class="flex items-center gap-2">
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
        </div>

        <!-- Grid -->
        <div class="mt-8">
            <PeopleGrid v-if="people.data.length" :items="people" />
            <div v-else class="py-20 text-center">
                <div class="text-4xl text-hearthlight/20">✦</div>
                <p class="mt-4 text-base text-gray-500 dark:text-stone-400">
                    {{
                        search
                            ? "No people matched your search."
                            : "No people have been added yet."
                    }}
                </p>
            </div>
        </div>
    </AppLayout>
    <ScrollTop />
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
import JetDropdownLink from "@/Base/DropdownLink.vue";
import SearchInput from "@/Base/SearchInput.vue";
import SortDropdown from "@/Base/SortDropdown.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import PeopleGrid from "@/Components/PeopleGrid.vue";

defineProps({
    people: { type: Object, required: true },
});

const { search } = usePage().props;
</script>
