<template>
    <div class="w-1/2 bg-white px-4 dark:bg-gray-800">
        <label for="search" class="hidden">Search</label>
        <input
            id="search"
            ref="searchRef"
            v-model="search"
            class="h-10 w-full cursor-pointer rounded-full border border-gray-500 bg-gray-100 px-4 pb-0 pt-px text-gray-700 outline-none transition focus:border-purple-400"
            :class="{ 'transition-border': search }"
            autocomplete="off"
            name="search"
            placeholder="Search"
            type="search"
            @keyup.esc="search = null"
        />
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import { debounce } from "lodash";

const props = defineProps({
    routeName: String,
});

const search = ref(usePage().props?.search || null);
const sort = ref(usePage().props?.sort || null);
let searchRef = ref(null);

watch(search, () => {
    if (search.value) {
        searchMethod();
    } else {
        router.get(route(props.routeName));
    }
});

const searchMethod = debounce(() => {
    router.get(
        route(props.routeName),
        { search: search.value, sort: sort.value },
        { preserveState: true }
    );
}, 2000);
</script>
