import path from 'node:path';
import { test, expect } from '@playwright/test';
import { FIXTURES, SEED } from '../constants';
import { expectFlash, fillDate, fillWysiwyg, loginAs, logout } from '../helpers/auth';

const photo = path.resolve(FIXTURES.photo);
const audio = path.resolve(FIXTURES.audio);

test.describe('CRUD', () => {
  test.beforeEach(async ({ page }) => {
    await loginAs(page);
  });

  test('create, edit, and delete a person/obituary', async ({ page }) => {
    const first = `E2E${Date.now()}`;
    const last = 'Person';
    const fullName = `${first} ${last}`;
    const slug = `${first.toLowerCase()}-person`;

    await page.goto('/dashboard');
    const form = page.getByTestId('person-create-form');
    await form.locator('#first_name').fill(first);
    await form.locator('#last_name').fill(last);
    await fillDate(page, 'person-birth-date', '1901-01-15');
    await fillDate(page, 'person-death-date', '1980-06-01');
    await fillWysiwyg(page, 'person-obituary', 'An e2e obituary for a memorable life.');
    await form.getByTestId('person-photo-input').setInputFiles(photo);
    await form.getByTestId('person-create-submit').click();
    await expectFlash(page, 'Obituary successfully created!');

    await page.goto(`/${slug}`);
    await expect(page.getByRole('heading', { name: fullName })).toBeVisible();

    await page.getByRole('button', { name: 'Edit Person' }).click();
    await page.locator('#first_name').fill(`${first} Updated`);
    const editEditor = page.locator('.ProseMirror').last();
    await editEditor.click();
    await page.keyboard.press(process.platform === 'darwin' ? 'Meta+A' : 'Control+A');
    await page.keyboard.insertText('Updated e2e obituary content.');
    await page.getByRole('button', { name: 'Update Obituary' }).click();
    await expectFlash(page, 'Obituary successfully updated!');
    await expect(page.getByRole('heading', { name: `${first} Updated ${last}` })).toBeVisible();

    await page.getByLabel('Delete Person').click();
    await page.getByTestId('confirm-delete-person').click();
    await expectFlash(page, 'Obituary successfully deleted!');
    await expect(page).toHaveURL('/people');
    await expect(page.getByRole('link', { name: `${first} Updated ${last}` })).toHaveCount(0);
  });

  test('create, edit, and delete a picture', async ({ page }) => {
    const title = `E2E Picnic ${Date.now()}`;
    const slug = title.toLowerCase().replace(/\s+/g, '-');

    await page.goto('/dashboard');
    const form = page.getByTestId('picture-create-form');
    await form.getByTestId('picture-photo-input').setInputFiles(photo);
    await form.locator('#title').fill(title);
    await fillWysiwyg(page, 'picture-description', 'An e2e picture description.');
    await form.locator('#year').fill('1977');
    await form.getByTestId('picture-create-submit').click();
    await expectFlash(page, 'Picture successfully created!');

    await page.goto(`/pictures/${slug}`);
    await expect(page.getByRole('heading', { name: title })).toBeVisible();

    await page.getByRole('button', { name: 'Edit Picture' }).click();
    await page.locator('#title').fill(`${title} Edited`);
    await page.getByRole('button', { name: 'Update Picture' }).click();
    await expectFlash(page, 'Picture successfully updated!');

    await page.getByLabel('Delete Picture').click();
    await page.getByTestId('confirm-delete-picture').click();
    await expectFlash(page, 'Picture successfully deleted!');
    await expect(page).toHaveURL('/pictures');
  });

  test('create, edit, and delete a story with media', async ({ page }) => {
    const title = `E2E Story ${Date.now()}`;
    const slug = title.toLowerCase().replace(/\s+/g, '-');

    await page.goto('/dashboard');
    const form = page.getByTestId('story-create-form');
    await form.locator('#title').fill(title);
    await fillWysiwyg(page, 'story-excerpt', 'Short e2e excerpt.');
    await fillWysiwyg(page, 'story-content', 'Full e2e story content.');
    await form.locator('#year').fill('1988');
    await form.getByTestId('story-media-input').setInputFiles(audio);
    await form.getByTestId('story-create-submit').click();
    await expectFlash(page, 'Story successfully created!');

    await page.goto(`/stories/${slug}`);
    await expect(page.getByRole('heading', { name: title })).toBeVisible();

    await page.getByRole('button', { name: 'Edit Story' }).click();
    await page.locator('#title').fill(`${title} Edited`);
    await page.getByRole('button', { name: 'Update Story' }).click();
    await expectFlash(page, 'Story successfully updated!');

    await page.getByLabel('Delete Story').click();
    await page.getByTestId('confirm-delete-story').click();
    await expectFlash(page, 'Story successfully deleted!');
    await expect(page).toHaveURL('/stories');
  });

  test('update site settings and registration gate', async ({ page }) => {
    await page.goto('/dashboard');
    const form = page.getByTestId('site-settings-form');

    try {
      await form.locator('#title').fill('');
      await form.locator('#title').fill('Family Tribute E2E');
      await expect(form.getByTestId('site-settings-submit')).toBeEnabled();
      await form.getByTestId('site-settings-submit').click();
      await expectFlash(page, 'Settings successfully updated!');
      await expect(page.getByText('Family Tribute E2E').first()).toBeVisible();

      await form.locator('#registration').uncheck();
      await expect(form.getByTestId('site-settings-submit')).toBeEnabled();
      await form.getByTestId('site-settings-submit').click();
      await expectFlash(page, 'Settings successfully updated!');

      await logout(page);
      await expect(page.getByRole('link', { name: 'Register' })).toHaveCount(0);
      await page.goto('/register');
      await page.locator('#name').fill('Should Fail');
      await page.locator('#email').fill(`blocked-${Date.now()}@example.com`);
      await page.locator('#password').fill('password');
      await page.locator('#password_confirmation').fill('password');
      await page.locator('#registration_secret').fill(SEED.registrationSecret);
      await page.getByTestId('register-submit').click();
      await expect(page.getByText(/Registration is not enabled/i)).toBeVisible();
    } finally {
      await loginAs(page);
      await page.goto('/dashboard');
      const restore = page.getByTestId('site-settings-form');
      await restore.locator('#title').fill('');
      await restore.locator('#title').fill(SEED.siteTitle);
      await restore.locator('#registration').check();
      await restore.locator('#registration_secret').fill(SEED.registrationSecret);
      await expect(restore.getByTestId('site-settings-submit')).toBeEnabled();
      await restore.getByTestId('site-settings-submit').click();
      await expectFlash(page, 'Settings successfully updated!');
    }
  });
});
