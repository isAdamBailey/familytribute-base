<script setup lang="ts">
const { login, isLoggedIn } = useAuth()
const router = useRouter()
const { processing, errors, submit } = useAuthForm()

if (isLoggedIn.value) {
  await navigateTo('/dashboard')
}

const email = ref('')
const password = ref('')
const remember = ref(false)

useSeoMeta({ title: 'Log In' })

async function onSubmit() {
  await submit(async () => {
    const { twoFactor } = await login({ email: email.value, password: password.value, remember: remember.value })
    if (twoFactor) {
      await router.push('/two-factor-challenge')
      return
    }
    await navigateTo('/dashboard')
  })
  password.value = ''
}
</script>

<template>
  <AuthCard title="Log In">
    <form class="mt-6 space-y-4" @submit.prevent="onSubmit">
      <div>
        <label for="email" class="block text-sm font-semibold">Email</label>
        <input id="email" v-model="email" type="email" required autofocus class="form-input mt-1">
        <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
      </div>

      <div>
        <label for="password" class="block text-sm font-semibold">Password</label>
        <input id="password" v-model="password" type="password" required autocomplete="current-password" class="form-input mt-1">
        <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
      </div>

      <label class="flex items-center gap-2 text-sm">
        <input v-model="remember" type="checkbox">
        Remember me
      </label>

      <div class="flex items-center justify-between pt-2">
        <NuxtLink to="/forgot-password" class="text-sm text-faded-ink underline hover:text-hearthlight-deep dark:text-old-binding dark:hover:text-hearthlight">
          Forgot your password?
        </NuxtLink>
        <button type="submit" data-testid="login-submit" class="btn-primary" :disabled="processing">
          Log In
        </button>
      </div>
    </form>
  </AuthCard>
</template>
