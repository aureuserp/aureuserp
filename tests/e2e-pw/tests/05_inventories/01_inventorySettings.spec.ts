import { test } from "../../setup";
import { InventoriesManagementPage } from "../../pages/06_inventoriesManagement";

/**
 * These tests ARE the settings provisioning, so they pass `force` to bypass the per-worker
 * cache the other describes rely on — otherwise a settings toggle already provisioned by an
 * earlier describe would make the test here a no-op.
 */
test.describe("Inventory Settings - Toggles", () => {
    test.beforeAll(async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.ensureInventoriesPluginInstalled();
    });

    test("Packages", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoManageOperationsPage();
        await inventoryPage.enableManageOperationsToggles(true);
    });

    test("Variants, UoM, Packagings", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoManageProductsSettingsPage();
        await inventoryPage.enableManageProductsToggles(true);
    });

    test("Locations And Multi-Step Routes", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoManageWarehousesSettingsPage();
        await inventoryPage.enableManageWarehousesToggles(true);
    });

    test("Lots & Serial Numbers", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoManageTraceabilitySettingsPage();
        await inventoryPage.enableManageTraceabilityToggles(true);
    });

    test("Dropshipping", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoManageLogisticsSettingsPage();
        await inventoryPage.enableManageLogisticsToggles(true);
    });

    test("All Inventory Settings", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.enableAllInventorySettings(true);
    });
});
