<script setup lang="ts">
const { user } = useAuth()
const {
  confirmPassword,
  enableTwoFactorAuthentication,
  disableTwoFactorAuthentication,
  twoFactorQrCode,
  twoFactorRecoveryCodes,
  regenerateRecoveryCodes,
} = useAccount()

const enabled = computed(() => Boolean(user.value?.two_factor_enabled))
const confirmOpen = ref(false)
const pendingAction = ref<'enable' | 'disable' | null>(null)
const qrCodeSvg = ref<string | null>(null)
const recoveryCodes = ref<string[]>([])
const message = ref('')
const modal = ref<{ setError: (message: string) => void } | null>(null)

function startEnable() {
  pendingAction.value = 'enable'
  confirmOpen.value = true
}

function startDisable() {
  pendingAction.value = 'disable'
  confirmOpen.value = true
}

async function confirm(password: string) {
  try {
    await confirmPassword(password)

    if (pendingAction.value === 'enable') {
      await enableTwoFactorAuthentication()
      const [qr, codes] = await Promise.all([twoFactorQrCode(), twoFactorRecoveryCodes()])
      qrCodeSvg.value = qr.svg
      recoveryCodes.value = codes
      message.value = 'You have enabled two factor authentication.'
    } else if (pendingAction.value === 'disable') {
      await disableTwoFactorAuthentication()
      qrCodeSvg.value = null
      recoveryCodes.value = []
      message.value = 'You have not enabled two factor authentication.'
    }

    confirmOpen.value = false
    pendingAction.value = null
  } catch (error) {
    modal.value?.setError(fieldErrors(error).password ?? 'The password is incorrect.')
  }
}

async function refreshRecoveryCodes() {
  await regenerateRecoveryCodes()
  recoveryCodes.value = await twoFactorRecoveryCodes()
}
</script>

<template>
  <div class="space-y-4">
    <h3 class="font-header text-2xl text-hearthlight-deep dark:text-hearthlight">Two Factor Authentication</h3>
    <p class="text-sm text-faded-ink dark:text-old-binding">
      Add additional security to your account using two factor authentication.
    </p>

    <p v-if="message" class="text-sm font-semibold text-hearthlight-deep dark:text-hearthlight">{{ message }}</p>

    <div v-if="qrCodeSvg" class="max-w-xs rounded-md border border-hearthlight-subtle p-4 dark:border-old-binding/30" v-html="qrCodeSvg" />

    <div v-if="recoveryCodes.length" class="rounded-md bg-hearthlight-subtle/40 p-4 text-sm dark:bg-old-binding/10">
      <p class="font-semibold">Recovery Codes</p>
      <ul class="mt-2 space-y-1 font-mono">
        <li v-for="code in recoveryCodes" :key="code">{{ code }}</li>
      </ul>
      <button type="button" class="btn-secondary mt-3" @click="refreshRecoveryCodes">
        Regenerate Recovery Codes
      </button>
    </div>

    <div>
      <button v-if="!enabled" type="button" data-testid="two-factor-enable" class="btn-primary" @click="startEnable">
        Enable
      </button>
      <button v-else type="button" data-testid="two-factor-disable" class="btn-danger" @click="startDisable">
        Disable
      </button>
    </div>

    <ConfirmPasswordModal
      ref="modal"
      :open="confirmOpen"
      title="Confirm Password"
      confirm-text="Confirm"
      @close="confirmOpen = false; pendingAction = null"
      @confirmed="confirm"
    />
  </div>
</template>
