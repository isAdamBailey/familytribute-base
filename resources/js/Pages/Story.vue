<template>
    <AppLayout>
        <template #header>
            <Link
                :href="route('stories.index')"
                class="transition-colors hover:text-hearthlight"
                >Stories</Link
            >
            <span class="mx-1.5 text-gray-400">/</span>
            {{ story.title }}
        </template>

        <!-- Title + year -->
        <div class="mt-8">
            <div class="flex flex-wrap items-baseline gap-3">
                <h1
                    class="font-header text-[clamp(2rem,5vw,4rem)] font-bold leading-tight text-gray-900 dark:text-amber-400"
                    style="text-wrap: balance"
                >
                    {{ story.title }}
                </h1>
                <span
                    v-if="story.year"
                    class="shrink-0 rounded-full bg-amber-50 px-3 py-0.5 text-sm font-semibold text-hearthlight-deep dark:bg-amber-950/50 dark:text-amber-400"
                >
                    {{ story.year }}
                </span>
            </div>

            <!-- Private / featured indicators -->
            <div
                v-if="authenticated && (story.private || story.featured)"
                class="mt-2 flex items-center gap-2 text-sm text-gray-500 dark:text-stone-400"
            >
                <span v-if="story.featured" class="flex items-center gap-1">
                    <i class="ri-star-fill text-hearthlight"></i> Featured
                </span>
                <span v-if="story.private" class="flex items-center gap-1">
                    <i class="ri-git-repository-private-fill"></i> Private
                </span>
            </div>
        </div>

        <!-- Metadata: share + admin -->
        <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
            <SocialShare :title="story.title" />
            <div v-if="authenticated" class="flex gap-2">
                <BaseButton
                    aria-label="Edit Story"
                    @click="storyEditModalOpen = true"
                >
                    Edit <i class="ri-edit-2-fill ml-1"></i>
                </BaseButton>
                <JetDangerButton
                    aria-label="Delete Story"
                    @click="storyDeleteModalOpen = true"
                >
                    <i class="ri-delete-bin-fill"></i>
                </JetDangerButton>
            </div>
        </div>

        <!-- Divider -->
        <div class="my-8 flex items-center gap-3">
            <div class="h-px flex-1 bg-gray-100 dark:bg-stone-700"></div>
            <div class="h-1.5 w-1.5 rounded-full bg-hearthlight/40"></div>
            <div class="h-px w-8 bg-hearthlight/30"></div>
            <div class="h-1.5 w-1.5 rounded-full bg-hearthlight/40"></div>
            <div class="h-px flex-1 bg-gray-100 dark:bg-stone-700"></div>
        </div>

        <div class="mx-auto max-w-2xl">
            <!-- Excerpt as pull quote / lede -->
            <div
                v-if="story.excerpt"
                class="prose prose-stone mb-8 border-l-2 border-hearthlight/40 pl-5 text-lg italic leading-relaxed text-gray-700 dark:prose-invert dark:text-gray-300"
                v-html="story.excerpt"
            />

            <!-- Media player -->
            <div
                v-if="story.media_url"
                class="mb-8 rounded-xl border border-amber-100 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-950/40"
            >
                <div
                    class="mb-3 flex items-center gap-2 text-hearthlight-deep dark:text-amber-300"
                >
                    <i
                        :class="isVideo ? 'ri-video-line' : 'ri-headphone-line'"
                        class="text-lg"
                    ></i>
                    <span class="text-sm font-medium">
                        {{
                            isVideo
                                ? "Video recording"
                                : "Audio recording"
                        }}
                        of this story being spoken
                    </span>
                </div>
                <video
                    v-if="isVideo"
                    :src="story.media_url"
                    controls
                    class="w-full rounded-lg shadow-sm"
                />
                <audio v-else :src="story.media_url" controls class="w-full" />
            </div>

            <!-- Story body -->
            <div
                class="html-content prose prose-stone max-w-none leading-relaxed text-gray-800 dark:prose-invert dark:text-gray-200"
                v-html="story.content"
            />
        </div>

        <!-- People in this story -->
        <div v-if="story.people.length">
            <RelatedPeopleContainer
                :people="story.people"
                title="People in this story"
            />
        </div>
    </AppLayout>

    <StoryEditModal
        v-if="authenticated"
        :open="storyEditModalOpen"
        :story="story"
        :people="people"
        @close="storyEditModalOpen = false"
    />
    <StoryDeleteModal
        v-if="authenticated"
        :open="storyDeleteModalOpen"
        :story="story"
        @close="storyDeleteModalOpen = false"
    />
    <ScrollTop />
</template>

<script setup>
import JetDangerButton from "@/Base/DangerButton.vue";
import RelatedPeopleContainer from "@/Components/RelatedPeopleContainer.vue";
import SocialShare from "@/Components/SocialShare.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import StoryDeleteModal from "@/Modals/StoryDeleteModal.vue";
import StoryEditModal from "@/Modals/StoryEditModal.vue";
import { usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    story: { type: Object, required: true },
    people: { type: Array, required: true },
});

const storyEditModalOpen = ref(false);
const storyDeleteModalOpen = ref(false);

const page = usePage();
const authenticated = computed(() => page.props.auth.user);

const isVideo = computed(() => {
    if (!props.story.media_url) return false;
    return /\.(mp4|webm|mov)$/i.test(props.story.media_url);
});
</script>
