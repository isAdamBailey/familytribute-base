/**
 * Format a date-only string (e.g. "1990-01-01" from Obituary birth_date/death_date)
 * as "Month D, YYYY". Formats in UTC — these fields represent a calendar date, not
 * an instant, so parsing/formatting in the viewer's local timezone would shift the
 * displayed day whenever that timezone has a negative UTC offset.
 */
export function formatDate(date: string | null | undefined): string {
  if (!date) return ''
  const parsed = new Date(date)
  if (Number.isNaN(parsed.getTime())) return date
  return new Intl.DateTimeFormat('en-US', { year: 'numeric', month: 'long', day: 'numeric', timeZone: 'UTC' }).format(parsed)
}
