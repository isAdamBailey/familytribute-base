<template>
    <app-layout :title="picture.title">
        <template #header>
            <Link :href="route('pictures.index')">Pictures</Link> /
            {{ picture.title }}
        </template>

        <div class="flex flex-wrap-reverse justify-between mt-8 mb-3">
            <h1 class="font-header font-header text-5xl md:text-7xl">
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
        <div class="mb-3 text-sm font-semibold">
            {{ picture.year }}
        </div>
        <div class="relative">
            <img
                class="w-full object-cover rounded-lg"
                :src="picture.url"
                :alt="picture.title"
            />
            <featured-star :picture="picture" />
        </div>

        <social-share :title="picture.title" />

        <div
            class="html-content mt-6 prose max-w-none"
            v-html="picture.description"
        />

        <tagged-people
            :people="picture.people"
            title="People in this picture"
        />
    </app-layout>

    <picture-edit-modal
        :open="pictureEditModalOpen"
        :picture="picture"
        :people="people"
        @close="pictureEditModalOpen = false"
    />

    <picture-delete-modal
        :open="pictureDeleteModalOpen"
        :picture="picture"
        @close="pictureDeleteModalOpen = false"
    />
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import PictureEditModal from "@/Modals/PictureEditModal";
import PictureDeleteModal from "@/Modals/PictureDeleteModal";
import TaggedPeople from "@/Components/TaggedPeople";
import JetDangerButton from "@/Base/DangerButton";
import SocialShare from "@/Components/SocialShare";
import FeaturedStar from "../Components/FeaturedStar";

export default defineComponent({
    components: {
        FeaturedStar,
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
