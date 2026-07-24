/**
 * Turn a failed resource fetch into a 404 page. Used by the Person/Picture/Story
 * show pages, which all 404 the same way when the API call 404s (missing slug,
 * or private content hidden from an unauthenticated request via Laravel's own
 * `abort(404)`). Other failures (500s, network errors, timeouts) are rethrown
 * as-is rather than masked as "not found" — a backend outage should surface as
 * an error, not silently look like missing content.
 */
export function throwIfNotFound(error: unknown, message: string): void {
  if (!error) return

  const statusCode = (error as { statusCode?: number, response?: { status?: number } })?.statusCode
    ?? (error as { response?: { status?: number } })?.response?.status

  if (statusCode === 404) {
    throw createError({ statusCode: 404, statusMessage: message })
  }

  throw error
}
