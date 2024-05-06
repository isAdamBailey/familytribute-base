<template>
    <div
        class="mx-auto grid max-w-7xl grid-cols-[repeat(auto-fit,minmax(12rem,1fr))] gap-2 md:p-4"
    >
        <pictures-container :items="items" />
    </div>
</template>

<script>
import { defineComponent } from "vue";
import PicturesContainer from "@/Components/PicturesContainer.vue";

export default defineComponent({
    components: { PicturesContainer },
    props: {
        items: Array,
    },
});
</script>
