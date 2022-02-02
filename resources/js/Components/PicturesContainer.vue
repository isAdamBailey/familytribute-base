<template>
    <Link
        v-for="(picture, index) in items"
        :key="index"
        :href="route('pictures.show', picture)"
        :class="fixedWidth ? 'w-60' : ''"
        class="min-w-20 relative snap-start rounded-lg bg-white shadow-indigo-200/50 transition hover:opacity-80 hover:shadow-xl hover:shadow-indigo-300/50"
    >
        <img
            class="h-48 w-full rounded-t-lg object-cover"
            :src="picture.url"
            :alt="picture.title"
        />
        <embedded-icon :item="picture" />

        <div class="card-text-gradient truncate rounded-b p-3">
            <div class="flex items-center justify-between">
                <p class="truncate text-sm font-bold">
                    {{ picture.title }}
                </p>
                <pill v-if="picture.year" class="" no-hover>{{
                    picture.year
                }}</pill>
            </div>
            <p
                class="prose max-w-none text-sm line-clamp-1"
                v-html="picture.description"
            ></p>
        </div>
    </Link>
</template>

<script>
import { defineComponent } from "vue";
import EmbeddedIcon from "@/Base/EmbeddedIcon";
import Pill from "../Base/Pill";

export default defineComponent({
    components: { Pill, EmbeddedIcon },
    props: {
        items: Array,
        fixedWidth: {
            type: Boolean,
            default: false,
        },
    },
});
</script>
