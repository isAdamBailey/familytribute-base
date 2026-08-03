/**
 * TypeScript contract for the Laravel JSON API (app/Http/Resources/*).
 * Auth-only fields are optional here — they are present only when the request
 * is authenticated. Kept in sync with the Resource classes; expanded as later
 * phases consume more of the surface.
 */

export interface User {
  id: number
  name: string
  email: string
  email_verified_at: string | null
  current_team_id?: number | null
  profile_photo_url?: string
  two_factor_enabled?: boolean
}

export interface SiteSettings {
  id: number
  title: string
  description: string
  registration: boolean
}

/**
 * GET /api/site-settings. `google_site_tag` is the GA4 measurement ID from the
 * API's own env (see plugins/gtag.ts) — null outside production, or when the
 * site runs without analytics. It sits beside `settings` rather than inside it
 * because it's env config, not a row in the site_settings table.
 */
export interface SiteSettingsResponse {
  settings: SiteSettings | null
  google_site_tag: string | null
}

/** Featured picture as returned by GET /api/home (raw array, not a Resource). */
export interface HomePicture {
  url: string
  title: string
  description: string
}

/** ParentChildResource — lightweight person reference. */
export interface PersonRef {
  slug: string
  photo_url: string
  full_name: string
}

export interface Obituary {
  id: number
  person_id: number
  birth_date: string
  death_date: string
  content: string
  background_photo_url: string | null
}

export interface Person {
  slug: string
  first_name: string
  full_name: string
  photo_url: string
  obituary?: Obituary
  pictures?: Picture[]
  stories?: Story[]
  parents?: PersonRef[]
  children?: PersonRef[]
  // auth-only
  parent_ids?: number[]
  last_name?: string
}

export interface Picture {
  slug: string
  title: string
  year: number | string
  url: string
  description: string
  people?: Person[]
  // auth-only
  person_ids?: number[]
  featured?: boolean
  private?: boolean
}

export interface Story {
  slug: string
  title: string
  content: string
  year: number | string | null
  excerpt: string
  media_url: string | null
  people?: Person[]
  // auth-only
  person_ids?: number[]
  private?: boolean
}

/** Person::allForTagging() — id + name only, auth-only (empty array for guests). */
export interface TaggingPerson {
  id: number
  full_name: string
}

/** Laravel paginator envelope (lists keep data/links/meta under a named key). */
export interface Paginated<T> {
  data: T[]
  links: {
    first: string | null
    last: string | null
    prev: string | null
    next: string | null
  }
  meta: {
    current_page: number
    from: number | null
    last_page: number
    path: string
    per_page: number
    to: number | null
    total: number
  }
}
