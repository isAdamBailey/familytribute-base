<template>
    <Link
        v-for="(story, index) in items"
        :key="index"
        :href="route('stories.show', story)"
        :class="fixedWidth ? 'w-60' : ''"
        class="py-10 px-7 bg-gradient-to-bl from-yellow-200 via-transparent to-gray-100 rounded-lg shadow-indigo-200/50 hover:shadow-xl hover:shadow-indigo-300/50 hover:opacity-80 transition"
    >
        <div :class="fixedWidth ? 'w-60' : ''" class="flex rounded-t-lg">
            <h2 class="font-bold text-xl truncate">
                {{ story.title }}
            </h2>
            <pill v-if="story.category" class="m-3" no-hover>{{
                story.category
            }}</pill>
        </div>
        <div class="rounded-b-lg">
            <div
                class="text-sm prose max-w-none line-clamp-3"
                v-html="story.excerpt"
            />
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
