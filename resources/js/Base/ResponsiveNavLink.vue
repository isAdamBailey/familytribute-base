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
                ? "block mx-2 px-3 py-2 rounded-md text-base font-semibold text-hearthlight-deep dark:text-amber-900 bg-amber-50 focus:outline-none transition"
                : "block mx-2 px-3 py-2 rounded-md text-base font-semibold text-gray-600 dark:text-amber-300 hover:text-gray-800 hover:bg-gray-50 dark:hover:bg-amber-900/30 dark:hover:text-amber-100 focus:outline-none transition";
        },
    },
});
</script>
