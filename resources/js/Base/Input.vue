<template>
    <input
        ref="input"
        class="rounded-md border-gray-300 shadow-sm focus:border-hearthlight focus:ring focus:ring-amber-200 focus:ring-opacity-50"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    />
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
    props: {
        modelValue: [String, Number],
    },

    emits: ["update:modelValue"],

    methods: {
        focus() {
            this.$refs.input.focus();
        },
    },
});
</script>
