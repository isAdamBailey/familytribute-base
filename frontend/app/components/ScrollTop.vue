<script setup lang="ts">
const { y } = useWindowScroll()
const visible = computed(() => y.value > 0)

function scrollToTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>

<template>
  <div
    class="sticky bottom-0 flex w-full justify-end pb-3 pr-5 transition lg:pr-16"
    :class="visible ? 'visible' : 'invisible'"
  >
    <button
      type="button"
      aria-label="Scroll to top of the page"
      class="rounded-full text-faded-ink transition hover:text-hearthlight-deep dark:text-aged-edge dark:hover:text-hearthlight"
      @click="scrollToTop"
    >
      <i class="ri-arrow-up-circle-line text-5xl" aria-hidden="true" />
    </button>
  </div>
</template>
