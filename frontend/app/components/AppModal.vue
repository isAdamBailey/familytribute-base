<script setup lang="ts">
defineProps<{ open: boolean, title: string }>()
const emit = defineEmits<{ close: [] }>()
</script>

<template>
  <Teleport to="body">
    <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
      <div class="fixed inset-0 z-0 bg-inkwell/50" @click="emit('close')" />
      <div class="relative z-10 max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-lg bg-white p-6 shadow-card dark:bg-inkwell">
        <h2 class="font-header text-2xl text-hearthlight-deep dark:text-hearthlight">
          {{ title }}
        </h2>
        <div class="mt-4">
          <slot />
        </div>
        <div class="mt-6 flex justify-end gap-2">
          <slot name="footer" />
        </div>
      </div>
    </div>
  </Teleport>
</template>
