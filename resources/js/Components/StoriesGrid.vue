<template>
    <div
        class="grid grid-cols-[repeat(auto-fit,minmax(22rem,1fr))] gap-4 mx-auto md:p-4"
    >
        <Link
            v-for="(story, index) in items"
            :key="index"
            :href="route('stories.show', story)"
            class="bg-gradient-to-bl from-yellow-500 via-transparent to-blueGray-800 shadow-xl rounded-lg hover:shadow-2xl transition"
        >
            <div class="flex justify-between w-full rounded-t-lg">
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
    </div>
</template>

<script>
import { defineComponent } from "vue";
import Pill from "@/Base/Pill";

export default defineComponent({
    components: { Pill },
    props: {
        items: Array,
    },
});
</script>
