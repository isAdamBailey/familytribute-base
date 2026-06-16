<template>
    <AppLayout>
        <template #header>Pictures</template>

        <!-- Toolbar -->
        <div
            class="mt-5 flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
        >
            <h1
                class="font-header text-3xl text-gray-800 dark:text-amber-400 md:text-5xl"
            >
                Pictures
            </h1>
            <div class="flex items-center gap-2">
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
        </div>

        <!-- Grid -->
        <div class="mt-8">
            <PicturesGrid v-if="pictures.data.length" :items="pictures" />
            <div v-else class="py-20 text-center">
                <div class="text-4xl text-hearthlight/20">✦</div>
                <p class="mt-4 text-base text-gray-500 dark:text-stone-400">
                    {{
                        search
                            ? "No pictures matched your search."
                            : "No pictures have been added yet."
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
import JetDropdownLink from "@/Base/DropdownLink.vue";
import SearchInput from "@/Base/SearchInput.vue";
import SortDropdown from "@/Base/SortDropdown.vue";
import PicturesGrid from "@/Components/PicturesGrid.vue";

defineProps({
    pictures: { type: Object, required: true },
});

const { search, user } = usePage().props;
</script>
