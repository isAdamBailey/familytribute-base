<script setup lang="ts">
import type { Story } from '~/types/api'

const props = defineProps<{ open: boolean, story: Story }>()
const emit = defineEmits<{ close: [], deleted: [] }>()

const { deleteStory } = useStoryMutations()
const { flash } = useFlash()
const processing = ref(false)

async function confirmDelete() {
  processing.value = true
  try {
    await deleteStory(props.story.slug)
    flash('Story successfully deleted!')
    emit('deleted')
  } finally {
    processing.value = false
  }
}
</script>

<template>
  <AppModal :open="open" title="Delete Story" @close="emit('close')">
    <p class="text-sm text-faded-ink dark:text-old-binding">
      Are you sure you want to delete this story? Once the story is deleted, all of its
      connections to people will also be permanently deleted.
    </p>

    <template #footer>
      <button type="button" class="btn-secondary" @click="emit('close')">Nevermind</button>
      <button
        type="button"
        data-testid="confirm-delete-story"
        class="btn-danger"
        :disabled="processing"
        @click="confirmDelete"
      >
        Delete Story
      </button>
    </template>
  </AppModal>

</template>
