import { test } from "../../setup";
import { InventoriesManagementPage } from "../../pages/06_inventoriesManagement";

test.describe("Inventory End-To-End Flow - 3-Step Warehouse, Receipt -> Internal Moves -> Validate", () => {
    test.beforeAll(async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.ensureBaseDependentPluginsInstalled();
        // Enable everything: locations, multi-step routes, packagings,
        // UoM, traceability, dropshipping. Required for the full 3-step flow.
        await inventoryPage.enableAllInventorySettings();
    });

    test("Three-Step Incoming - Receipt Then Internal Transfers Then Stock Check", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const warehouseName = `WH Flow ${key}`;
        const warehouseCode = `WF${key}`.slice(-5);
        const productName = `E2E Flow Product ${key}`;

        // 1) Create a 3-step incoming + 3-step delivery warehouse.
        await inventoryPage.createWarehouse({
            name: warehouseName,
            code: warehouseCode,
            receptionStep: 3,
            deliveryStep: 3,
        });

        // 2) Verify auto-created locations, operation types, routes, rules.
        await inventoryPage.expectLocationCreatedFor(warehouseCode);
        await inventoryPage.expectOperationTypeCreatedFor(warehouseName);
        await inventoryPage.expectRouteCreatedFor(warehouseName);
        await inventoryPage.expectRuleCreatedFor(warehouseCode);

        // 3) Create a storable product.
        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "100",
        });

        // 4) Receipt: receive stock at the input location (step 1 of 3).
        await inventoryPage.receiptFullFlow({
            productName,
            demand: "25",
        });

        // 5) For 3-step incoming, the route auto-creates the QC and
        // store internal transfers. Validate them in sequence.
        await inventoryPage.gotoInternalTransfersPage();
        await inventoryPage.searchList(productName);

        // 6) Validate the first auto-created internal transfer (input -> QC).
        await inventoryPage.internalTransferFullFlow({
            productName,
            demand: "25",
        });

        // 7) Validate quantities are reflected on the product quantities page.
        await inventoryPage.expectProductQuantityRowVisible(productName);

        // 8) Issue an outgoing delivery to verify Out movement.
        await inventoryPage.deliveryFullFlow({
            productName,
            demand: "5",
        });

        await inventoryPage.expectProductQuantityRowVisible(productName);
    });

    test("One-Step Warehouse - Direct Receipt And Delivery Flow", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const warehouseName = `WH One-Step ${key}`;
        const warehouseCode = `W1${key}`.slice(-5);
        const productName = `E2E One-Step Product ${key}`;

        // 1) Create a 1-step warehouse (direct receipt + direct delivery).
        await inventoryPage.createWarehouse({
            name: warehouseName,
            code: warehouseCode,
            receptionStep: 1,
            deliveryStep: 1,
        });

        // 2) Verify auto-created configs exist for the new warehouse.
        await inventoryPage.expectLocationCreatedFor(warehouseCode);
        await inventoryPage.expectOperationTypeCreatedFor(warehouseName);

        // 3) Create a storable product.
        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "60",
        });

        // 4) Single-step receipt goes straight to stock.
        await inventoryPage.receiptFullFlow({
            productName,
            demand: "20",
        });
        await inventoryPage.expectProductMoveRowVisible(productName, "Done");

        // 5) Single-step delivery ships directly from stock.
        await inventoryPage.deliveryFullFlow({
            productName,
            demand: "8",
        });
        await inventoryPage.expectProductQuantityRowVisible(productName);
    });

    test("Two-Step Incoming - Receipt Then Single Internal Transfer", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const warehouseName = `WH Two-Step ${key}`;
        const warehouseCode = `W2${key}`.slice(-5);
        const productName = `E2E Two-Step Product ${key}`;

        // 1) Create a 2-step incoming warehouse (input -> stock).
        await inventoryPage.createWarehouse({
            name: warehouseName,
            code: warehouseCode,
            receptionStep: 2,
            deliveryStep: 1,
        });

        await inventoryPage.expectLocationCreatedFor(warehouseCode);
        await inventoryPage.expectOperationTypeCreatedFor(warehouseName);
        await inventoryPage.expectRouteCreatedFor(warehouseName);

        // 2) Create a storable product.
        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "45",
        });

        // 3) Receipt brings stock to the input location.
        await inventoryPage.receiptFullFlow({
            productName,
            demand: "16",
        });

        // 4) Auto-created internal transfer pushes input -> stock; validate it.
        await inventoryPage.gotoInternalTransfersPage();
        await inventoryPage.searchList(productName);
        await inventoryPage.internalTransferFullFlow({
            productName,
            demand: "16",
        });

        // 5) Quantities page should now report the product as on-hand.
        await inventoryPage.expectProductQuantityRowVisible(productName);
        await inventoryPage.expectProductMoveRowVisible(productName, "Done");
    });

    test("Three-Step Delivery - Validates Out Chain End-To-End", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const warehouseName = `WH Out 3-Step ${key}`;
        const warehouseCode = `WO${key}`.slice(-5);
        const productName = `E2E Out 3-Step ${key}`;

        // 1) Warehouse with 1-step incoming + 3-step outgoing (pick -> pack -> ship).
        await inventoryPage.createWarehouse({
            name: warehouseName,
            code: warehouseCode,
            receptionStep: 1,
            deliveryStep: 3,
        });

        // 2) Create storable product and stock it.
        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "55",
        });

        await inventoryPage.receiptFullFlow({
            productName,
            demand: "30",
        });
        await inventoryPage.expectProductQuantityRowVisible(productName);

        // 3) Outgoing delivery chain. Validate the delivery operation itself.
        await inventoryPage.deliveryFullFlow({
            productName,
            demand: "10",
        });

        // 4) The 3-step outgoing route auto-creates internal pick/pack transfers.
        // Validate the chained internal transfers in sequence.
        await inventoryPage.gotoInternalTransfersPage();
        await inventoryPage.searchList(productName);
        await inventoryPage.internalTransferFullFlow({
            productName,
            demand: "10",
        });

        await inventoryPage.expectProductMoveRowVisible(productName, "Done");
    });

    test("End-To-End Two-Step - Multiple Products Through Same Warehouse", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const warehouseName = `WH Multi ${key}`;
        const warehouseCode = `WM${key}`.slice(-5);
        const productOne = `E2E Multi One ${key}`;
        const productTwo = `E2E Multi Two ${key}`;

        // 1) 2-step warehouse so we exercise the input -> stock chain twice.
        await inventoryPage.createWarehouse({
            name: warehouseName,
            code: warehouseCode,
            receptionStep: 2,
            deliveryStep: 2,
        });

        // 2) Two distinct storable products.
        await inventoryPage.createInventoryProduct({
            name: productOne,
            price: "30",
        });
        await inventoryPage.createInventoryProduct({
            name: productTwo,
            price: "70",
        });

        // 3) Flow product 1: receipt + internal transfer to stock.
        await inventoryPage.receiptFullFlow({
            productName: productOne,
            demand: "10",
        });
        await inventoryPage.gotoInternalTransfersPage();
        await inventoryPage.searchList(productOne);
        await inventoryPage.internalTransferFullFlow({
            productName: productOne,
            demand: "10",
        });
        await inventoryPage.expectProductQuantityRowVisible(productOne);

        // 4) Flow product 2: receipt + internal transfer to stock.
        await inventoryPage.receiptFullFlow({
            productName: productTwo,
            demand: "5",
        });
        await inventoryPage.gotoInternalTransfersPage();
        await inventoryPage.searchList(productTwo);
        await inventoryPage.internalTransferFullFlow({
            productName: productTwo,
            demand: "5",
        });
        await inventoryPage.expectProductQuantityRowVisible(productTwo);
    });

    test("Warehouse Step Change - Edit One-Step Into Two-Step Incoming", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const warehouseName = `WH Edit ${key}`;
        const warehouseCode = `WE${key}`.slice(-5);
        const productName = `E2E Edit Steps ${key}`;

        // 1) Start as a 1-step warehouse.
        await inventoryPage.createWarehouse({
            name: warehouseName,
            code: warehouseCode,
            receptionStep: 1,
            deliveryStep: 1,
        });

        // 2) Re-configure to 2-step incoming + 1-step delivery.
        await inventoryPage.editWarehouseSteps(warehouseName, 2, 1);

        // 3) Create a product and exercise the updated incoming chain.
        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "50",
        });

        await inventoryPage.receiptFullFlow({
            productName,
            demand: "12",
        });

        // 4) The new 2-step config should require an internal transfer to move
        // stock from input -> stock before it shows on quantities.
        await inventoryPage.gotoInternalTransfersPage();
        await inventoryPage.searchList(productName);
        await inventoryPage.internalTransferFullFlow({
            productName,
            demand: "12",
        });

        await inventoryPage.expectProductQuantityRowVisible(productName);
    });
});
