<script setup lang="ts">
const { user } = useAuth()
const { updateProfileInformation } = useAccount()

const name = ref(user.value?.name ?? '')
const email = ref(user.value?.email ?? '')
const processing = ref(false)
const recentlySuccessful = ref(false)
const errors = ref<Record<string, string>>({})

async function submit() {
  processing.value = true
  errors.value = {}
  try {
    await updateProfileInformation({ name: name.value, email: email.value })
    recentlySuccessful.value = true
  } catch (error) {
    errors.value = fieldErrors(error)
  } finally {
    processing.value = false
  }
}
</script>

<template>
  <form data-testid="profile-information-form" class="space-y-4" @submit.prevent="submit">
    <h3 class="font-header text-2xl text-hearthlight-deep dark:text-hearthlight">Profile Information</h3>

    <div>
      <label for="name" class="block text-sm font-semibold">Name</label>
      <input id="name" v-model="name" type="text" class="form-input mt-1">
      <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
    </div>

    <div>
      <label for="email" class="block text-sm font-semibold">Email</label>
      <input id="email" v-model="email" type="email" class="form-input mt-1">
      <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
    </div>

    <div class="flex items-center gap-3">
      <button type="submit" class="btn-primary" :disabled="processing">Save</button>
      <ActionMessage :show="recentlySuccessful">Saved.</ActionMessage>
    </div>
  </form>
</template>
