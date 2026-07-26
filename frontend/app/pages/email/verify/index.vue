<script setup lang="ts">
const { user, isLoggedIn, resendVerificationEmail, logout } = useAuth()

if (!isLoggedIn.value) {
  await navigateTo('/login')
} else if (user.value?.email_verified_at) {
  await navigateTo('/dashboard')
}

const processing = ref(false)
const sent = ref(false)

useSeoMeta({ title: 'Email Verification' })

async function submit() {
  processing.value = true
  try {
    await resendVerificationEmail()
    sent.value = true
  } finally {
    processing.value = false
  }
}

async function handleLogout() {
  await logout()
  await navigateTo('/')
}
</script>

<template>
  <AuthCard title="Email Verification">
    <p class="mt-4 text-sm text-faded-ink dark:text-old-binding">
      Thanks for signing up! Before getting started, could you verify your email address by clicking
      on the link we just emailed to you? If you didn't receive the email, we will gladly send you
      another.
    </p>

    <p v-if="sent" data-testid="verification-link-sent" class="mt-4 text-sm font-medium text-green-600">
      A new verification link has been sent to the email address you provided during registration.
    </p>

    <div class="mt-6 flex items-center justify-between">
      <button type="button" data-testid="resend-verification-submit" class="btn-primary" :disabled="processing" @click="submit">
        Resend Verification Email
      </button>
      <button type="button" class="text-sm text-faded-ink underline hover:text-hearthlight-deep dark:text-old-binding dark:hover:text-hearthlight" @click="handleLogout">
        Log Out
      </button>
    </div>
  </AuthCard>
</template>
