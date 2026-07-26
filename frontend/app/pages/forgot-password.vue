<script setup lang="ts">
const { forgotPassword } = useAuth()
const { processing, errors, submit } = useAuthForm()

const email = ref('')
const status = ref<string | null>(null)

useSeoMeta({ title: 'Forgot Password' })

async function onSubmit() {
  status.value = null
  await submit(async () => {
    const response = await forgotPassword(email.value)
    status.value = response?.status ?? 'We have emailed your password reset link!'
  })
}
</script>

<template>
  <AuthCard title="Forgot Password">
    <p class="mt-4 text-sm text-faded-ink dark:text-old-binding">
      Forgot your password? No problem. Just let us know your email address and we will email you a
      password reset link that will allow you to choose a new one.
    </p>

    <p v-if="status" class="mt-4 text-sm font-medium text-green-600">{{ status }}</p>

    <form class="mt-6 space-y-4" @submit.prevent="onSubmit">
      <div>
        <label for="email" class="block text-sm font-semibold">Email</label>
        <input id="email" v-model="email" type="email" required autofocus class="form-input mt-1">
        <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
      </div>

      <div class="flex items-center justify-end pt-2">
        <button type="submit" data-testid="forgot-password-submit" class="btn-primary" :disabled="processing">
          Email Password Reset Link
        </button>
      </div>
    </form>
  </AuthCard>
</template>
