<template>
    <h3
        class="mt-10 font-header text-gray-800 dark:text-indigo-400 text-3xl md:text-5xl"
    >
        {{ title }}
    </h3>
    <div
        class="flex snap-x overflow-x-scroll space-x-5 md:space-x-10 pb-8 px-3"
    >
        <Link
            v-for="(person, index) in people"
            :key="index"
            :href="route('people.show', person.slug)"
            class="snap-center bg-white shadow-indigo-200/50 rounded-lg hover:shadow-xl hover:shadow-indigo-300/50 hover:opacity-80 transition"
        >
            <div class="card-text-gradient px-3 py-2 rounded-t-lg">
                <p class="font-bold truncate">
                    {{ person.full_name }}
                </p>
            </div>
            <img
                class="w-full h-36 object-cover rounded-b-lg"
                :src="person.photo_url"
                :alt="person.full_name"
            />
        </Link>
    </div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        people: Array,
        title: String,
    },
});
</script>
