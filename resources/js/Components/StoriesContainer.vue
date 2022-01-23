<template>
    <Link
        v-for="(story, index) in items"
        :key="index"
        :href="route('stories.show', story)"
        :class="fixedWidth ? 'w-96' : ''"
        class="relative p-7 rounded-lg bg-white dark:card-text-gradient shadow shadow-indigo-200/50 hover:shadow-xl hover:shadow-indigo-300/50 hover:opacity-80 transition"
    >
        <embedded-icon :item="story" color="text-gray-900" />

        <div :class="fixedWidth ? 'w-96' : ''" class="flex justify-between">
            <h2
                :class="fixedWidth ? 'w-80' : ''"
                class="font-bold text-xl truncate"
            >
                {{ story.title }}
            </h2>
            <pill v-if="story.year" class="" no-hover>{{ story.year }}</pill>
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
import EmbeddedIcon from "@/Base/EmbeddedIcon";

export default defineComponent({
    components: { EmbeddedIcon, Pill },
    props: {
        items: Array,
        fixedWidth: {
            type: Boolean,
            default: false,
        },
    },
});
</script>
