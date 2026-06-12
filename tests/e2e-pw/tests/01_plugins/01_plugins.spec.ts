import { test } from "../../setup";
import { PluginManagementPage } from "../../pages/01_pluginManagement";

const ALL_PLUGINS_TIMEOUT = 60 * 60 * 1000;

test.describe('Plugin Management', () => {

    test.beforeEach(async ({ adminPage }) => {
        const pluginManagementPage = new PluginManagementPage(adminPage);
        await pluginManagementPage.gotoPluginManagementPage();
    });

    /**
     * All plugins installation test
     */
    test('All Plugins Installation Test', async ({ adminPage }) => {
        test.setTimeout(ALL_PLUGINS_TIMEOUT);

        const pluginManagementPage = new PluginManagementPage(adminPage);
        await pluginManagementPage.installAllPlugins();
    });

    /**
     * All plugins uninstallation test
     */
    test('All Plugins Uninstallation Test', async ({ adminPage }) => {
        test.setTimeout(ALL_PLUGINS_TIMEOUT);

        const pluginManagementPage = new PluginManagementPage(adminPage);
        await pluginManagementPage.uninstallAllPlugins();
    });
});
