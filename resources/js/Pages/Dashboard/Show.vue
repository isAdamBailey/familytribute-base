<template>
    <app-layout title="Dashboard">
        <template #header> Dashboard </template>

        <div
            class="border-b border-gray-200 bg-white p-6 dark:border-indigo-300 dark:bg-gray-900 sm:px-20"
        >
            <div class="flex items-baseline">
                <jet-application-mark class="block h-12 w-auto" />
                <div
                    class="ml-3 font-header text-3xl text-gray-800 dark:text-indigo-500 md:text-5xl"
                >
                    {{ $inertia.page.props.settings.title }}
                </div>
            </div>

            <div class="mt-8 text-2xl text-gray-800 dark:text-indigo-300">
                Welcome to {{ $inertia.page.props.settings.title }}'s dashboard,
                {{ $page.props.user.name }}!
            </div>

            <info-text>
                If nothing has been added to the site yet,
                <strong>this is where you start!</strong>
            </info-text>
            <div class="mt-6 text-gray-500 dark:text-indigo-300">
                <p>
                    Here you can add new stories, photos, obituaries and more!
                </p>
                <ul class="mt-2 list-disc">
                    <li class="ml-4">
                        We suggest starting by adding
                        <a
                            class="font-bold text-sky-500 underline decoration-sky-500 underline-offset-1 transition hover:decoration-2"
                            href="#new-person"
                            >a person</a
                        >
                        first.
                    </li>
                    <li class="ml-4">
                        If you want to change the name or description on the
                        home page, turn off registration, or change the
                        registration secret, go to
                        <a
                            class="font-bold text-sky-500 underline decoration-sky-500 underline-offset-1 transition hover:decoration-2"
                            href="#site-settings"
                            >site settings</a
                        >.
                    </li>
                </ul>
            </div>
        </div>

        <div class="mx-auto max-w-7xl py-10 sm:px-6 lg:px-8">
            <store-picture-form :people-options="peopleOptions" />
            <jet-section-border />

            <store-story-form :people-options="peopleOptions" />
            <jet-section-border />

            <store-person-form
                id="new-person"
                :people-options="peopleOptions"
            />
            <jet-section-border />

            <update-settings-form id="site-settings" :settings="settings" />
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetApplicationMark from "@/Base/ApplicationMark.vue";
import JetSectionBorder from "@/Base/SectionBorder.vue";
import StorePictureForm from "@/Pages/Dashboard/Partials/StorePictureForm";
import StorePersonForm from "@/Pages/Dashboard/Partials/StorePersonForm";
import StoreStoryForm from "@/Pages/Dashboard/Partials/StoreStoryForm";
import UpdateSettingsForm from "@/Pages/Dashboard/Partials/UpdateSettingsForm";
import InfoText from "@/Base/InfoText";

export default defineComponent({
    components: {
        UpdateSettingsForm,
        AppLayout,
        JetApplicationMark,
        JetSectionBorder,
        StorePictureForm,
        StorePersonForm,
        StoreStoryForm,
        InfoText,
    },

    props: {
        people: Array,
        settings: Object,
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
