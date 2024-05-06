<template>
    <div
        class="mx-auto grid grid-cols-1 gap-4 md:grid-cols-[repeat(auto-fit,minmax(22rem,1fr))] md:p-4"
    >
        <stories-container :items="items" />
    </div>
</template>

<script>
import { defineComponent } from "vue";
import StoriesContainer from "@/Components/StoriesContainer.vue";

export default defineComponent({
    components: { StoriesContainer },
    props: {
        items: Array,
    },
});
</script>
