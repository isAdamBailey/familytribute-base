<template>
    <app-layout :title="story.title">
        <template #header>
            <Link :href="route('stories.index')">Stories</Link> /
            {{ story.title }}
        </template>

        <div class="flex flex-wrap-reverse justify-between mt-8 mb-3">
            <h1 class="font-header text-3xl md:text-6xl">{{ story.title }}</h1>
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

        <div class="italic mt-6 prose max-w-none" v-html="story.excerpt" />

        <div
            class="html-content mt-6 prose max-w-none"
            v-html="story.content"
        />

        <social-share :title="story.title" />

        <tagged-people :people="story.people" title="People in this story" />
    </app-layout>

    <story-edit-modal
        :open="storyEditModalOpen"
        :story="story"
        :people="people"
        @close="storyEditModalOpen = false"
    />

    <story-delete-modal
        :open="storyDeleteModalOpen"
        :story="story"
        @close="storyDeleteModalOpen = false"
    />
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import StoryEditModal from "@/Modals/StoryEditModal";
import TaggedPeople from "@/Components/TaggedPeople";
import StoryDeleteModal from "@/Modals/StoryDeleteModal";
import JetDangerButton from "@/Base/DangerButton";
import SocialShare from "@/Components/SocialShare";

export default defineComponent({
    components: {
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
