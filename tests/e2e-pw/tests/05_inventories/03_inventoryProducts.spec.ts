import { test } from "../../setup";
import { InventoriesManagementPage } from "../../pages/06_inventoriesManagement";

test.describe("Inventory Products - CRUD, Quantities & In/Out Tab", () => {
    test.beforeAll(async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.ensureBaseDependentPluginsInstalled();
        // Locations + multi-step routes must be on for the location-based
        // on-hand quantity selectors and the moves tab to render.
        await inventoryPage.enableManageWarehousesToggles();
        // Lots & Serial Numbers must be on to expose the product tracking field.
        await inventoryPage.enableManageTraceabilityToggles();
        // Packages must be on for the Packages resource to register.
        await inventoryPage.enableManageOperationsToggles();
    });

    test("Products Listing - Loads Table", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoProductsPage();
    });

    test("Create Storable Product - Valid Inputs", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        await inventoryPage.createInventoryProduct({
            name: `E2E Inv Product ${key}`,
            price: "150",
        });
    });

    test("Create Lot-Tracked Product - Valid Inputs", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        await inventoryPage.createInventoryProduct({
            name: `E2E Lot Product ${key}`,
            price: "100",
            tracking: "lot",
        });
    });

    test("Create Serial-Tracked Product - Valid Inputs", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        await inventoryPage.createInventoryProduct({
            name: `E2E Serial Product ${key}`,
            price: "120",
            tracking: "serial",
        });
    });

    test("Delete Product - Removes Record", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Inv Delete ${key}`;

        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "75",
        });

        await inventoryPage.deleteInventoryProduct(productName);
    });

    test("Product Quantities Tab - Loads For Storable Product", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Qty Tab ${key}`;

        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "10",
        });

        await inventoryPage.gotoProductQuantitiesTab(productName);
    });

    test("Product In/Out Moves Tab - Loads For Storable Product", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Moves Tab ${key}`;

        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "15",
        });

        await inventoryPage.gotoProductMovesTab(productName);
    });

    test("Add On-Hand Quantity At Default Stock Location", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E On-Hand ${key}`;

        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "20",
        });

        await inventoryPage.addOnHandQuantity(productName, "WH/Stock", "30");
        await inventoryPage.expectOnHandQuantityRow(productName, "WH/Stock", "30");
    });

    test("New Warehouse - Add On-Hand Quantity At Its Stock Location", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouseName = `WH Qty ${key}`;
        const warehouseCode = `WQ${key}`.slice(-5);
        const productName = `E2E WH Qty ${key}`;

        await inventoryPage.createWarehouse({
            name: warehouseName,
            code: warehouseCode,
            receptionStep: 1,
            deliveryStep: 1,
        });

        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "25",
        });

        // Auto-created stock location for a 1-step warehouse is "<CODE>/Stock".
        const stockLocation = `${warehouseCode}/Stock`;
        await inventoryPage.addOnHandQuantity(productName, stockLocation, "50");
        await inventoryPage.expectOnHandQuantityRow(productName, warehouseCode, "50");
    });

    /**
     * The Packages listing table renders.
     */
    test("Packages list loads", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoPackagesPage();
    });

    /**
     * A package holds a product quantity added at a location via the quantities tab.
     */
    test("Package holds product quantity", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const packageName = `E2E Package ${key}`;
        const productName = `E2E Pkg Product ${key}`;

        await inventoryPage.createPackage({ name: packageName });
        await inventoryPage.createInventoryProduct({ name: productName, price: "20" });

        // Add 30 on-hand at the default warehouse stock location, inside the package.
        await inventoryPage.addOnHandQuantity(productName, "WH/Stock", "30", packageName);

        // The package's Products tab should now list the product at quantity 30.
        await inventoryPage.expectPackageContainsProduct(packageName, productName, "30");
    });

    /**
     * A package at an internal location lists and can be deleted from its row actions.
     */
    test("Delete package - removes record", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const packageName = `E2E Package Delete ${key}`;

        // The packages list only shows packages at an internal location.
        await inventoryPage.createPackage({ name: packageName, location: "WH/Stock" });
        await inventoryPage.deletePackage(packageName);
    });

});
