/**
 * The processing/errors/try-catch bookkeeping shared by every auth form page
 * (login, register, forgot-password, reset-password, two-factor-challenge):
 * flip `processing` on, clear `errors`, run the request, and turn a 422 into
 * field errors via `fieldErrors()` on failure.
 */
export function useAuthForm() {
  const processing = ref(false)
  const errors = ref<Record<string, string>>({})

  async function submit(action: () => Promise<unknown>) {
    processing.value = true
    errors.value = {}
    try {
      await action()
    } catch (error) {
      errors.value = fieldErrors(error)
    } finally {
      processing.value = false
    }
  }

  return { processing, errors, submit }
}
