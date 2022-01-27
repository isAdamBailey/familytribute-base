<template>
    <button
        type="button"
        :class="
            noHover
                ? ''
                : 'transition hover:bg-indigo-500 hover:text-white active:bg-indigo-700 dark:hover:bg-indigo-700'
        "
        class="inline-flex items-center rounded-xl border border-solid border-indigo-500 bg-white px-4 py-0.5 text-center text-sm text-indigo-500 dark:border-indigo-700 dark:bg-indigo-500 dark:text-white"
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
