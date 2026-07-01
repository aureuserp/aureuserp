import { test } from "../../setup";
import { InventoriesManagementPage } from "../../pages/06_inventoriesManagement";

test.describe("Inventory Settings - Toggle Configuration", () => {
    test.beforeAll(async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.ensureBaseDependentPluginsInstalled();
    });

    test("Manage Operations Page - Loads And Toggles Save", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoManageOperationsPage();
        await inventoryPage.enableManageOperationsToggles();
    });

    test("Manage Products Page - Enable Variants, UoM, Packagings", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoManageProductsSettingsPage();
        await inventoryPage.enableManageProductsToggles();
    });

    test("Manage Warehouses Page - Enable Locations And Multi-Step Routes", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoManageWarehousesSettingsPage();
        await inventoryPage.enableManageWarehousesToggles();
    });

    test("Manage Traceability Page - Enable Lots & Serial Numbers", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoManageTraceabilitySettingsPage();
        await inventoryPage.enableManageTraceabilityToggles();
    });

    test("Manage Logistics Page - Enable Dropshipping", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoManageLogisticsSettingsPage();
        await inventoryPage.enableManageLogisticsToggles();
    });

    test("Enable All Inventory Settings - Combined Flow", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.enableAllInventorySettings();
    });
});
