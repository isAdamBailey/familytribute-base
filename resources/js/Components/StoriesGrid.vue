<template>
    <div
        class="grid grid-cols-[repeat(auto-fit,minmax(22rem,1fr))] gap-4 mx-auto md:p-4"
    >
        <stories-container :items="items" />
    </div>
</template>

<script>
import { defineComponent } from "vue";
import StoriesContainer from "@/Components/StoriesContainer";

export default defineComponent({
    components: { StoriesContainer },
    props: {
        items: Array,
    },
});
</script>
