<template>
    <HomeLayout>
        <!-- Hero -->
        <div class="pb-2 pt-10 sm:pt-14">
            <h1
                class="animate-fade-up font-header text-[clamp(3rem,8vw,5.5rem)] font-bold leading-tight text-hearthlight"
                v-html="settings.title"
            ></h1>

            <div
                class="animate-fade-up my-7 h-px w-14 bg-hearthlight/40"
                style="animation-delay: 80ms"
            ></div>

            <div
                class="animate-fade-up html-content max-w-prose text-lg leading-relaxed text-gray-700 dark:text-amber-300 sm:text-xl"
                style="animation-delay: 140ms"
                v-html="settings.description"
            ></div>

            <div
                class="animate-fade-up mt-10 flex flex-wrap items-center gap-6"
                style="animation-delay: 240ms"
            >
                <Link :href="route('people.index')">
                    <attention-button>
                        <i class="ri-group-fill mr-2"></i>Meet the Family
                    </attention-button>
                </Link>

                <Link
                    :href="route('pictures.index')"
                    class="group inline-flex items-center gap-2 font-semibold text-hearthlight-deep transition-colors hover:text-hearthlight dark:text-amber-400 dark:hover:text-amber-300"
                >
                    View Pictures
                    <span
                        class="inline-block transition-transform duration-200 group-hover:translate-x-1"
                        >→</span
                    >
                </Link>
            </div>
        </div>

        <!-- Photo Mosaic -->
        <section
            v-if="pictures.length > 0"
            class="-mx-2 -mb-20 mt-14 sm:-mx-20"
        >
            <div class="grid grid-cols-2 gap-0.5 lg:grid-cols-4">
                <div
                    v-if="pictures[0]"
                    class="group relative overflow-hidden lg:col-span-2"
                >
                    <img
                        class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.03]"
                        :src="pictures[0].url"
                        :alt="pictures[0].title"
                    />
                    <div
                        v-if="pictures[0].description"
                        class="absolute inset-x-0 bottom-0 line-clamp-1 bg-amber-950/60 px-3 py-2.5 text-sm text-amber-100/90 backdrop-blur-sm"
                        v-html="pictures[0].description"
                    ></div>
                </div>

                <div
                    v-if="pictures[1]"
                    class="group relative overflow-hidden"
                >
                    <img
                        class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.03]"
                        :src="pictures[1].url"
                        :alt="pictures[1].title"
                    />
                    <div
                        v-if="pictures[1].description"
                        class="absolute inset-x-0 bottom-0 line-clamp-1 bg-amber-950/60 px-3 py-2.5 text-sm text-amber-100/90 backdrop-blur-sm"
                        v-html="pictures[1].description"
                    ></div>
                </div>

                <div
                    v-if="pictures[4]"
                    class="group relative overflow-hidden lg:col-start-4 lg:row-span-2 lg:row-start-1"
                >
                    <img
                        class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.03]"
                        :src="pictures[4].url"
                        :alt="pictures[4].title"
                    />
                    <div
                        v-if="pictures[4].description"
                        class="absolute inset-x-0 bottom-0 line-clamp-1 bg-amber-950/60 px-3 py-2.5 text-sm text-amber-100/90 backdrop-blur-sm"
                        v-html="pictures[4].description"
                    ></div>
                </div>

                <div
                    v-if="pictures[2]"
                    class="group relative col-span-2 overflow-hidden lg:col-span-1 lg:row-start-2"
                >
                    <img
                        class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.03]"
                        :src="pictures[2].url"
                        :alt="pictures[2].title"
                    />
                    <div
                        v-if="pictures[2].description"
                        class="absolute inset-x-0 bottom-0 line-clamp-1 bg-amber-950/60 px-3 py-2.5 text-sm text-amber-100/90 backdrop-blur-sm"
                        v-html="pictures[2].description"
                    ></div>
                </div>

                <div
                    v-if="pictures[3]"
                    class="group relative overflow-hidden lg:col-span-2"
                >
                    <img
                        class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.03]"
                        :src="pictures[3].url"
                        :alt="pictures[3].title"
                    />
                    <div
                        v-if="pictures[3].description"
                        class="absolute inset-x-0 bottom-0 line-clamp-1 bg-amber-950/60 px-3 py-2.5 text-sm text-amber-100/90 backdrop-blur-sm"
                        v-html="pictures[3].description"
                    ></div>
                </div>
            </div>
        </section>

        <!-- Empty state -->
        <div v-else class="mt-14 py-20 text-center">
            <div class="text-4xl text-hearthlight/20">✦</div>
            <p class="mt-4 text-base text-gray-400 dark:text-stone-500">
                Family photos will appear here once added.
            </p>
        </div>
    </HomeLayout>
</template>

<script setup>
import AttentionButton from "@/Base/AttentionButton.vue";
import HomeLayout from "@/Layouts/HomeLayout.vue";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

defineProps({
    pictures: {
        type: Array,
        required: true,
    },
});

const settings = computed(() => usePage().props.settings);
</script>

<style scoped>
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-up {
    animation: fadeUp 0.65s cubic-bezier(0.16, 1, 0.3, 1) both;
}

@media (prefers-reduced-motion: reduce) {
    .animate-fade-up {
        animation: none;
    }
}
</style>
