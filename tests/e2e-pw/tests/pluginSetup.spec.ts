import { test, expect } from '../setup';

test.describe('Plugin Installation', () => {
    let j = 0;

    test.beforeEach(async ({ adminPage }) => {

        await adminPage.goto('/admin/plugins');
        await expect(adminPage).toHaveURL(/.*admin/);
        await expect(adminPage.locator('text=Sync Available Plugins')).toBeVisible();
    });

    /** 
     * All plugins installation test
     */
    test('All Plugins Installation Test', async ({ adminPage }) => {
        test.setTimeout(400000);
        for (let i = 0; i < 13; i++) {

            const title = adminPage.locator('.fi-size-lg.fi-font-semibold.fi-ta-text-item.fi-ta-text.fi-inline');
            await adminPage.locator('button[title="Actions"]').nth(i).click();
            const checkInstalled = await adminPage.locator('button.fi-color.fi-color-danger.fi-dropdown-list-item').nth(i).isVisible();

            if (!checkInstalled) {
                await adminPage.waitForLoadState('networkidle');
                await adminPage.locator('button.fi-color.fi-color-success.fi-text-color-700').nth(j).click();
                await adminPage.waitForTimeout(3000); // Wait for 3 seconds to allow installation to complete
                await adminPage.locator('span[x-show="! isProcessing"]').click();
                const pluginTitle = await title.nth(i).innerText();
                console.log(`Installing Plugin: ${pluginTitle}`);
                await expect(adminPage.getByRole('heading', { name: 'Plugin Installed Successfully' })).toBeVisible();
            }
        }
    });

    /** 
     * All plugins installation test
     */
    test('All Plugins Uninstallation Test', async ({ adminPage }) => {
        for (let i = 0; i < 13; i++) {

            const title = adminPage.locator('.fi-size-lg.fi-font-semibold.fi-ta-text-item.fi-ta-text.fi-inline');
            await adminPage.locator('button[title="Actions"]').nth(i).click();
            const checkInstalled = await adminPage.locator('button.fi-color.fi-color-danger.fi-dropdown-list-item').nth(j).isVisible();

            if (checkInstalled) {
                await adminPage.waitForLoadState('networkidle');
                await adminPage.waitForTimeout(2000);
                await adminPage.locator('button.fi-color.fi-color-danger.fi-dropdown-list-item').nth(j).click();
                await adminPage.waitForTimeout(5000);
                await adminPage.locator('span[x-show="! isProcessing"]').click();
                // await expect(adminPage.getByText('Plugin Uninstalled Successfully')).toBeVisible();
                const pluginTitle = await title.nth(i).innerText();
                console.log(`Uninstalling Plugin: ${pluginTitle}`);
                await expect(adminPage.getByRole('heading', { name: 'Plugin Uninstalled Successfully' })).toBeVisible();
            }
        }
    });

    /** 
     * Plugins installation for testing purposes
     */
    // test('Plugins Installation for testing purposes', async ({ adminPage }) => {
    //     for (let i = 0; i < 13; i++) {

    //         await adminPage.locator('button[title="Actions"]').nth(i).click();
    //         const checkInstalled = await adminPage.locator('button.fi-color.fi-color-danger.fi-dropdown-list-item').nth(i).isVisible();

    //         if (!checkInstalled) {
    //             await adminPage.waitForLoadState('networkidle');
    //             await adminPage.locator('button.fi-color.fi-color-success.fi-text-color-700').nth(j).click();
    //             await adminPage.waitForTimeout(5000); // Wait for 5 seconds to allow installation to complete
    //             await adminPage.locator('span[x-show="! isProcessing"]').click();
    //             // await expect(adminPage.getByText('Plugin Installed Successfully')).toBeVisible();
    //             await expect(adminPage.getByRole('heading', { name: 'Plugin Installed Successfully' })).toBeVisible();
    //         }
    //     }
    // });
});
