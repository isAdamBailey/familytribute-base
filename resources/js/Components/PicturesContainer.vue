<template>
    <Link
        v-for="(picture, index) in items"
        :key="index"
        :href="route('pictures.show', picture)"
        :class="fixedWidth ? 'w-60' : ''"
        class="relative snap-center bg-white shadow-xl shadow-indigo-200/50 rounded-lg hover:shadow-2xl hover:shadow-indigo-400/50 transition"
    >
        <img
            class="w-full h-48 object-cover rounded-t-lg"
            :src="picture.url"
            :alt="picture.title"
        />
        <featured-star :picture="picture" />

        <div class="card-text-gradient max-h-16 p-3 rounded-b truncate">
            <div class="flex justify-between">
                <p class="text-sm font-bold truncate">
                    {{ picture.title }}
                </p>
                <p class="text-sm font-bold">{{ picture.year }}</p>
            </div>
            <p
                class="text-sm prose max-w-none truncate"
                v-html="picture.description"
            ></p>
        </div>
    </Link>
</template>

<script>
import { defineComponent } from "vue";
import FeaturedStar from "./FeaturedStar";

export default defineComponent({
    components: { FeaturedStar },
    props: {
        items: Array,
        fixedWidth: {
            type: Boolean,
            default: false,
        },
    },
});
</script>
