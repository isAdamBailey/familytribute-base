<template>
    <app-layout
        :title="person.full_name"
        :description="`${person.full_name}'s obituary`"
    >
        <template #header>
            <Link :href="route('people.index')">People</Link> /
            {{ person.full_name }}
        </template>

        <div class="flex flex-wrap-reverse justify-between mt-8 mb-3">
            <h1 class="font-header font-header text-5xl md:text-7xl">
                {{ person.full_name }}
            </h1>
            <div v-if="$page.props.user">
                <jet-danger-button
                    class="mr-3"
                    aria-label="Delete Person"
                    @click="obituaryDeleteModalOpen = true"
                >
                    <i class="ri-delete-bin-fill"></i>
                </jet-danger-button>
                <base-button
                    aria-label="Edit Person"
                    @click="obituaryEditModalOpen = true"
                >
                    Edit <i class="ri-edit-2-fill"></i>
                </base-button>
            </div>
        </div>
        <div class="mb-3 text-sm font-semibold">
            {{ formatDate(person.obituary.birth_date) }} -
            {{ formatDate(person.obituary.death_date) }}
        </div>
        <div class="flex justify-center">
            <img
                class="md:w-2/3 object-cover rounded-xl"
                :src="person.obituary.headstone_url"
                :alt="`${person.full_name}'s headstone`"
            />
        </div>

        <social-share :title="`${person.full_name}'s obituary`" />

        <div
            class="html-content my-10 prose max-w-none"
            v-html="person.obituary.content"
        />

        <div
            v-if="person.pictures.length"
            class="flex snap-x space-x-1 overflow-x-scroll pb-8 px-3"
        >
            <pictures-container :items="person.pictures" :fixed-width="true" />
        </div>

        <div
            v-if="person.stories.length"
            class="flex snap-x space-x-1 overflow-x-scroll mt-5 pb-8 px-3"
        >
            <stories-container :items="person.stories" :fixed-width="true" />
        </div>
    </app-layout>

    <obituary-edit-modal
        :open="obituaryEditModalOpen"
        :person="person"
        @close="obituaryEditModalOpen = false"
    />
    <obituary-delete-modal
        :open="obituaryDeleteModalOpen"
        :person="person"
        @close="obituaryDeleteModalOpen = false"
    />
</template>

<script>
import { defineComponent } from "vue";
import { format, parseISO } from "date-fns";
import AppLayout from "@/Layouts/AppLayout.vue";
import ObituaryEditModal from "@/Modals/ObituaryEditModal";
import ObituaryDeleteModal from "@/Modals/ObituaryDeleteModal";
import JetDangerButton from "@/Base/DangerButton";
import PicturesContainer from "@/Components/PicturesContainer";
import StoriesContainer from "@/Components/StoriesContainer";
import SocialShare from "@/Components/SocialShare";

export default defineComponent({
    components: {
        SocialShare,
        StoriesContainer,
        PicturesContainer,
        JetDangerButton,
        ObituaryDeleteModal,
        AppLayout,
        ObituaryEditModal,
    },

    props: {
        person: Object,
    },

    data() {
        return {
            obituaryEditModalOpen: false,
            obituaryDeleteModalOpen: false,
        };
    },

    methods: {
        formatDate(date) {
            return format(parseISO(date), "PPP");
        },
    },
});
</script>
