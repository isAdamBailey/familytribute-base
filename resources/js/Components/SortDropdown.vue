<template>
    <jet-dropdown width="48" :arrow-trigger-title="dropdownText">
        <template #content>
            <div class="block px-4 py-2 text-xs text-gray-400">Sort By:</div>
            <slot></slot>
        </template>
    </jet-dropdown>
</template>

<script>
import { defineComponent } from "vue";
import JetDropdown from "@/Base/Dropdown.vue";

export default defineComponent({
    components: { JetDropdown },

    computed: {
        dropdownText() {
            let text = "Sort By:";
            if (this.$inertia.page.props.sort) {
                text = `Sorting by ${this.$inertia.page.props.sort}`;
            }
            if (this.$inertia.page.props.order) {
                text = `${text} ${this.$inertia.page.props.order}`;
            }
            return text;
        },
    },
});
</script>
