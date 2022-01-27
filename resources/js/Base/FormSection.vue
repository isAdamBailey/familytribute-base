<template>
    <div :class="full ? '' : 'md:grid md:grid-cols-3 md:gap-6'">
        <jet-section-title>
            <template #title><slot name="title"></slot></template>
            <template #description
                ><info-text><slot name="description"></slot></info-text
            ></template>
        </jet-section-title>

        <div class="mt-5 md:col-span-2 md:mt-0">
            <form @submit.prevent="$emit('submitted')">
                <div
                    class="bg-white px-4 py-5 shadow dark:bg-indigo-300 sm:p-6"
                    :class="
                        hasActions
                            ? 'sm:rounded-tl-md sm:rounded-tr-md'
                            : 'sm:rounded-md'
                    "
                >
                    <div :class="full ? 'grid gap-6' : 'grid-cols-6'">
                        <slot name="form"></slot>
                    </div>
                </div>

                <div
                    v-if="hasActions"
                    class="flex items-center bg-gray-50 px-4 py-3 text-right shadow sm:rounded-bl-md sm:rounded-br-md sm:px-6"
                    :class="full ? 'justify-start' : 'justify-end'"
                >
                    <slot name="actions"></slot>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import JetSectionTitle from "./SectionTitle.vue";
import InfoText from "./InfoText";

export default defineComponent({
    components: {
        InfoText,
        JetSectionTitle,
    },
    props: {
        full: {
            type: Boolean,
            default: false,
        },
    },

    emits: ["submitted"],

    computed: {
        hasActions() {
            return !!this.$slots.actions;
        },
    },
});
</script>
