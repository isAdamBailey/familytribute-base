~<template>
    <Link
        v-for="(picture, index) in items"
        replace
        :key="index"
        :href="route('pictures.show', picture)"
        :class="fixedWidth ? 'w-60' : ''"
        class="min-w-50 relative snap-start rounded-lg bg-white shadow-indigo-200/50 transition hover:opacity-80 hover:shadow-xl hover:shadow-indigo-300/50"
    >
        <img
            class="max-h-60 w-full rounded-t-lg object-cover"
            :src="picture.url"
            :alt="picture.title"
        />
        <EmbeddedIcon :item="picture" />

        <div class="card-text-gradient truncate rounded-b p-3">
            <div class="flex items-center justify-between">
                <p class="truncate text-sm font-bold">
                    {{ picture.title }}
                </p>
                <Pill v-if="picture.year" class="" no-hover>{{
                    picture.year
                }}</Pill>
            </div>
            <p
                class="prose max-w-none text-sm line-clamp-1"
                v-html="picture.description"
            ></p>
        </div>
    </Link>
</template>

<script setup>
import EmbeddedIcon from "@/Base/EmbeddedIcon.vue";
import Pill from "@/Base/Pill.vue";

defineProps({
    items: {
        type: Array,
        required: true,
    },
    fixedWidth: {
        type: Boolean,
        default: false,
    },
});
</script>
