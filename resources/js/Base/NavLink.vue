<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/vue3";

export default defineComponent({
    components: {
        Link,
    },
    props: {
        active: Boolean,
        href: String,
    },

    computed: {
        classes() {
            return this.active
                ? "inline-flex items-center px-1 pt-1 border-b-4 border-indigo-500 dark:border-gray-200 text-md font-semibold leading-5 text-gray-900 dark:text-indigo-300 focus:outline-none focus:border-indigo-700 transition"
                : "inline-flex items-center px-1 pt-1 border-b-4 border-transparent text-md font-semibold leading-5 text-gray-500 dark:text-gray-200 hover:text-gray-700 hover:border-gray-300 dark:hover:border-indigo-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition";
        },
    },
});
</script>
