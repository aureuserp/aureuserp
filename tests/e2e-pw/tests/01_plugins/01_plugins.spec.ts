import { test, expect } from "../../setup";
import { PluginManagementPage } from "../../pages/01_pluginManagement";

/**
 * Installing and uninstalling plugins rewrites the schema of the whole application, so these
 * two run on their own — in file order, in the "plugins" project, before any other test
 * starts. Uninstalling comes first so the suite is handed a fully installed application:
 * every other describe then only has to *read* that a plugin is installed, which is safe to
 * do from several workers at once. (Installing the same plugin from four workers in parallel
 * would run four `php artisan <plugin>:install` migrations against one database.)
 */
test.describe.configure({ mode: 'serial' });

test.describe('Plugin Installation', () => {

    test.beforeEach(async ({ adminPage }) => {
        const pluginManagementPage = new PluginManagementPage(adminPage);
        await pluginManagementPage.gotoPluginManagementPage();
    });

    /**
     * All plugins uninstallation test
     */
    test('All Plugins Uninstallation Test', async ({  adminPage }) => {
        const pluginManagementPage = new PluginManagementPage(adminPage);
        await pluginManagementPage.uninstallAllPlugins();
    });

    /**
     * All plugins installation test
     */
    test('All Plugins Installation Test', async ({ adminPage }) => {
        // test.setTimeout(400000);
        const pluginManagementPage = new PluginManagementPage(adminPage);
        await pluginManagementPage.installAllPlugins();
    });
});
