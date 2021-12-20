<template>
    <div
        class="grid grid-cols-[repeat(auto-fit,minmax(12rem,1fr))] gap-3 max-w-7xl mx-auto md:p-4"
    >
        <pictures-container :items="items" />
    </div>
</template>

<script>
import { defineComponent } from "vue";
import PicturesContainer from "@/Components/PicturesContainer";

export default defineComponent({
    components: { PicturesContainer },
    props: {
        items: Array,
    },
});
</script>
