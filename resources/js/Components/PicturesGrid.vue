<template>
    <div
        class="grid grid-cols-[repeat(auto-fit,minmax(12rem,1fr))] gap-4 max-w-7xl mx-auto md:p-4"
    >
        <picture-container :items="items" />
    </div>
</template>

<script>
import { defineComponent } from "vue";
import PictureContainer from "./PictureContainer";

export default defineComponent({
    components: { PictureContainer },
    props: {
        items: Array,
    },
});
</script>
