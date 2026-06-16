<template>
    <button
        :aria-label="title"
        :title="title"
        :disabled="disabled"
        class="border-b border-r border-gray-200 px-2 py-1 text-gray-600 transition-colors hover:bg-amber-50 hover:text-hearthlight-deep focus:outline-none focus:ring-1 focus:ring-inset focus:ring-hearthlight/40 disabled:cursor-not-allowed disabled:opacity-40 dark:border-stone-600 dark:text-gray-300 dark:hover:bg-amber-950/30 dark:hover:text-amber-300"
        :class="{ 'bg-hearthlight font-bold text-white hover:bg-hearthlight-deep hover:text-white dark:bg-hearthlight dark:text-white': isActive }"
    >
        <i :class="icon"></i>
    </button>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        icon: String,
        isActive: Boolean,
        disabled: Boolean,
        title: {
            type: String,
            default: "editor style toggle",
        },
    },
});
</script>
