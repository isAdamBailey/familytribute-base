<template>
    <div
        class="grid grid-cols-[repeat(auto-fit,minmax(16rem,1fr))] gap-2 max-w-7xl mx-auto md:p-4"
    >
        <Link
            v-for="(person, index) in items"
            :key="index"
            :href="route('people.show', person.slug)"
            class="bg-white shadow-indigo-200/50 rounded-lg hover:shadow-xl hover:shadow-indigo-300/50 hover:opacity-80 transition"
        >
            <img
                class="w-full h-48 object-cover rounded-t-lg"
                :src="person.photo_url"
                :alt="person.full_name"
            />
            <div class="card-text-gradient px-3 py-2 rounded-b-lg">
                <p class="font-bold truncate">
                    {{ person.full_name }}
                </p>
                <p class="text-xs font-semibold truncate">
                    {{ formatDate(person.obituary.birth_date) }} -
                    {{ formatDate(person.obituary.death_date) }}
                </p>
            </div>
        </Link>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import { format, parseISO } from "date-fns";

export default defineComponent({
    props: {
        items: Array,
    },
    methods: {
        fullName(obituary) {
            return `${this.capitalize(obituary.first_name)} ${this.capitalize(
                obituary.last_name
            )}`;
        },
        capitalize(word) {
            return word[0].toUpperCase() + word.slice(1);
        },
        formatDate(date) {
            return format(parseISO(date), "PPP");
        },
    },
});
</script>
