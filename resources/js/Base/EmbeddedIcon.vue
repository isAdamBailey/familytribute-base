<template>
    <div
        v-if="$inertia.page.props.user"
        :class="color"
        class="absolute inset-x-0 top-0 pl-0.5 text-sm"
    >
        <i v-if="item.featured" class="ri-star-fill"></i>
        <i v-if="item.private" class="ri-git-repository-private-fill"></i>
    </div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        item: Object,
        color: {
            type: String,
            default: "text-white",
        },
    },
});
</script>
