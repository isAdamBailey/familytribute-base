<template>
    <Link
        v-for="(story, index) in items"
        :key="index"
        :href="route('stories.show', story)"
        class="bg-gradient-to-bl from-yellow-200 via-transparent to-blueGray-800 shadow-xl rounded-lg hover:shadow-2xl transition"
    >
        <div
            :class="fixedWidth ? 'w-60 md:w-96' : ''"
            class="flex justify-between rounded-t-lg"
        >
            <h2 class="p-3 font-bold text-xl truncate">
                {{ story.title }}
            </h2>
            <pill v-if="story.category" class="m-3" no-hover>{{
                story.category
            }}</pill>
        </div>
        <div class="rounded-b-lg">
            <div class="p-3 prose max-w-none" v-html="story.excerpt" />
        </div>
    </Link>
</template>

<script>
import { defineComponent } from "vue";
import Pill from "@/Base/Pill";

export default defineComponent({
    components: { Pill },
    props: {
        items: Array,
        fixedWidth: {
            type: Boolean,
            default: false,
        },
    },
});
</script>
