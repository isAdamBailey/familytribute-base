<template>
    <div>
        <button
            v-if="as === 'button'"
            :class="classes"
            class="w-full text-left"
        >
            <slot></slot>
        </button>

        <Link v-else :href="href" :class="classes">
            <slot />
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
        active: Boolean,
        href: String,
        as: String,
    },
    computed: {
        classes() {
            return this.active
                ? "block pl-3 pr-4 py-2 border-l-4 border-indigo-400 text-base font-semibold text-indigo-700 dark:text-gray-900 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition"
                : "block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-semibold text-gray-600 hover:text-gray-800 dark:hover:text-gray-800 dark:text-indigo-300 hover:bg-gray-50 hover:border-gray-300 dark:hover:bg-indigo-200 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition";
        },
    },
});
</script>
