<script setup lang="ts">
const route = useRoute()
const { resetPassword } = useAuth()
const { processing, errors, submit } = useAuthForm()

const token = String(route.params.token)
const email = ref(String(route.query.email ?? ''))
const password = ref('')
const passwordConfirmation = ref('')

useSeoMeta({ title: 'Reset Password' })

async function onSubmit() {
  await submit(async () => {
    await resetPassword({
      token,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    })
    await navigateTo('/login')
  })
  password.value = ''
  passwordConfirmation.value = ''
}
</script>

<template>
  <AuthCard title="Reset Password">
    <form class="mt-6 space-y-4" @submit.prevent="onSubmit">
      <div>
        <label for="email" class="block text-sm font-semibold">Email</label>
        <input id="email" v-model="email" type="email" required autofocus class="form-input mt-1">
        <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
      </div>

      <div>
        <label for="password" class="block text-sm font-semibold">Password</label>
        <input id="password" v-model="password" type="password" required autocomplete="new-password" class="form-input mt-1">
        <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
      </div>

      <div>
        <label for="password_confirmation" class="block text-sm font-semibold">Confirm Password</label>
        <input id="password_confirmation" v-model="passwordConfirmation" type="password" required autocomplete="new-password" class="form-input mt-1">
      </div>

      <div class="flex items-center justify-end pt-2">
        <button type="submit" data-testid="reset-password-submit" class="btn-primary" :disabled="processing">
          Reset Password
        </button>
      </div>
    </form>
  </AuthCard>
</template>
