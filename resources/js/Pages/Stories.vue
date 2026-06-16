<template>
    <AppLayout>
        <template #header>Stories</template>

        <!-- Toolbar -->
        <div
            class="mt-5 flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
        >
            <h1
                class="font-header text-3xl text-gray-800 dark:text-amber-400 md:text-5xl"
            >
                Stories
            </h1>
            <div class="flex items-center gap-2">
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
        </div>

        <!-- Grid -->
        <div class="mt-8">
            <StoriesGrid v-if="stories.data.length" :items="stories" />
            <div v-else class="py-20 text-center">
                <div class="text-4xl text-hearthlight/20">✦</div>
                <p class="mt-4 text-base text-gray-500 dark:text-stone-400">
                    {{
                        search
                            ? "No stories matched your search."
                            : "No stories have been added yet."
                    }}
                </p>
            </div>
        </div>
    </AppLayout>
    <ScrollTop />
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import DropdownLink from "@/Base/DropdownLink.vue";
import SearchInput from "@/Base/SearchInput.vue";
import SortDropdown from "@/Base/SortDropdown.vue";
import StoriesGrid from "@/Components/StoriesGrid.vue";

defineProps({
    stories: { type: Object, required: true },
});

const { user, search } = usePage().props;
</script>
