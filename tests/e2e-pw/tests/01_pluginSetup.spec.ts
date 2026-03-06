import { test, expect } from '../setup';
import { PluginManagementPage } from '../pages/01_pluginManagement';

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
        await pluginManagementPage.installAllPlugins();
    });

    /**
     * All plugins uninstallation test
     */
    test('All Plugins Uninstallation Test', async ({  adminPage }) => {
        const pluginManagementPage = new PluginManagementPage(adminPage);
        await pluginManagementPage.uninstallAllPlugins();
    });

    /**
     * Accounting plugin installation test
     */
    // test('Accounting Plugin Installation Test', async ({ adminPage }) => {
    //     const pluginManagementPage = new PluginManagementPage(adminPage);
    //     await pluginManagementPage.AccountingInstall();
    // });
});