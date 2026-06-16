<template>
    <AppLayout
        :title="person.full_name"
        :description="`${person.full_name}'s obituary`"
    >
        <template #header>
            <Link
                :href="route('people.index')"
                class="transition-colors hover:text-hearthlight"
                >People</Link
            >
            <span class="mx-1.5 text-gray-400">/</span>
            {{ person.full_name }}
        </template>

        <!-- Hero -->
        <div class="-mx-6 -mt-6 sm:-mx-20">
            <div
                v-if="person.obituary.background_photo_url"
                class="relative h-72 overflow-hidden"
            >
                <img
                    class="h-full w-full object-cover"
                    :src="person.obituary.background_photo_url"
                    :alt="`${person.full_name} background photo`"
                />
                <div
                    class="absolute inset-0 bg-gradient-to-b from-transparent via-amber-950/10 to-amber-950/60"
                ></div>
            </div>
            <div
                v-else
                class="h-44 bg-gradient-to-br from-amber-50 via-stone-100 to-amber-100 dark:from-stone-800 dark:via-stone-900 dark:to-amber-950"
            ></div>
        </div>

        <!-- Avatar -->
        <div class="-mt-20 flex justify-center">
            <img
                class="relative z-10 h-40 w-40 rounded-full border-4 border-white object-cover shadow-xl shadow-amber-200/60 dark:border-gray-800 dark:shadow-amber-900/40"
                :src="person.photo_url"
                :alt="person.full_name"
            />
        </div>

        <!-- Name, dates, share -->
        <div class="mt-5 text-center">
            <h1
                class="font-header text-[clamp(2.25rem,5vw,4rem)] font-bold leading-tight text-gray-900 dark:text-amber-400"
                style="text-wrap: balance"
            >
                {{ person.full_name }}
            </h1>
            <p
                v-if="lifeDates"
                class="mt-2 text-sm font-semibold text-gray-500 dark:text-stone-400"
            >
                {{ lifeDates }}
            </p>
            <div class="mt-4 flex justify-center">
                <social-share :title="`${person.full_name}'s obituary`" />
            </div>
        </div>

        <!-- Admin controls -->
        <div v-if="authenticated" class="mt-6 flex justify-end gap-2">
            <BaseButton
                aria-label="Edit Person"
                @click="obituaryEditModalOpen = true"
            >
                Edit <i class="ri-edit-2-fill ml-1"></i>
            </BaseButton>
            <JetDangerButton
                aria-label="Delete Person"
                @click="obituaryDeleteModalOpen = true"
            >
                <i class="ri-delete-bin-fill"></i>
            </JetDangerButton>
        </div>

        <!-- Ornamental divider -->
        <div class="my-10 flex items-center gap-3">
            <div class="h-px flex-1 bg-gray-100 dark:bg-stone-700"></div>
            <div class="h-1.5 w-1.5 rounded-full bg-hearthlight/40"></div>
            <div class="h-px w-8 bg-hearthlight/30"></div>
            <div class="h-1.5 w-1.5 rounded-full bg-hearthlight/40"></div>
            <div class="h-px flex-1 bg-gray-100 dark:bg-stone-700"></div>
        </div>

        <!-- Obituary (promoted above carousels) -->
        <div class="mx-auto max-w-2xl">
            <h2
                class="font-header text-3xl text-gray-800 dark:text-amber-400 md:text-4xl"
            >
                Obituary
            </h2>
            <div
                class="html-content prose prose-stone mt-6 max-w-none leading-relaxed text-gray-700 dark:prose-invert dark:text-gray-200"
                v-html="person.obituary.content"
            />
        </div>

        <!-- Pictures carousel -->
        <div v-if="person.pictures.length" class="mt-14">
            <h2
                class="font-header text-3xl text-gray-800 dark:text-amber-400 md:text-4xl"
            >
                Pictures of {{ person.first_name }}
            </h2>
            <div
                class="mt-4 flex snap-x space-x-1 overflow-x-scroll px-3 pb-8 scrollbar scrollbar-thumb-amber-600 scrollbar-thumb-rounded"
            >
                <PicturesContainer
                    :items="person.pictures"
                    :fixed-width="true"
                />
            </div>
        </div>

        <!-- Stories / Memories carousel -->
        <div v-if="person.stories.length" class="mt-14">
            <h2
                class="font-header text-3xl text-gray-800 dark:text-amber-400 md:text-4xl"
            >
                Memories of {{ person.first_name }}
            </h2>
            <div
                class="mt-4 flex snap-x space-x-1 overflow-x-scroll px-3 pb-8 scrollbar scrollbar-thumb-amber-600 scrollbar-thumb-rounded"
            >
                <StoriesContainer
                    :items="person.stories"
                    :fixed-width="true"
                />
            </div>
        </div>

        <!-- Family connections -->
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

const props = defineProps({
    person: { type: Object, required: true },
    people: { type: Array, required: true },
});

function formatDate(date) {
    if (!date) return null;
    return format(parseISO(date), "PPP");
}

const lifeDates = computed(() => {
    const birth = formatDate(props.person.obituary.birth_date);
    const death = formatDate(props.person.obituary.death_date);
    if (!birth && !death) return null;
    return `${birth ?? "?"} – ${death ?? "present"}`;
});

const obituaryEditModalOpen = ref(false);
const obituaryDeleteModalOpen = ref(false);

const page = usePage();
const authenticated = computed(() => page.props.auth.user);
</script>
