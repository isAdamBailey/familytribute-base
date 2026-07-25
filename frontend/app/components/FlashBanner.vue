<script setup lang="ts">
const { message, clear } = useFlash()
</script>

<template>
  <div v-if="message" class="bg-hearthlight">
    <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-2">
      <p data-testid="flash-banner" class="text-sm font-semibold text-white">
        {{ message }}
      </p>
      <button
        type="button"
        aria-label="Dismiss"
        class="shrink-0 rounded-md p-1 text-white/90 transition hover:bg-hearthlight-deep hover:text-white"
        @click="clear"
      >
        <i class="ri-close-line" />
      </button>
    </div>
  </div>
</template>
