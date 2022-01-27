<template>
    <div
        class="mx-auto grid max-w-7xl grid-cols-[repeat(auto-fit,minmax(16rem,1fr))] gap-2 md:p-4"
    >
        <Link
            v-for="(person, index) in items"
            :key="index"
            :href="route('people.show', person.slug)"
            class="rounded-lg bg-white shadow-indigo-200/50 transition hover:opacity-80 hover:shadow-xl hover:shadow-indigo-300/50"
        >
            <img
                class="h-48 w-full rounded-t-lg object-cover"
                :src="person.photo_url"
                :alt="person.full_name"
            />
            <div class="card-text-gradient rounded-b-lg px-3 py-2">
                <p class="truncate font-bold">
                    {{ person.full_name }}
                </p>
                <p class="truncate text-xs font-semibold">
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
