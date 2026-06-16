<template>
    <div>
        <button
            v-if="as == 'button'"
            type="submit"
            class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 transition hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:text-amber-300 dark:hover:bg-amber-900/40 dark:hover:text-amber-100"
        >
            <slot></slot>
        </button>

        <a
            v-else-if="as == 'a'"
            :href="href"
            class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:text-amber-300 dark:hover:bg-amber-900/40 dark:hover:text-amber-100"
        >
            <slot></slot>
        </a>

        <Link
            v-else
            :href="href"
            class="block px-4 py-2 text-sm leading-5 text-gray-700 transition hover:bg-gray-100 focus:bg-gray-100 focus:outline-none dark:text-amber-300 dark:hover:bg-amber-900/40 dark:hover:text-amber-100"
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
