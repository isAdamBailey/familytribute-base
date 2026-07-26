<script setup lang="ts">
const route = useRoute()
const { verifyEmail, fetchUser } = useAuth()

const error = ref<string | null>(null)

useSeoMeta({ title: 'Email Verification' })

try {
  const { expires, signature } = route.query
  await verifyEmail(String(route.params.id), String(route.params.hash), {
    ...(typeof expires === 'string' && { expires }),
    ...(typeof signature === 'string' && { signature }),
  })
  await fetchUser()
  await navigateTo('/dashboard')
} catch {
  error.value = 'This verification link is invalid or has expired.'
}
</script>

<template>
  <AuthCard title="Email Verification">
    <p v-if="error" class="mt-4 text-sm text-red-600">{{ error }}</p>
    <p v-else class="mt-4 text-sm text-faded-ink dark:text-old-binding">
      Verifying your email address...
    </p>
  </AuthCard>
</template>
