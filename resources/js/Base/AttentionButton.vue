<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-500 px-10 py-4 font-semibold uppercase tracking-widest text-white transition hover:bg-indigo-700 hover:bg-gray-800 hover:shadow-xl hover:shadow-indigo-300/50 focus:border-gray-900 focus:outline-none focus:ring focus:ring-gray-300 active:bg-gray-900 disabled:opacity-25 dark:hover:bg-indigo-800 dark:focus:border-indigo-800 dark:focus:ring-indigo-500"
    >
        <slot></slot>
    </button>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        type: {
            type: String,
            default: "button",
        },
    },
});
</script>
