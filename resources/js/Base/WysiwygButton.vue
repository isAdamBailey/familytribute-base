<template>
    <button
        :aria-label="aria"
        class="border-r border-b py-1 px-2"
        :class="{ 'font-bold bg-purple-600 text-white': isActive }"
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
        aria: {
            type: String,
            default: "editor style toggle",
        },
    },
});
</script>
