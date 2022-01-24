<template>
    <app-layout>
        <template #header>
            <Link :href="route('stories.index')">Stories</Link> /
            {{ story.title }}
        </template>

        <div class="flex flex-wrap-reverse justify-between mt-8 mb-3">
            <h1
                class="relative font-header text-gray-800 dark:text-indigo-400 text-5xl md:text-7xl"
            >
                <embedded-icon
                    :item="story"
                    color="text-gray-900 dark:text-indigo-300"
                />
                {{ story.title }}
            </h1>
            <div v-if="$page.props.user">
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

        <p v-if="story.year" class="text-gray-800 dark:text-indigo-400">
            Written in or around
            <span class="font-semibold">{{ story.year }}</span>
        </p>

        <div
            class="italic mt-6 text-gray-900 dark:text-gray-100 prose max-w-none"
            v-html="story.excerpt"
        />

        <div
            class="html-content mt-6 text-gray-900 dark:text-gray-100 prose max-w-none"
            v-html="story.content"
        />

        <social-share class="my-5" :title="story.title" />

        <section-border />
        <tagged-people :people="story.people" title="People in this story" />
    </app-layout>

    <story-edit-modal
        v-if="$page.props.user"
        :open="storyEditModalOpen"
        :story="story"
        :people="people"
        @close="storyEditModalOpen = false"
    />

    <story-delete-modal
        v-if="$page.props.user"
        :open="storyDeleteModalOpen"
        :story="story"
        @close="storyDeleteModalOpen = false"
    />
    <scroll-top />
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import StoryEditModal from "@/Modals/StoryEditModal";
import TaggedPeople from "@/Components/TaggedPeople";
import StoryDeleteModal from "@/Modals/StoryDeleteModal";
import JetDangerButton from "@/Base/DangerButton";
import SocialShare from "@/Components/SocialShare";
import EmbeddedIcon from "@/Base/EmbeddedIcon";
import SectionBorder from "@/Base/SectionBorder";
import Pill from "../Base/Pill";

export default defineComponent({
    components: {
        Pill,
        SectionBorder,
        EmbeddedIcon,
        SocialShare,
        JetDangerButton,
        StoryDeleteModal,
        AppLayout,
        StoryEditModal,
        TaggedPeople,
    },

    props: {
        story: Object,
        people: Array,
    },

    data() {
        return {
            storyEditModalOpen: false,
            storyDeleteModalOpen: false,
        };
    },
});
</script>
