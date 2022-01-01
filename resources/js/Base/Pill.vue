<template>
    <button
        type="button"
        :class="
            noHover
                ? ''
                : 'hover:bg-indigo-500 dark:hover:bg-indigo-700 hover:text-white active:bg-indigo-700 transition'
        "
        class="inline-flex items-center px-4 py-0.5 text-sm text-center text-indigo-500 dark:text-white bg-white dark:bg-indigo-500 border border-solid rounded-xl border-indigo-500 dark:border-indigo-700"
    >
        <slot></slot>
    </button>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        noHover: {
            type: Boolean,
            default: false,
        },
    },
});
</script>
