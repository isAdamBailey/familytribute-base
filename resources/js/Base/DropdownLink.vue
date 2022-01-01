<template>
    <div>
        <button
            v-if="as == 'button'"
            type="submit"
            class="block w-full px-4 py-2 text-sm leading-5 text-gray-700 dark:text-indigo-300 text-left hover:bg-gray-100 dark:hover:bg-indigo-300 dark:hover:text-gray-900 focus:outline-none focus:bg-gray-100 transition"
        >
            <slot></slot>
        </button>

        <a
            v-else-if="as == 'a'"
            :href="href"
            class="block px-4 py-2 text-sm leading-5 text-gray-700 dark:text-indigo-300 hover:bg-gray-100 dark:hover:bg-indigo-300 dark:hover:text-gray-900 focus:outline-none focus:bg-gray-100 transition"
        >
            <slot></slot>
        </a>

        <Link
            v-else
            :href="href"
            class="block px-4 py-2 text-sm leading-5 text-gray-700 dark:text-indigo-300 hover:bg-gray-100 dark:hover:bg-indigo-300 dark:hover:text-gray-900 focus:outline-none focus:bg-gray-100 transition"
        >
            <slot></slot>
        </Link>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";

export default defineComponent({
    components: {
        Link,
    },
    props: {
        href: String,
        as: String,
    },
});
</script>
