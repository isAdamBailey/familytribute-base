<script setup lang="ts">
const { updatePassword } = useAccount()

const currentPassword = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const processing = ref(false)
const recentlySuccessful = ref(false)
const errors = ref<Record<string, string>>({})

async function submit() {
  processing.value = true
  errors.value = {}
  try {
    await updatePassword({
      current_password: currentPassword.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    })
    recentlySuccessful.value = true
    currentPassword.value = ''
    password.value = ''
    passwordConfirmation.value = ''
  } catch (error) {
    errors.value = fieldErrors(error)
    if (errors.value.password) {
      password.value = ''
      passwordConfirmation.value = ''
    }
    if (errors.value.current_password) {
      currentPassword.value = ''
    }
  } finally {
    processing.value = false
  }
}
</script>

<template>
  <form data-testid="update-password-form" class="space-y-4" @submit.prevent="submit">
    <h3 class="font-header text-2xl text-hearthlight-deep dark:text-hearthlight">Update Password</h3>

    <div>
      <label for="current_password" class="block text-sm font-semibold">Current Password</label>
      <input id="current_password" v-model="currentPassword" type="password" class="form-input mt-1" autocomplete="current-password">
      <p v-if="errors.current_password" class="mt-1 text-sm text-red-600">{{ errors.current_password }}</p>
    </div>

    <div>
      <label for="password" class="block text-sm font-semibold">New Password</label>
      <input id="password" v-model="password" type="password" class="form-input mt-1" autocomplete="new-password">
      <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
    </div>

    <div>
      <label for="password_confirmation" class="block text-sm font-semibold">Confirm Password</label>
      <input id="password_confirmation" v-model="passwordConfirmation" type="password" class="form-input mt-1" autocomplete="new-password">
      <p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">{{ errors.password_confirmation }}</p>
    </div>

    <div class="flex items-center gap-3">
      <button type="submit" class="btn-primary" :disabled="processing">Save</button>
      <ActionMessage :show="recentlySuccessful">Saved.</ActionMessage>
    </div>
  </form>
</template>
