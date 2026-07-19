export const SEED = {
  siteTitle: 'Family Tribute',
  registrationSecret: 'familytribute',
  users: {
    admin: { email: 'test@test.com', password: 'password', name: 'Test Admin' },
    editor: { email: 'test2@test.com', password: 'password', name: 'Test Editor' },
  },
  people: {
    ada: { name: 'Ada Lovelace', path: '/ada-lovelace' },
    alan: { name: 'Alan Turing', path: '/alan-turing' },
  },
  pictures: {
    public: { title: 'Public Picnic', path: '/pictures/public-picnic' },
    private: { title: 'Private Portrait', path: '/pictures/private-portrait' },
  },
  stories: {
    public: { title: 'Public Garden Story', path: '/stories/public-garden-story' },
    private: { title: 'Private Letter', path: '/stories/private-letter' },
  },
} as const;

export const FIXTURES = {
  photo: 'e2e/fixtures/photo.jpg',
  audio: 'e2e/fixtures/story.wav',
} as const;
