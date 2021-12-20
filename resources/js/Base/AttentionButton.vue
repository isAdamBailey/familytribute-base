<template>
    <button
        :type="type"
        class="bg-indigo-500 inline-flex justify-center items-center px-10 py-4 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-indigo-700 hover:shadow-xl hover:shadow-indigo-300/50 hover:bg-gray-800 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition"
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
