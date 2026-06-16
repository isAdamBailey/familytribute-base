<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center rounded-md border border-transparent bg-hearthlight px-10 py-4 font-semibold text-white transition hover:bg-hearthlight-deep hover:-translate-y-px hover:shadow-xl hover:shadow-amber-300/40 focus:border-amber-900 focus:outline-none focus:ring focus:ring-amber-200 active:translate-y-0 active:bg-hearthlight-deep disabled:opacity-25 dark:hover:bg-hearthlight-deep dark:focus:border-hearthlight dark:focus:ring-hearthlight/30"
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
