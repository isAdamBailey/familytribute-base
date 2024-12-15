<template>
    <home-layout>
        <div class="mb-5 flex flex-col-reverse md:mb-7 lg:flex-row">
            <div>
                <div
                    class="font-header text-6xl font-bold text-gray-700 dark:text-indigo-400 md:text-8xl"
                    v-html="settings.title"
                ></div>
                <div
                    class="html-content text-l mt-6 text-gray-500 dark:text-indigo-300 md:text-2xl"
                    v-html="settings.description"
                ></div>
            </div>
            <application-mark class="mx-auto mb-6 w-full md:w-1/2 lg:mb-0" />
        </div>

        <section class="grid grid-cols-2 gap-1 lg:grid-cols-4">
            <div v-if="pictures[0]" class="relative lg:col-span-2">
                <img
                    class="h-full w-full object-cover"
                    :src="pictures[0].url"
                    :alt="pictures[0].title"
                />
                <div
                    class="absolute inset-x-0 bottom-0 w-full truncate bg-white/30 py-2.5 text-center text-sm leading-4 text-white backdrop-blur-sm line-clamp-1"
                    v-html="pictures[0].description"
                ></div>
            </div>
            <div v-if="pictures[1]" class="relative">
                <img
                    class="h-full w-full object-cover"
                    :src="pictures[1].url"
                    :alt="pictures[1].title"
                />
                <div
                    class="absolute inset-x-0 bottom-0 w-full truncate bg-white/30 py-2.5 text-center text-sm leading-4 text-white backdrop-blur-sm line-clamp-1"
                    v-html="pictures[1].description"
                ></div>
            </div>
            <div
                v-if="pictures[2]"
                class="relative col-span-2 lg:col-span-1 lg:row-start-2"
            >
                <img
                    class="h-full w-full object-cover"
                    :src="pictures[2].url"
                    :alt="pictures[2].title"
                />
                <div
                    class="absolute inset-x-0 bottom-0 w-full truncate bg-white/30 py-2.5 text-center text-sm leading-4 text-white backdrop-blur-sm line-clamp-1"
                    v-html="pictures[2].description"
                ></div>
            </div>
            <div v-if="pictures[3]" class="relative lg:col-span-2">
                <img
                    class="h-full w-full object-cover"
                    :src="pictures[3].url"
                    :alt="pictures[3].title"
                />
                <div
                    class="absolute inset-x-0 bottom-0 w-full truncate bg-white/30 py-2.5 text-center text-sm leading-4 text-white backdrop-blur-sm line-clamp-1"
                    v-html="pictures[3].description"
                ></div>
            </div>
            <div
                v-if="pictures[4]"
                class="relative lg:col-start-4 lg:row-span-2 lg:row-start-1"
            >
                <img
                    class="h-full w-full object-cover"
                    :src="pictures[4].url"
                    :alt="pictures[4].title"
                />
                <div
                    class="absolute inset-x-0 bottom-0 w-full truncate bg-white/30 py-2.5 text-center text-sm leading-4 text-white backdrop-blur-sm line-clamp-1"
                    v-html="pictures[4].description"
                ></div>
            </div>
        </section>

        <div class="mt-10 flex flex-wrap justify-center">
            <Link :href="route('people.index')">
                <attention-button class="mb-5 mr-5">
                    <i class="ri-user-shared-fill mr-2"></i>
                    People
                </attention-button>
            </Link>
            <Link :href="route('pictures.index')">
                <attention-button>
                    <i class="ri-image-2-fill mr-2"></i>
                    Pictures
                </attention-button>
            </Link>
        </div>
    </home-layout>
</template>

<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import HomeLayout from "@/Layouts/HomeLayout.vue";
import ApplicationMark from "@/Base/ApplicationMark.vue";
import AttentionButton from "@/Base/AttentionButton.vue";

defineProps({
    pictures: {
        type: Array,
        required: true,
    },
});

const settings = computed(() => {
    const { props } = usePage();
    return props.settings;
});
</script>
