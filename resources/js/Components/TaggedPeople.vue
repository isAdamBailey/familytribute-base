<template>
    <div v-if="people.length">
        <div class="my-3 text-2xl">{{ title }}</div>
        <div class="flex flex-wrap">
            <div v-for="person in people" :key="person.id">
                <pill class="mb-2 mr-2">
                    <Link :href="route('people.show', person)">{{
                        person.full_name
                    }}</Link>
                </pill>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import Pill from "@/Base/Pill";

export default defineComponent({
    components: { Pill },
    props: {
        people: Array,
        title: String,
    },
});
</script>
