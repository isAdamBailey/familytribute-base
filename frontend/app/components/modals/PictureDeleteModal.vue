<script setup lang="ts">
import type { Picture } from '~/types/api'

const props = defineProps<{ open: boolean, picture: Picture }>()
const emit = defineEmits<{ close: [], deleted: [] }>()

const { deletePicture } = usePictureMutations()
const { flash } = useFlash()
const processing = ref(false)

async function confirmDelete() {
  processing.value = true
  try {
    await deletePicture(props.picture.slug)
    flash('Picture successfully deleted!')
    emit('deleted')
  } finally {
    processing.value = false
  }
}
</script>

<template>
  <AppModal :open="open" title="Delete Picture" @close="emit('close')">
    <p class="text-sm text-faded-ink dark:text-old-binding">
      Are you sure you want to delete this picture? Once the picture is deleted, all of its
      connections to people will also be permanently deleted.
    </p>

    <template #footer>
      <button type="button" class="btn-secondary" @click="emit('close')">Nevermind</button>
      <button
        type="button"
        data-testid="confirm-delete-picture"
        class="btn-danger"
        :disabled="processing"
        @click="confirmDelete"
      >
        Delete Picture
      </button>
    </template>
  </AppModal>

</template>
