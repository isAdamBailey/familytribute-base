<template>
    <button
        type="button"
        :class="
            noHover
                ? ''
                : 'transition hover:bg-hearthlight hover:text-white active:bg-hearthlight-deep dark:hover:bg-hearthlight-deep'
        "
        class="inline-flex items-center rounded-xl border border-solid border-hearthlight bg-white px-4 py-0.5 text-center text-sm text-hearthlight dark:border-amber-600 dark:bg-hearthlight dark:text-white"
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
