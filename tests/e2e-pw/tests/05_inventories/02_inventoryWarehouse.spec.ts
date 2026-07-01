import { test } from "../../setup";
import { InventoriesManagementPage } from "../../pages/06_inventoriesManagement";

/**
 * Warehouse step-configuration auto-creation, verified by record presence (not brittle counts).
 */
test.describe("Inventory Warehouse - Step Configuration & Auto-Created Records", () => {
    /**
     * Locations + multi-step routes must be enabled to expose the step radios.
     */
    test.beforeAll(async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.ensureBaseDependentPluginsInstalled();
        await inventoryPage.enableManageWarehousesToggles();
    });

    /**
     * The warehouse listing table renders.
     */
    test("Warehouse Listing - Loads Table", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoWarehousesPage();
    });

    /**
     * A one-step warehouse creates only the single-step records.
     */
    test("Create Warehouse - One Step (Receive / Deliver)", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouse = {
            name: `WH 1Step ${key}`,
            code: `W1${key}`.slice(-5),
            receptionStep: 1 as const,
            deliveryStep: 1 as const,
        };

        await inventoryPage.createWarehouse(warehouse);
        await inventoryPage.expectWarehouseConfiguration(warehouse);
    });

    /**
     * Two steps add the Input/Output locations, Storage/Pick types and two-step routes.
     */
    test("Create Warehouse - Two Steps (Receive Then Store / Pick Then Deliver)", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouse = {
            name: `WH 2Step ${key}`,
            code: `W2${key}`.slice(-5),
            receptionStep: 2 as const,
            deliveryStep: 2 as const,
        };

        await inventoryPage.createWarehouse(warehouse);
        await inventoryPage.expectWarehouseConfiguration(warehouse);
    });

    /**
     * Three steps add the Quality Control/Packing Zone locations, QC/Pack types and three-step routes.
     */
    test("Create Warehouse - Three Steps (Receive, QC, Store / Pick, Pack, Deliver)", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouse = {
            name: `WH 3Step ${key}`,
            code: `W3${key}`.slice(-5),
            receptionStep: 3 as const,
            deliveryStep: 3 as const,
        };

        await inventoryPage.createWarehouse(warehouse);
        await inventoryPage.expectWarehouseConfiguration(warehouse);
    });

    /**
     * Reception and delivery steps are independent: two-step receive, one-step deliver.
     */
    test("Create Warehouse - Asymmetric Steps (Two-Step Receive / One-Step Deliver)", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouse = {
            name: `WH Mixed ${key}`,
            code: `WX${key}`.slice(-5),
            receptionStep: 2 as const,
            deliveryStep: 1 as const,
        };

        await inventoryPage.createWarehouse(warehouse);
        await inventoryPage.expectWarehouseConfiguration(warehouse);
    });

    /**
     * Editing a one-step warehouse to three steps adds the multi-step records and drops the single-step routes.
     */
    test("Edit Warehouse - Switch From One Step To Three Steps", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouse = {
            name: `WH Edit ${key}`,
            code: `WE${key}`.slice(-5),
            receptionStep: 1 as const,
            deliveryStep: 1 as const,
        };

        await inventoryPage.createWarehouse(warehouse);
        await inventoryPage.expectWarehouseConfiguration(warehouse);

        await inventoryPage.editWarehouseSteps(warehouse.code, 3, 3);
        await inventoryPage.expectWarehouseConfiguration({
            ...warehouse,
            receptionStep: 3,
            deliveryStep: 3,
        });
    });
});
