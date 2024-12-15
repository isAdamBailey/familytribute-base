<template>
    <div
        v-if="user"
        :class="color"
        class="absolute inset-x-0 top-0 pl-0.5 text-sm"
    >
        <i v-if="item.featured" class="ri-star-fill"></i>
        <i v-if="item.private" class="ri-git-repository-private-fill"></i>
    </div>
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";

defineProps({
    item: Object,
    color: {
        type: String,
        default: "text-white",
    },
});

const user = usePage().props.user;
</script>
