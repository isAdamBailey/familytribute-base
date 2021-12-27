<template>
    <Link
        v-for="(picture, index) in items"
        :key="index"
        :href="route('pictures.show', picture)"
        :class="fixedWidth ? 'w-60' : ''"
        class="min-w-20 relative snap-center bg-white rounded-lg shadow-indigo-200/50 hover:shadow-xl hover:shadow-indigo-300/50 hover:opacity-80 transition"
    >
        <img
            class="w-full h-48 object-cover rounded-t-lg"
            :src="picture.url"
            :alt="picture.title"
        />
        <embedded-icon :item="picture" />

        <div
            :class="fixedWidth ? 'w-60' : ''"
            class="card-text-gradient p-3 rounded-b truncate"
        >
            <div class="flex justify-between">
                <p class="text-sm font-bold truncate">
                    {{ picture.title }}
                </p>
                <p class="text-sm font-bold">{{ picture.year }}</p>
            </div>
            <p
                class="text-sm prose max-w-none line-clamp-1"
                v-html="picture.description"
            ></p>
        </div>
    </Link>
</template>

<script>
import { defineComponent } from "vue";
import EmbeddedIcon from "@/Base/EmbeddedIcon";

export default defineComponent({
    components: { EmbeddedIcon },
    props: {
        items: Array,
        fixedWidth: {
            type: Boolean,
            default: false,
        },
    },
});
</script>
