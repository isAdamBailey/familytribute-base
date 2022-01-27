<template>
    <button
        :aria-label="title"
        :title="title"
        class="border-r border-b py-1 px-2"
        :class="{ 'bg-purple-600 font-bold text-white': isActive }"
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
        title: {
            type: String,
            default: "editor style toggle",
        },
    },
});
</script>
