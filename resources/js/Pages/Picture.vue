<template>
    <app-layout>
        <template #header>
            <Link :href="route('pictures.index')">Pictures</Link> /
            {{ picture.title }}
        </template>

        <div class="mt-8 mb-3 flex flex-wrap-reverse justify-between">
            <h1
                class="font-header text-5xl text-gray-800 dark:text-indigo-400 md:text-7xl"
            >
                {{ picture.title }}
            </h1>

            <div v-if="$page.props.user">
                <jet-danger-button
                    class="mr-3"
                    aria-label="Delete Picture"
                    @click="pictureDeleteModalOpen = true"
                >
                    <i class="ri-delete-bin-fill"></i>
                </jet-danger-button>
                <base-button
                    aria-label="Edit Picture"
                    @click="pictureEditModalOpen = true"
                >
                    Edit <i class="ri-edit-2-fill"></i>
                </base-button>
            </div>
        </div>
        <p class="mb-3 text-gray-800 dark:text-indigo-400 md:mb-7">
            Taken in or around
            <span class="font-semibold">{{ picture.year }}</span>
        </p>
        <div class="relative">
            <img
                class="w-full rounded-lg object-cover"
                :src="picture.url"
                :alt="picture.title"
            />
            <embedded-icon :item="picture" />
        </div>

        <social-share class="mt-5" :title="picture.title" />

        <div
            class="html-content prose my-6 max-w-none text-gray-700 dark:text-gray-100"
            v-html="picture.description"
        />

        <section-border />

        <tagged-people
            :people="picture.people"
            title="People in this picture"
        />
    </app-layout>

    <picture-edit-modal
        v-if="$page.props.user"
        :open="pictureEditModalOpen"
        :picture="picture"
        :people="people"
        @close="pictureEditModalOpen = false"
    />

    <picture-delete-modal
        v-if="$page.props.user"
        :open="pictureDeleteModalOpen"
        :picture="picture"
        @close="pictureDeleteModalOpen = false"
    />
    <scroll-top />
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import PictureEditModal from "@/Modals/PictureEditModal";
import PictureDeleteModal from "@/Modals/PictureDeleteModal";
import TaggedPeople from "@/Components/TaggedPeople";
import JetDangerButton from "@/Base/DangerButton";
import SocialShare from "@/Components/SocialShare";
import EmbeddedIcon from "@/Base/EmbeddedIcon";
import SectionBorder from "@/Base/SectionBorder";

export default defineComponent({
    components: {
        SectionBorder,
        EmbeddedIcon,
        SocialShare,
        JetDangerButton,
        PictureDeleteModal,
        PictureEditModal,
        AppLayout,
        TaggedPeople,
    },

    props: {
        picture: Object,
        people: Array,
    },

    data() {
        return {
            pictureEditModalOpen: false,
            pictureDeleteModalOpen: false,
        };
    },
});
</script>
