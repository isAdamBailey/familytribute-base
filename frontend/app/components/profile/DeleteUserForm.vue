<script setup lang="ts">
const { deleteAccount } = useAccount()
const { user } = useAuth()

const confirmOpen = ref(false)
const modal = ref<{ setError: (message: string) => void } | null>(null)

async function confirm(password: string) {
  try {
    await deleteAccount(password)
    user.value = null
    await navigateTo('/')
  } catch (error) {
    modal.value?.setError(fieldErrors(error).password ?? 'The password is incorrect.')
  }
}
</script>

<template>
  <div class="space-y-4">
    <h3 class="font-header text-2xl text-hearthlight-deep dark:text-hearthlight">Delete Account</h3>
    <p class="text-sm text-faded-ink dark:text-old-binding">
      Once your account is deleted, all of its resources and data will be permanently deleted.
      Before deleting your account, please download any data or information that you wish to
      retain.
    </p>

    <button type="button" class="btn-danger" @click="confirmOpen = true">Delete Account</button>

    <ConfirmPasswordModal
      ref="modal"
      :open="confirmOpen"
      title="Delete Account"
      confirm-text="Delete Account"
      @close="confirmOpen = false"
      @confirmed="confirm"
    />
  </div>
</template>
