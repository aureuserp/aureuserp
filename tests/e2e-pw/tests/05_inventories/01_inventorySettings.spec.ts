import { test } from "../../setup";
import { InventoriesManagementPage } from "../../pages/06_inventoriesManagement";

test.describe("Inventory Settings - Toggles", () => {
    test.beforeAll(async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.ensureBaseDependentPluginsInstalled();
    });

    test("Packages", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoManageOperationsPage();
        await inventoryPage.enableManageOperationsToggles();
    });

    test("Variants, UoM, Packagings", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoManageProductsSettingsPage();
        await inventoryPage.enableManageProductsToggles();
    });

    test("Locations And Multi-Step Routes", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoManageWarehousesSettingsPage();
        await inventoryPage.enableManageWarehousesToggles();
    });

    test("Lots & Serial Numbers", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoManageTraceabilitySettingsPage();
        await inventoryPage.enableManageTraceabilityToggles();
    });

    test("Dropshipping", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoManageLogisticsSettingsPage();
        await inventoryPage.enableManageLogisticsToggles();
    });

    test("All Inventory Settings", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.enableAllInventorySettings();
    });
});
