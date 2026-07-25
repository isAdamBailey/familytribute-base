<script setup lang="ts">
defineProps<{ open: boolean, title: string, confirmText: string }>()
const emit = defineEmits<{ close: [], confirmed: [password: string] }>()

const password = ref('')
const error = ref('')

function submit() {
  error.value = ''
  emit('confirmed', password.value)
}

function close() {
  password.value = ''
  error.value = ''
  emit('close')
}

defineExpose({ setError: (message: string) => { error.value = message } })
</script>

<template>
  <AppModal :open="open" :title="title" @close="close">
    <label for="confirm-password" class="block text-sm font-semibold">Password</label>
    <input
      id="confirm-password"
      v-model="password"
      type="password"
      placeholder="Password"
      class="form-input mt-1"
      @keyup.enter="submit"
    >
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>

    <template #footer>
      <button type="button" class="btn-secondary" @click="close">Nevermind</button>
      <button type="button" data-testid="confirm-password-submit" class="btn-primary" @click="submit">
        {{ confirmText }}
      </button>
    </template>
  </AppModal>

</template>
