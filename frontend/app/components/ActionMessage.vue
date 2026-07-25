<script setup lang="ts">
const props = defineProps<{ show: boolean }>()

const visible = ref(false)
let timeout: ReturnType<typeof setTimeout> | undefined

watch(() => props.show, (value) => {
  if (!value) return
  visible.value = true
  clearTimeout(timeout)
  timeout = setTimeout(() => { visible.value = false }, 2000)
})
</script>

<template>
  <Transition
    enter-active-class="transition ease-in-out duration-300"
    enter-from-class="opacity-0"
    leave-active-class="transition ease-in-out duration-300"
    leave-to-class="opacity-0"
  >
    <p v-if="visible" class="text-sm font-semibold text-hearthlight-deep dark:text-hearthlight">
      <slot />
    </p>
  </Transition>
</template>
