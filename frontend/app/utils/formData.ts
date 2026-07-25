/**
 * Build a multipart FormData body for Laravel API mutations. PHP doesn't
 * parse multipart PUT bodies natively, so updates spoof the method via a
 * `_method` field and POST instead (same convention the old Inertia app used).
 *
 * Booleans are sent as '1'/'0' (Laravel's `boolean` validation rule accepts
 * these, unlike the JS `true`/`false` strings multipart would otherwise send).
 * `null`/`undefined` values are skipped so partial-update endpoints that use
 * `isset()`/truthiness checks don't see an empty string as "field present".
 */
export function toFormData(fields: object, method?: 'PUT' | 'DELETE'): FormData {
  const formData = new FormData()

  if (method) {
    formData.append('_method', method)
  }

  for (const [key, value] of Object.entries(fields as Record<string, unknown>)) {
    if (value === null || value === undefined) continue

    if (typeof value === 'boolean') {
      formData.append(key, value ? '1' : '0')
    } else if (value instanceof File) {
      formData.append(key, value)
    } else if (Array.isArray(value)) {
      // Laravel's `array` validation rule rejects an empty-string field, so an
      // empty selection is sent as nothing (leaves the relation untouched)
      // rather than as an explicit "clear all" — matches the old app's
      // Multiselect, which never posted an empty person_ids field either.
      value.forEach(item => formData.append(`${key}[]`, String(item)))
    } else {
      formData.append(key, String(value))
    }
  }

  return formData
}
