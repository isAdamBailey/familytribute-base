<template>
    <app-layout title="Dashboard">
        <template #header> Dashboard </template>

        <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
            <div class="flex items-baseline">
                <jet-application-mark class="block h-12 w-auto" />
                <div class="ml-3 text-2xl md:text-5xl">
                    {{ $inertia.page.props.siteName }}
                </div>
            </div>

            <div class="mt-8 text-2xl">
                Welcome to {{ $inertia.page.props.siteName }}'s dashboard,
                {{ $page.props.user.name }}!
            </div>

            <div class="mt-6 text-gray-500">
                Here you can add new stories, photos, obituaries and more!
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <store-picture-form :people-options="peopleOptions" />
            <jet-section-border />

            <store-story-form :people-options="peopleOptions" />
            <jet-section-border />

            <store-obituary-form />
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetApplicationMark from "@/Base/ApplicationMark.vue";
import JetSectionBorder from "@/Base/SectionBorder.vue";
import StorePictureForm from "@/Pages/Dashboard/Partials/StorePictureForm";
import StoreObituaryForm from "@/Pages/Dashboard/Partials/StoreObituaryForm";
import StoreStoryForm from "@/Pages/Dashboard/Partials/StoreStoryForm";

export default defineComponent({
    components: {
        AppLayout,
        JetApplicationMark,
        JetSectionBorder,
        StorePictureForm,
        StoreObituaryForm,
        StoreStoryForm,
    },

    props: {
        people: Array,
    },

    computed: {
        peopleOptions() {
            return this.people.map((person) => {
                return {
                    value: person.id,
                    label: person.full_name,
                };
            });
        },
    },
});
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
