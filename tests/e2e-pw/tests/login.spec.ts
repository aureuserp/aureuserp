import { test, expect } from '../setup';

test('admin can login', async ({ adminPage }) => {
    // The adminPage fixture already logs in as admin
    await expect(adminPage).toHaveURL(/.*admin/); 
    await expect(adminPage.locator('text=Sync Available Plugins')).toBeVisible();
});