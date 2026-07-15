import { test as setup } from "../../setup";
import { PluginManagementPage } from "../../pages/01_pluginManagement";

/**
 * Install every plugin once, before the parallel phase starts.
 */
setup("install all plugins", async ({ adminPage }) => {
    setup.setTimeout(15 * 60 * 1000);

    const pluginManagementPage = new PluginManagementPage(adminPage);

    await pluginManagementPage.gotoPluginManagementPage();
    await pluginManagementPage.installAllPlugins();
});
