<template>
    <app-layout
        title="Pictures"
        :description="`The pictures of ${$inertia.page.props.siteName}`"
    >
        <template #header> Pictures </template>

        <div class="md:mt-5 mb-5 md:mb-0 flex justify-between items-center">
            <div class="text-2xl">Pictures</div>

            <search-input route-name="pictures.index" />

            <jet-dropdown width="48" arrow-trigger-title="Sort Pictures">
                <template #content>
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        Sort By:
                    </div>
                    <jet-dropdown-link
                        :href="
                            route('pictures.index', {
                                sort: 'year',
                                order: 'desc',
                            })
                        "
                    >
                        Year Descending
                    </jet-dropdown-link>
                    <jet-dropdown-link
                        :href="
                            route('pictures.index', {
                                sort: 'year',
                                order: 'asc',
                            })
                        "
                    >
                        Year Ascending
                    </jet-dropdown-link>
                    <jet-dropdown-link
                        :href="
                              route('pictures.index', {
                                  sort: 'featured',
                                  order: 'desc',
                              })
                          "
                    >
                      Featured
                    </jet-dropdown-link>
                </template>
            </jet-dropdown>
        </div>

        <section-border />

        <div v-if="pictures.data.length">
            <infinite-scroll :items="pictures" component="pictures-grid" />
        </div>
        <div v-else class="text-gray-500">No pictures were found.</div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import InfiniteScroll from "@/Components/InfiniteScroll.vue";
import JetDropdown from "@/Base/Dropdown.vue";
import JetDropdownLink from "@/Base/DropdownLink.vue";
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
        pictures: Object,
    },
});
</script>
