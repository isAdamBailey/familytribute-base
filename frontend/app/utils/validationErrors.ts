/** Laravel's 422 ValidationException shape: { message, errors: { field: string[] } }. */
export function fieldErrors(error: unknown): Record<string, string> {
  const data = (error as { data?: { errors?: Record<string, string[]> } })?.data
  if (!data?.errors) return {}

  return Object.fromEntries(
    Object.entries(data.errors).map(([field, messages]) => [field, messages[0] ?? '']),
  )
}
