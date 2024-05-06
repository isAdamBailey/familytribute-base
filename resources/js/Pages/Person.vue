<template>
    <app-layout
        :title="person.full_name"
        :description="`${person.full_name}'s obituary`"
    >
        <template #header>
            <Link :href="route('people.index')">People</Link> /
            {{ person.full_name }}
        </template>

        <div class="mt-8 flex flex-wrap-reverse justify-between">
            <h1
                class="font-header text-5xl text-gray-800 dark:text-indigo-400 md:text-7xl"
            >
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

        <div class="flex flex-wrap items-center justify-between">
            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                {{ formatDate(person.obituary.birth_date) }} -
                {{ formatDate(person.obituary.death_date) }}
            </div>
            <social-share :title="`${person.full_name}'s obituary`" />
        </div>

        <div
            v-if="person.obituary.background_photo_url"
            class="h-80 w-full rounded bg-cover bg-center bg-no-repeat"
            :style="`background-image: url(${person.obituary.background_photo_url});`"
        >
            <img
                class="h-full w-full opacity-0"
                :src="person.obituary.background_photo_url"
                alt="background image"
            />
        </div>
        <div
            v-else
            class="via-trueGray-200 h-80 w-full bg-gradient-to-t from-violet-400 to-transparent"
        ></div>
        <div class="flex justify-center">
            <div class="z-40 -mt-28">
                <div class="avatar relative h-60 w-60 rounded-full">
                    <img
                        class="relative h-full w-full rounded-full border-4 border-gray-900"
                        :src="person.photo_url"
                        alt=""
                    />
                </div>
            </div>
        </div>

        <div v-if="person.pictures.length" class="-mt-16">
            <h3
                class="invisible font-header text-3xl text-gray-800 dark:text-indigo-400 md:visible"
            >
                Pictures of {{ person.first_name }}
            </h3>
            <div
                class="flex snap-x space-x-1 overflow-x-scroll px-3 pb-8 scrollbar scrollbar-thumb-indigo-500 scrollbar-thumb-rounded"
            >
                <pictures-container
                    :items="person.pictures"
                    :fixed-width="true"
                />
            </div>
        </div>

        <div
            v-if="person.stories.length"
            :class="person.pictures.length ? 'mt-5' : '-mt-16'"
        >
            <h3
                class="invisible font-header text-3xl text-gray-800 dark:text-indigo-400 md:visible"
            >
                Memories of {{ person.first_name }}
            </h3>
            <div
                class="flex snap-x space-x-1 overflow-x-scroll px-3 pb-8 scrollbar scrollbar-thumb-indigo-500 scrollbar-thumb-rounded"
            >
                <stories-container
                    :items="person.stories"
                    :fixed-width="true"
                />
            </div>
        </div>

        <h1
            class="mt-10 font-header text-3xl text-gray-800 dark:text-indigo-400 md:text-5xl"
        >
            Obituary
        </h1>

        <div
            class="html-content prose my-10 max-w-none text-gray-700 dark:text-gray-100"
            v-html="person.obituary.content"
        />

        <div v-if="person.parents.length">
            <related-people-container
                :people="person.parents"
                title="Parents"
            />
        </div>
        <div v-if="person.children.length">
            <related-people-container
                :people="person.children"
                title="Children"
            />
        </div>
    </app-layout>

    <obituary-edit-modal
        v-if="$page.props.user"
        :open="obituaryEditModalOpen"
        :person="person"
        :people="people"
        @close="obituaryEditModalOpen = false"
    />
    <obituary-delete-modal
        v-if="$page.props.user"
        :open="obituaryDeleteModalOpen"
        :person="person"
        @close="obituaryDeleteModalOpen = false"
    />
    <scroll-top />
</template>

<script>
import { defineComponent } from "vue";
import { format, parseISO } from "date-fns";
import AppLayout from "@/Layouts/AppLayout.vue";
import ObituaryEditModal from "@/Modals/ObituaryEditModal.vue";
import ObituaryDeleteModal from "@/Modals/ObituaryDeleteModal.vue";
import JetDangerButton from "@/Base/DangerButton.vue";
import PicturesContainer from "@/Components/PicturesContainer.vue";
import StoriesContainer from "@/Components/StoriesContainer.vue";
import SocialShare from "@/Components/SocialShare.vue";
import RelatedPeopleContainer from "@/Components/RelatedPeopleContainer.vue";

export default defineComponent({
    components: {
        RelatedPeopleContainer,
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
        people: Array,
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
