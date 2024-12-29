<template>
    <AppLayout
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
            <div v-if="authenticated">
                <JetDangerButton
                    class="mr-3"
                    aria-label="Delete Person"
                    @click="obituaryDeleteModalOpen = true"
                >
                    <i class="ri-delete-bin-fill"></i>
                </JetDangerButton>
                <BaseButton
                    aria-label="Edit Person"
                    @click="obituaryEditModalOpen = true"
                >
                    Edit <i class="ri-edit-2-fill"></i>
                </BaseButton>
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
        <div class="flex justify-center" :class="person.obituary.background_photo_url ? '-mt-40' : 'mt-18'">
            <div class="avatar relative h-80 w-80 rounded-full z-40">
                <img
                    class="relative h-full w-full rounded-full border-4 border-gray-900"
                    :src="person.photo_url"
                    :alt="person.full_name"
                />
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
                <PicturesContainer
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
                <StoriesContainer
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
            <RelatedPeopleContainer
                :people="person.parents"
                title="Parents"
            />
        </div>
        <div v-if="person.children.length">
            <RelatedPeopleContainer
                :people="person.children"
                title="Children"
            />
        </div>
    </AppLayout>

    <ObituaryEditModal
        v-if="authenticated"
        :open="obituaryEditModalOpen"
        :person="person"
        :people="people"
        @close="obituaryEditModalOpen = false"
    />
    <ObituaryDeleteModal
        v-if="authenticated"
        :open="obituaryDeleteModalOpen"
        :person="person"
        @close="obituaryDeleteModalOpen = false"
    />
    <ScrollTop />
</template>

<script setup>
import JetDangerButton from "@/Base/DangerButton.vue";
import PicturesContainer from "@/Components/PicturesContainer.vue";
import RelatedPeopleContainer from "@/Components/RelatedPeopleContainer.vue";
import SocialShare from "@/Components/SocialShare.vue";
import StoriesContainer from "@/Components/StoriesContainer.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import ObituaryDeleteModal from "@/Modals/ObituaryDeleteModal.vue";
import ObituaryEditModal from "@/Modals/ObituaryEditModal.vue";
import { usePage } from "@inertiajs/vue3";
import { format, parseISO } from "date-fns";
import { computed, ref } from "vue";

defineProps({
    person: { type: Object, required: true },
    people: { type: Array, required: true },
});

function formatDate(date) {
    return format(parseISO(date), "PPP");
}

const obituaryEditModalOpen = ref(false);
const obituaryDeleteModalOpen = ref(false);

const page = usePage();
const authenticated = computed(() => page.props.auth.user);
</script>
