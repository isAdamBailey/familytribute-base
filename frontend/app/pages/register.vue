<script setup lang="ts">
const { register, isLoggedIn, user } = useAuth()
const { processing, errors, submit } = useAuthForm()

if (isLoggedIn.value) {
  await navigateTo('/dashboard')
}

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const registrationSecret = ref('')

useSeoMeta({ title: 'Register' })

async function onSubmit() {
  await submit(async () => {
    await register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
      registration_secret: registrationSecret.value,
    })
    await navigateTo(user.value?.email_verified_at ? '/dashboard' : '/email/verify')
  })
  password.value = ''
  passwordConfirmation.value = ''
}
</script>

<template>
  <AuthCard title="Register">
    <p v-if="errors.registration" class="mt-2 text-sm text-red-600">{{ errors.registration }}</p>

    <form class="mt-6 space-y-4" @submit.prevent="onSubmit">
      <div>
        <label for="name" class="block text-sm font-semibold">Name</label>
        <input id="name" v-model="name" type="text" required autofocus autocomplete="name" class="form-input mt-1">
        <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
      </div>

      <div>
        <label for="email" class="block text-sm font-semibold">Email</label>
        <input id="email" v-model="email" type="email" required class="form-input mt-1">
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

      <div>
        <label for="registration_secret" class="block text-sm font-semibold">Registration Secret</label>
        <input id="registration_secret" v-model="registrationSecret" type="text" required class="form-input mt-1">
        <p v-if="errors.registration_secret" class="mt-1 text-sm text-red-600">{{ errors.registration_secret }}</p>
      </div>

      <div class="flex items-center justify-between pt-2">
        <NuxtLink to="/login" class="text-sm text-faded-ink underline hover:text-hearthlight-deep dark:text-old-binding dark:hover:text-hearthlight">
          Already registered?
        </NuxtLink>
        <button type="submit" data-testid="register-submit" class="btn-primary" :disabled="processing">
          Register
        </button>
      </div>
    </form>
  </AuthCard>
</template>
