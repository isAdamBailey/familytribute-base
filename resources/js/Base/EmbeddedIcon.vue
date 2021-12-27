<template>
    <div
        v-if="$inertia.page.props.user"
        class="absolute pl-0.5 top-0 inset-x-0 text-white text-sm"
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
    },
});
</script>
