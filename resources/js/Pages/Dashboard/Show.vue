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
                Welcome to {{ $inertia.page.props.settings.title }}'s dashboard,
                {{ $page.props.user.name }}!
            </div>

            <info-text>
                If nothing has been added to the site yet,
                <strong>this is where you start!</strong>
            </info-text>
            <div class="mt-6 text-gray-500">
                <p>
                    Here you can add new stories, photos, obituaries and more!
                </p>
                <ul class="mt-2 list-disc">
                    <li class="ml-4">
                        If you would like to tag photos to a person, we suggest
                        starting by adding
                        <a
                            class="text-sky-500 font-bold underline underline-offset-1 decoration-sky-500 hover:decoration-2 transition"
                            href="#new-obituary"
                            >an obituary</a
                        >
                        first.
                    </li>
                    <li class="ml-4">
                        If you want to change the name or description on the
                        home page, turn off registration, or change the
                        registration secret, go to
                        <a
                            class="text-sky-500 font-bold underline underline-offset-1 decoration-sky-500 hover:decoration-2 transition"
                            href="#site-settings"
                            >site settings</a
                        >.
                    </li>
                </ul>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <store-picture-form :people-options="peopleOptions" />
            <jet-section-border />

            <store-story-form :people-options="peopleOptions" />
            <jet-section-border />

            <store-obituary-form id="new-obituary" />
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
import StoreObituaryForm from "@/Pages/Dashboard/Partials/StoreObituaryForm";
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
        StoreObituaryForm,
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
