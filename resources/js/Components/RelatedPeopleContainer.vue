<template>
    <h3 class="mt-10 font-header text-3xl text-gray-800 dark:text-indigo-400">
        {{ title }}
    </h3>
    <div
        class="flex snap-x space-x-5 overflow-x-scroll pb-8 scrollbar scrollbar-thumb-indigo-500 scrollbar-thumb-rounded"
    >
        <Link
            v-for="(person, index) in people"
            prefetch
            :key="index"
            :href="route('people.show', person.slug)"
            class="shrink-0 snap-start rounded-lg bg-white shadow-indigo-200/50 transition hover:opacity-80 hover:shadow-xl hover:shadow-indigo-300/50"
        >
            <div class="card-text-gradient rounded-t-lg px-3 py-2">
                <p class="truncate font-bold">
                    {{ person.full_name }}
                </p>
            </div>
            <img
                class="h-full w-36 max-h-60 rounded-b-lg object-cover"
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
