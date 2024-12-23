<template>
    <AppLayout>
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

            <div v-if="authenticated">
                <JetDangerButton
                    class="mr-3"
                    aria-label="Delete Picture"
                    @click="pictureDeleteModalOpen = true"
                >
                    <i class="ri-delete-bin-fill"></i>
                </JetDangerButton>
                <BaseButton
                    aria-label="Edit Picture"
                    @click="pictureEditModalOpen = true"
                >
                    Edit <i class="ri-edit-2-fill"></i>
                </BaseButton>
            </div>
        </div>
        <div class="flex flex-wrap items-center justify-between">
            <p class="text-gray-800 dark:text-indigo-400 md:mb-7">
                Taken in or around
                <span class="font-semibold">{{ picture.year }}</span>
            </p>
            <SocialShare :title="picture.title" />
        </div>
        <div class="relative">
            <img
                class="w-full rounded-lg object-cover"
                :src="picture.url"
                :alt="picture.title"
            />
            <EmbeddedIcon :item="picture" />
        </div>

        <div
            class="html-content prose my-6 max-w-none text-gray-700 dark:text-gray-100"
            v-html="picture.description"
        />

        <div v-if="picture.people.length">
            <SectionBorder />
            <RelatedPeopleContainer
                :people="picture.people"
                title="People in this picture"
            />
        </div>
    </AppLayout>

    <PictureEditModal
        v-if="authenticated"
        :open="pictureEditModalOpen"
        :picture="picture"
        :people="people"
        @close="pictureEditModalOpen = false"
    />

    <PictureDeleteModal
        v-if="authenticated"
        :open="pictureDeleteModalOpen"
        :picture="picture"
        @close="pictureDeleteModalOpen = false"
    />
    <scroll-top />
</template>

<script setup>
import JetDangerButton from "@/Base/DangerButton.vue";
import EmbeddedIcon from "@/Base/EmbeddedIcon.vue";
import SectionBorder from "@/Base/SectionBorder.vue";
import RelatedPeopleContainer from "@/Components/RelatedPeopleContainer.vue";
import SocialShare from "@/Components/SocialShare.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import PictureDeleteModal from "@/Modals/PictureDeleteModal.vue";
import PictureEditModal from "@/Modals/PictureEditModal.vue";
import { usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

defineProps({
    picture: { type: Object, required: true },
    people: { type: Array, required: true },
});

const pictureEditModalOpen = ref(false);
const pictureDeleteModalOpen = ref(false);

const page = usePage();
const authenticated = computed(() => page.props.auth.user);
</script>
