<template>
    <div
        v-if="picture.featured && $inertia.page.props.user"
        class="absolute pl-0.5 top-0 inset-x-0 text-white text-sm"
    >
        <i class="ri-star-fill"></i>
    </div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        picture: Object,
    },
});
</script>
