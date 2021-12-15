<template>
    <Link
        v-for="(picture, index) in items"
        :key="index"
        :href="route('pictures.show', picture)"
        :class="fixedWidth ? 'w-48 md:w-60' : ''"
        class="snap-center bg-white shadow-xl shadow-indigo-200/50 rounded-lg hover:shadow-2xl hover:shadow-indigo-400/50 transition"
    >
        <img
            class="w-full h-48 object-cover rounded-t-lg"
            :src="picture.url"
            :alt="picture.title"
        />
        <div class="card-text-gradient p-3 rounded-b">
            <div class="flex justify-between">
                <p class="text-sm font-bold truncate">
                    {{ picture.title }}
                </p>
                <p class="text-sm font-bold">{{ picture.year }}</p>
            </div>
            <p class="text-sm prose max-w-none truncate">
                {{ picture.description }}
            </p>
        </div>
    </Link>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        items: Array,
        fixedWidth: {
            type: Boolean,
            default: false,
        },
    },
});
</script>
