<script setup lang="ts">
const { sessions: fetchSessions, logoutOtherBrowserSessions } = useAccount()

const sessionsData = await fetchSessions()
const sessions = computed(() => sessionsData.sessions ?? [])

const confirmOpen = ref(false)
const done = ref(false)
const modal = ref<{ setError: (message: string) => void } | null>(null)

async function confirm(password: string) {
  try {
    await logoutOtherBrowserSessions(password)
    confirmOpen.value = false
    done.value = true
  } catch (error) {
    modal.value?.setError(fieldErrors(error).password ?? 'The password is incorrect.')
  }
}
</script>

<template>
  <div class="space-y-4">
    <h3 class="font-header text-2xl text-hearthlight-deep dark:text-hearthlight">Browser Sessions</h3>
    <p class="text-sm text-faded-ink dark:text-old-binding">
      If necessary, you may log out of all of your other browser sessions across all of your
      devices.
    </p>

    <ul v-if="sessions.length" class="space-y-3">
      <li v-for="(session, index) in sessions" :key="index" class="flex items-center gap-3 text-sm">
        <i :class="session.agent.is_desktop ? 'ri-computer-line' : 'ri-smartphone-line'" class="text-lg text-faded-ink dark:text-old-binding" />
        <div>
          {{ session.agent.platform ?? 'Unknown' }} - {{ session.agent.browser ?? 'Unknown' }}
          <span v-if="session.is_current_device" class="font-semibold text-hearthlight-deep dark:text-hearthlight">This device</span>
          <span v-else class="text-faded-ink dark:text-old-binding">Last active {{ session.last_active }}</span>
        </div>
      </li>
    </ul>

    <div class="flex items-center gap-3">
      <button type="button" class="btn-secondary" @click="confirmOpen = true">
        Log Out Other Browser Sessions
      </button>
      <ActionMessage :show="done">Done.</ActionMessage>
    </div>

    <ConfirmPasswordModal
      ref="modal"
      :open="confirmOpen"
      title="Log Out Other Browser Sessions"
      confirm-text="Log Out Other Browser Sessions"
      @close="confirmOpen = false"
      @confirmed="confirm"
    />
  </div>
</template>
