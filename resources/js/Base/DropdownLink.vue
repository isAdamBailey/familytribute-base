<template>
    <div>
        <button
            v-if="as == 'button'"
            type="submit"
            class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 transition hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:text-indigo-300 dark:hover:bg-indigo-300 dark:hover:text-gray-900"
        >
            <slot></slot>
        </button>

        <a
            v-else-if="as == 'a'"
            :href="href"
            class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:text-indigo-300 dark:hover:bg-indigo-300 dark:hover:text-gray-900"
        >
            <slot></slot>
        </a>

        <Link
            v-else
            :href="href"
            class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:text-indigo-300 dark:hover:bg-indigo-300 dark:hover:text-gray-900"
        >
            <slot></slot>
        </Link>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/vue3";

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
