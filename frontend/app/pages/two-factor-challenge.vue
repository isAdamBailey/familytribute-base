<script setup lang="ts">
const { twoFactorChallenge } = useAuth()
const { processing, errors, submit } = useAuthForm()

const recovery = ref(false)
const code = ref('')
const recoveryCode = ref('')

useSeoMeta({ title: 'Two-Factor Confirmation' })

function toggleRecovery() {
  recovery.value = !recovery.value
  code.value = ''
  recoveryCode.value = ''
}

async function onSubmit() {
  await submit(async () => {
    await twoFactorChallenge(
      recovery.value ? { recovery_code: recoveryCode.value } : { code: code.value },
    )
    await navigateTo('/dashboard')
  })
}
</script>

<template>
  <AuthCard title="Two-Factor Confirmation">
    <p class="mt-4 text-sm text-faded-ink dark:text-old-binding">
      <template v-if="!recovery">
        Please confirm access to your account by entering the authentication code provided by your
        authenticator application.
      </template>
      <template v-else>
        Please confirm access to your account by entering one of your emergency recovery codes.
      </template>
    </p>

    <form class="mt-6 space-y-4" @submit.prevent="onSubmit">
      <div v-if="!recovery">
        <label for="code" class="block text-sm font-semibold">Code</label>
        <input id="code" v-model="code" type="text" inputmode="numeric" autofocus autocomplete="one-time-code" class="form-input mt-1">
        <p v-if="errors.code" class="mt-1 text-sm text-red-600">{{ errors.code }}</p>
      </div>
      <div v-else>
        <label for="recovery_code" class="block text-sm font-semibold">Recovery Code</label>
        <input id="recovery_code" v-model="recoveryCode" type="text" autocomplete="one-time-code" class="form-input mt-1">
        <p v-if="errors.recovery_code" class="mt-1 text-sm text-red-600">{{ errors.recovery_code }}</p>
      </div>

      <div class="flex items-center justify-between pt-2">
        <button type="button" class="text-sm text-faded-ink underline hover:text-hearthlight-deep dark:text-old-binding dark:hover:text-hearthlight" @click="toggleRecovery">
          {{ recovery ? 'Use an authentication code' : 'Use a recovery code' }}
        </button>
        <button type="submit" data-testid="two-factor-challenge-submit" class="btn-primary" :disabled="processing">
          Log In
        </button>
      </div>
    </form>
  </AuthCard>
</template>
