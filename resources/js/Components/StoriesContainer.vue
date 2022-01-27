<template>
    <Link
        v-for="(story, index) in items"
        :key="index"
        :href="route('stories.show', story)"
        :class="fixedWidth ? 'w-96' : ''"
        class="dark:card-text-gradient relative snap-center rounded-lg bg-white p-7 shadow shadow-indigo-200/50 transition hover:opacity-80 hover:shadow-xl hover:shadow-indigo-300/50"
    >
        <embedded-icon :item="story" color="text-gray-900" />

        <div :class="fixedWidth ? 'w-96' : ''" class="flex justify-between">
            <h2
                :class="fixedWidth ? 'w-80' : ''"
                class="truncate text-xl font-bold"
            >
                {{ story.title }}
            </h2>
            <pill v-if="story.year" class="" no-hover>{{ story.year }}</pill>
        </div>
        <div class="rounded-b-lg">
            <div
                class="prose max-w-none text-sm line-clamp-3"
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
