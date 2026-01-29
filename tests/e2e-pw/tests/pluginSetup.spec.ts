import { test, expect } from '../setup';
import { PluginManagementPage } from '../pages/pluginmanagement';

test.describe('Plugin Installation', () => {

    test.beforeEach(async ({ adminPage }) => {
        const pluginManagementPage = new PluginManagementPage(adminPage);
        await pluginManagementPage.gotoPluginManagementPage();
    });

    /**
     * All plugins installation test
     */
    test('All Plugins Installation Test', async ({ adminPage }) => {
        test.setTimeout(400000);
        const pluginManagementPage = new PluginManagementPage(adminPage);
        await pluginManagementPage.installPlugin();
    });

    /**
     * All plugins uninstallation test
     */
    test('All Plugins Uninstallation Test', async ({  adminPage }) => {
        const pluginManagementPage = new PluginManagementPage(adminPage);
        await pluginManagementPage.uninstallPlugin();
    });
});