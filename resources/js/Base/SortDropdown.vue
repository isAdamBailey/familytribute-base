<template>
    <JetDropdown width="48" :arrow-trigger-title="dropdownText">
        <template #content>
            <div class="block px-4 py-2 text-xs text-gray-400">Sort By:</div>
            <slot></slot>
        </template>
    </JetDropdown>
</template>

<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

import JetDropdown from "@/Base/Dropdown.vue";
const page = usePage();

const dropdownText = computed(() => {
    let text = "Sort By:";
    if (page.props.sort) {
        text = `Sorting by ${page.props.sort}`;
    }
    if (page.props.order) {
        text = `${text} ${page.props.order}`;
    }
    return text;
});
</script>
