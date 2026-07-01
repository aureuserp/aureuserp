import { test } from "../../setup";
import { InventoriesManagementPage } from "../../pages/06_inventoriesManagement";

/**
 * Inventory configuration resources that aren't products or operations
 * (e.g. Package Types).
 */
test.describe("Inventory Configurations - Package Types", () => {
    /**
     * Packages must be enabled for the Package Type resource to register.
     */
    test.beforeAll(async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.ensureBaseDependentPluginsInstalled();
        await inventoryPage.enableManageOperationsToggles();
    });

    /**
     * The Package Types listing table renders.
     */
    test("Package types list loads", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoPackageTypesPage();
    });

    /**
     * A package type can be created with its dimensions and weights.
     */
    test("Create package type", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        await inventoryPage.createPackageType({
            name: `E2E Pack Type ${key}`,
            length: "40",
            width: "30",
            height: "20",
            baseWeight: "2",
            maxWeight: "25",
        });
    });
});
