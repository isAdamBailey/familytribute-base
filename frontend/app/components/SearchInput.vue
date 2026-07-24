<script setup lang="ts">
const model = defineModel<string>({ default: '' })

defineProps<{ placeholder?: string }>()
</script>

<template>
  <input
    v-model="model"
    type="search"
    :placeholder="placeholder ?? 'Search…'"
    class="w-40 rounded-full border border-hearthlight/40 bg-white px-4 py-2 text-inkwell shadow-sm focus:border-hearthlight focus:outline-none focus:ring-1 focus:ring-hearthlight sm:w-56 dark:bg-inkwell/60 dark:text-aged-edge"
  >
</template>
