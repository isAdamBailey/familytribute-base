<template>
    <app-layout>
        <template #header>
            <Link :href="route('stories.index')">Stories</Link> /
            {{ story.title }}
        </template>

        <div class="mt-8 mb-3 flex flex-wrap-reverse justify-between">
            <h1
                class="relative font-header text-5xl text-gray-800 dark:text-indigo-400 md:text-7xl"
            >
                <embedded-icon
                    :item="story"
                    color="text-gray-900 dark:text-indigo-300"
                />
                {{ story.title }}
            </h1>
            <div v-if="authenticated">
                <jet-danger-button
                    class="mr-3"
                    aria-label="Delete Story"
                    @click="storyDeleteModalOpen = true"
                >
                    <i class="ri-delete-bin-fill"></i>
                </jet-danger-button>
                <base-button
                    aria-label="Edit Story"
                    @click="storyEditModalOpen = true"
                >
                    Edit <i class="ri-edit-2-fill"></i>
                </base-button>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-between">
            <p v-if="story.year" class="text-gray-800 dark:text-indigo-400">
                Written in or around
                <span class="font-semibold">{{ story.year }}</span>
            </p>
            <social-share :title="story.title" />
        </div>

        <div
            class="prose mt-6 max-w-none italic text-gray-900 dark:text-gray-100"
            v-html="story.excerpt"
        />

        <div
            class="html-content prose mt-6 max-w-none text-gray-900 dark:text-gray-100"
            v-html="story.content"
        />

        <div v-if="story.people.length">
            <section-border />
            <related-people-container
                :people="story.people"
                title="People in this story"
            />
        </div>
    </app-layout>

    <story-edit-modal
        v-if="authenticated"
        :open="storyEditModalOpen"
        :story="story"
        :people="people"
        @close="storyEditModalOpen = false"
    />

    <story-delete-modal
        v-if="authenticated"
        :open="storyDeleteModalOpen"
        :story="story"
        @close="storyDeleteModalOpen = false"
    />
    <scroll-top />
</template>

<script setup>
import { ref, computed } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import StoryEditModal from "@/Modals/StoryEditModal.vue";
import StoryDeleteModal from "@/Modals/StoryDeleteModal.vue";
import JetDangerButton from "@/Base/DangerButton.vue";
import SocialShare from "@/Components/SocialShare.vue";
import EmbeddedIcon from "@/Base/EmbeddedIcon.vue";
import SectionBorder from "@/Base/SectionBorder.vue";
import RelatedPeopleContainer from "@/Components/RelatedPeopleContainer.vue";
import { usePage } from "@inertiajs/vue3";

defineProps({
    story: { type: Object, required: true },
    people: { type: Array, required: true },
});

const storyEditModalOpen = ref(false);
const storyDeleteModalOpen = ref(false);

const page = usePage();
const authenticated = computed(() => page.props.auth.user);
</script>
