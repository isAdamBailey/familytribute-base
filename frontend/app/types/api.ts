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
}

export interface SiteSettings {
  id: number
  title: string
  description: string
  registration: boolean
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
