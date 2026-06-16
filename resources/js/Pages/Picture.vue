<template>
    <AppLayout>
        <template #header>
            <Link
                :href="route('pictures.index')"
                class="transition-colors hover:text-hearthlight"
                >Pictures</Link
            >
            <span class="mx-1.5 text-gray-400">/</span>
            {{ picture.title }}
        </template>

        <!-- Photo — full-bleed to card edges, natural aspect ratio -->
        <div class="-mx-6 -mt-6 relative sm:-mx-20">
            <img
                class="w-full"
                :src="picture.url"
                :alt="picture.title"
            />
            <EmbeddedIcon :item="picture" />
        </div>

        <!-- Title + year -->
        <div class="mt-8 flex flex-wrap items-baseline gap-3">
            <h1
                class="font-header text-[clamp(2rem,5vw,3.5rem)] font-bold leading-tight text-gray-900 dark:text-amber-400"
                style="text-wrap: balance"
            >
                {{ picture.title }}
            </h1>
            <span
                v-if="picture.year"
                class="shrink-0 rounded-full bg-amber-50 px-3 py-0.5 text-sm font-semibold text-hearthlight-deep dark:bg-amber-950/50 dark:text-amber-400"
            >
                {{ picture.year }}
            </span>
        </div>

        <!-- Share + admin controls -->
        <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
            <SocialShare :title="picture.title" />
            <div v-if="authenticated" class="flex gap-2">
                <BaseButton
                    aria-label="Edit Picture"
                    @click="pictureEditModalOpen = true"
                >
                    Edit <i class="ri-edit-2-fill ml-1"></i>
                </BaseButton>
                <JetDangerButton
                    aria-label="Delete Picture"
                    @click="pictureDeleteModalOpen = true"
                >
                    <i class="ri-delete-bin-fill"></i>
                </JetDangerButton>
            </div>
        </div>

        <!-- Description -->
        <div
            v-if="picture.description"
            class="html-content prose prose-stone mx-auto mt-8 max-w-2xl leading-relaxed text-gray-700 dark:prose-invert dark:text-gray-200"
            v-html="picture.description"
        />

        <!-- People in this picture -->
        <div v-if="picture.people.length">
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
