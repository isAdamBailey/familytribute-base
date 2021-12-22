<template>
    <div :class="full ? '' : 'md:grid md:grid-cols-3 md:gap-6'">
        <jet-section-title>
            <template #title><slot name="title"></slot></template>
            <template #description><slot name="description"></slot></template>
        </jet-section-title>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form @submit.prevent="$emit('submitted')">
                <div
                    class="px-4 py-5 bg-white sm:p-6 shadow"
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
                    class="flex items-center px-4 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md"
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

export default defineComponent({
    components: {
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
