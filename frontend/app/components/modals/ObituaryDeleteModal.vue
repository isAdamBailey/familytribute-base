<script setup lang="ts">
import type { Person } from '~/types/api'

const props = defineProps<{ open: boolean, person: Person }>()
const emit = defineEmits<{ close: [], deleted: [] }>()

const { deleteObituary } = useObituaryMutations()
const { flash } = useFlash()
const processing = ref(false)

async function confirmDelete() {
  if (!props.person.obituary) return
  processing.value = true
  try {
    await deleteObituary(props.person.obituary.id)
    flash('Obituary successfully deleted!')
    emit('deleted')
  } finally {
    processing.value = false
  }
}
</script>

<template>
  <AppModal :open="open" title="Delete Person" @close="emit('close')">
    <p class="text-sm text-faded-ink dark:text-old-binding">
      Are you sure you want to delete {{ person.full_name }}? Once deleted, all of their pictures,
      stories, and family connections will also be permanently removed.
    </p>

    <template #footer>
      <button type="button" class="btn-secondary" @click="emit('close')">Nevermind</button>
      <button
        type="button"
        data-testid="confirm-delete-person"
        class="btn-danger"
        :disabled="processing"
        @click="confirmDelete"
      >
        Delete Person
      </button>
    </template>
  </AppModal>

</template>
