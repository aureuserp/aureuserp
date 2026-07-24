import { test } from "../../setup";
import { InventoriesManagementPage } from "../../pages/06_inventoriesManagement";

/**
 * Enable exactly the inventory settings the operations tests rely on (locations,
 * traceability for lot/serial, and operations for packages). Each describe calls
 * this in its own beforeAll so any shard/worker that runs only a subset of the
 * describes still provisions the settings its tests need — this keeps CI
 * sharding and fullyParallel runs correct and order-independent.
 */
async function enableOperationsSettings(adminPage: import("@playwright/test").Page) {
    const inventoryPage = new InventoriesManagementPage(adminPage);
    await inventoryPage.ensureBaseDependentPluginsInstalled();
    await inventoryPage.enableManageWarehousesToggles();
    await inventoryPage.enableManageTraceabilityToggles();
    await inventoryPage.enableManageOperationsToggles();
}

test.describe("Inventory Operations - Listings", () => {
    test.beforeAll(async ({ adminPage }) => {
        await enableOperationsSettings(adminPage);
    });

    /**
     * The receipts listing table renders.
     */
    test("Receipts Listing - Loads Table", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoReceiptsPage();
    });

    /**
     * The deliveries listing table renders.
     */
    test("Deliveries Listing - Loads Table", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoDeliveriesPage();
    });

    /**
     * The internal transfers listing table renders.
     */
    test("Internal Transfers Listing - Loads Table", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoInternalTransfersPage();
    });

    /**
     * The Scraps listing table renders.
     */
    test("Scraps list loads", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoScrapsPage();
    });
});

test.describe("Inventory Operations - Receipts", () => {
    test.beforeAll(async ({ adminPage }) => {
        await enableOperationsSettings(adminPage);
    });

    /**
     * A validated receipt brings stock on hand.
     */
    test("Receipt Full Flow - Create, Validate, Stock Increases", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Receipt Flow ${key}`;

        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "20",
        });

        await inventoryPage.receiptFullFlow({
            productName,
            demand: "10",
        });

        await inventoryPage.expectProductQuantityRowVisible(productName);
    });

    /**
     * Two receipts each add a row to the product moves tab.
     */
    test("Two Sequential Receipts - Both Reflect On Product Moves Tab", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Two Receipts ${key}`;

        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "25",
        });

        await inventoryPage.receiptFullFlow({
            productName,
            demand: "8",
        });
        const movesAfterFirst = await inventoryPage.countProductMoveRows(productName);

        await inventoryPage.receiptFullFlow({
            productName,
            demand: "12",
        });
        const movesAfterSecond = await inventoryPage.countProductMoveRows(productName);

        if (movesAfterSecond <= movesAfterFirst) {
            throw new Error(
                `Expected moves count to grow after second receipt: ${movesAfterFirst} -> ${movesAfterSecond}`
            );
        }
    });

    /**
     * A validated receipt shows on the product quantities and moves tabs.
     */
    test("Receipt Validation Reflects In Product In/Out Tab And Quantities", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Move Flow ${key}`;

        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "30",
        });

        await inventoryPage.receiptFullFlow({
            productName,
            demand: "12",
        });

        await inventoryPage.gotoProductQuantitiesTab(productName);

        await inventoryPage.expectProductMoveRowVisible(productName, "Done");
    });

    /**
     * A lot-tracked receipt generates its lot and validates.
     */
    test("Receipt With Lot - Generate Lot And Validate", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Lot Receipt ${key}`;
        const lotName = `LOT-${key}`;

        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "30",
            tracking: "lot",
        });

        await inventoryPage.receiptWithLotFlow({ productName, demand: "8" }, lotName);

        await inventoryPage.expectProductQuantityRowVisible(productName);
        await inventoryPage.expectProductMoveRowVisible(productName, "Done");
    });

    /**
     * A serial-tracked receipt generates its serials and validates.
     */
    test("Receipt With Serial - Generate Serials And Validate", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Serial Receipt ${key}`;
        const serialPrefix = `SN-${key}`;

        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "30",
            tracking: "serial",
        });

        await inventoryPage.receiptWithLotFlow({ productName, demand: "3" }, serialPrefix);

        await inventoryPage.expectProductQuantityRowVisible(productName);
        await inventoryPage.expectProductMoveRowVisible(productName, "Done");
    });

    /**
     * A 2-step warehouse receipt reaches stock via Next Transfer.
     */
    test("Receipt flow - 2-step to stock", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouseName = `WH 2Step ${key}`;
        const warehouseCode = `R2${key}`;
        const productName = `E2E 2Step Receipt ${key}`;

        await inventoryPage.createWarehouse({
            name: warehouseName,
            code: warehouseCode,
            receptionStep: 2,
            deliveryStep: 1,
        });
        await inventoryPage.createInventoryProduct({ name: productName, price: "20" });

        await inventoryPage.receiptChainFullFlow({
            productName,
            demand: "10",
            operationType: warehouseName,
        });

        await inventoryPage.expectOnHandQuantityRow(productName, `${warehouseCode}/Stock`, "10");
    });

    /**
     * A 3-step warehouse receipt reaches stock via Next Transfer.
     */
    test("Receipt flow - 3-step to stock", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouseName = `WH 3Step ${key}`;
        const warehouseCode = `R3${key}`;
        const productName = `E2E 3Step Receipt ${key}`;

        await inventoryPage.createWarehouse({
            name: warehouseName,
            code: warehouseCode,
            receptionStep: 3,
            deliveryStep: 1,
        });
        await inventoryPage.createInventoryProduct({ name: productName, price: "20" });

        await inventoryPage.receiptChainFullFlow({
            productName,
            demand: "10",
            operationType: warehouseName,
        });

        await inventoryPage.expectOnHandQuantityRow(productName, `${warehouseCode}/Stock`, "10");
    });

    /**
     * A single receipt with multiple product lines validates.
     */
    test("Receipt - multiple products", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productA = `E2E Multi A ${key}`;
        const productB = `E2E Multi B ${key}`;

        await inventoryPage.createInventoryProduct({ name: productA, price: "10" });
        await inventoryPage.createInventoryProduct({ name: productB, price: "15" });

        await inventoryPage.receiptLinesFullFlow([
            { productName: productA, demand: "5" },
            { productName: productB, demand: "3" },
        ]);

        await inventoryPage.expectProductQuantityRowVisible(productA);
        await inventoryPage.expectProductQuantityRowVisible(productB);
    });

    /**
     * One receipt mixes lot, serial and quantity-tracked lines.
     */
    test("Receipt - mixed lot, serial & quantity", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const lotProduct = `E2E Mix Lot ${key}`;
        const serialProduct = `E2E Mix Serial ${key}`;
        const qtyProduct = `E2E Mix Qty ${key}`;

        await inventoryPage.createInventoryProduct({ name: lotProduct, price: "10", tracking: "lot" });
        await inventoryPage.createInventoryProduct({ name: serialProduct, price: "12", tracking: "serial" });
        await inventoryPage.createInventoryProduct({ name: qtyProduct, price: "8" });

        await inventoryPage.receiptLinesFullFlow([
            { productName: lotProduct, demand: "4", lotName: `LOT-${key}` },
            { productName: serialProduct, demand: "2", lotName: `SN-${key}` },
            { productName: qtyProduct, demand: "5" },
        ]);

        await inventoryPage.expectProductQuantityRowVisible(lotProduct);
        await inventoryPage.expectProductQuantityRowVisible(serialProduct);
        await inventoryPage.expectProductQuantityRowVisible(qtyProduct);
    });
});

test.describe("Inventory Operations - Deliveries", () => {
    test.beforeAll(async ({ adminPage }) => {
        await enableOperationsSettings(adminPage);
    });

    /**
     * A validated delivery ships stock out after a receipt.
     */
    test("Delivery Full Flow - Create, Validate, Stock Decreases", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Delivery Flow ${key}`;

        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "30",
        });

        await inventoryPage.receiptFullFlow({
            productName,
            demand: "20",
        });

        await inventoryPage.deliveryFullFlow({
            productName,
            demand: "5",
        });

        await inventoryPage.expectProductQuantityRowVisible(productName);
    });

    /**
     * A delivery after a receipt adds an outgoing move row.
     */
    test("Delivery After Receipt - Outgoing Move Row Visible", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Out Move Op ${key}`;

        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "22",
        });

        await inventoryPage.receiptFullFlow({
            productName,
            demand: "20",
        });
        const movesAfterReceipt = await inventoryPage.countProductMoveRows(productName);

        await inventoryPage.deliveryFullFlow({
            productName,
            demand: "6",
        });
        const movesAfterDelivery = await inventoryPage.countProductMoveRows(productName);

        if (movesAfterDelivery <= movesAfterReceipt) {
            throw new Error(
                `Expected moves count to grow after delivery: ${movesAfterReceipt} -> ${movesAfterDelivery}`
            );
        }
    });

    /**
     * A delivery reduces on-hand and adds a done outgoing move row.
     */
    test("Delivery Validation Adds An Outgoing Row To Product In/Out Tab", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouseName = `WH Out ${key}`;
        const warehouseCode = `WO${key}`;
        const productName = `E2E Out Move ${key}`;

        await inventoryPage.createWarehouse({
            name: warehouseName,
            code: warehouseCode,
            receptionStep: 1,
            deliveryStep: 1,
        });

        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "40",
        });

        await inventoryPage.receiptFullFlow({
            productName,
            demand: "10",
            operationType: warehouseName,
        });

        await inventoryPage.deliveryFullFlow({
            productName,
            demand: "4",
            operationType: warehouseName,
        });

        await inventoryPage.expectOnHandQuantityRow(productName, warehouseCode, "6");
        await inventoryPage.expectProductMoveRowVisible(productName, "Done");
        await inventoryPage.expectProductQuantityRowVisible(productName);
    });

    /**
     * A 3-step delivery ships out through Pick, Pack and Ship.
     */
    test("Delivery flow - 3-step pick, pack, ship", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouseName = `WH Out3Step ${key}`;
        const warehouseCode = `D3${key}`;
        const productName = `E2E 3Step Delivery ${key}`;

        await inventoryPage.createWarehouse({
            name: warehouseName,
            code: warehouseCode,
            receptionStep: 1,
            deliveryStep: 3,
        });
        await inventoryPage.createInventoryProduct({ name: productName, price: "20" });

        await inventoryPage.receiptFullFlow({
            productName,
            demand: "10",
            operationType: warehouseName,
        });

        await inventoryPage.internalTransferFullFlow({
            productName,
            demand: "10",
            operationType: warehouseName,
            operationTypeName: "Pick",
        });
        await inventoryPage.chainNextTransfers();

        await inventoryPage.expectProductMoveRowVisible(productName, "Done");
    });

    /**
     * A lot-tracked product is received then delivered, reserving its lot.
     */
    test("Delivery With Lot - Reserve Lot And Validate", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Dlv Lot ${key}`;
        const lotName = `LOT-${key}`;

        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "30",
            tracking: "lot",
        });

        await inventoryPage.receiptWithLotFlow({ productName, demand: "8" }, lotName);

        await inventoryPage.deliveryFullFlow({ productName, demand: "5" });

        await inventoryPage.expectOnHandQuantityRow(productName, "WH/Stock", "3");
        await inventoryPage.expectProductMoveRowVisible(productName, "Done");
    });

    /**
     * A serial-tracked product is received then partially delivered, reserving a serial.
     */
    test("Delivery With Serial - Reserve Serial And Validate", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Dlv Serial ${key}`;
        const serialPrefix = `SN-${key}`;

        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "30",
            tracking: "serial",
        });

        await inventoryPage.receiptWithLotFlow({ productName, demand: "2" }, serialPrefix);

        await inventoryPage.deliveryFullFlow({ productName, demand: "1" });

        await inventoryPage.expectProductQuantityRowVisible(productName);
        await inventoryPage.expectProductMoveRowVisible(productName, "Done");
    });

    /**
     * A 2-step delivery ships out through Pick then Ship.
     */
    test("Delivery flow - 2-step pick, ship", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouseName = `WH Out2Step ${key}`;
        const warehouseCode = `D2${key}`;
        const productName = `E2E 2Step Delivery ${key}`;

        await inventoryPage.createWarehouse({
            name: warehouseName,
            code: warehouseCode,
            receptionStep: 1,
            deliveryStep: 2,
        });
        await inventoryPage.createInventoryProduct({ name: productName, price: "20" });

        await inventoryPage.receiptFullFlow({
            productName,
            demand: "10",
            operationType: warehouseName,
        });

        await inventoryPage.internalTransferFullFlow({
            productName,
            demand: "10",
            operationType: warehouseName,
            operationTypeName: "Pick",
        });
        await inventoryPage.chainNextTransfers();

        await inventoryPage.expectProductMoveRowVisible(productName, "Done");
    });

    /**
     * One delivery ships mixed lot, serial and quantity-tracked lines.
     */
    test("Delivery - mixed lot, serial & quantity", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const lotProduct = `E2E DlvMix Lot ${key}`;
        const serialProduct = `E2E DlvMix Serial ${key}`;
        const qtyProduct = `E2E DlvMix Qty ${key}`;

        await inventoryPage.createInventoryProduct({ name: lotProduct, price: "10", tracking: "lot" });
        await inventoryPage.createInventoryProduct({ name: serialProduct, price: "12", tracking: "serial" });
        await inventoryPage.createInventoryProduct({ name: qtyProduct, price: "8" });

        await inventoryPage.receiptLinesFullFlow([
            { productName: lotProduct, demand: "4", lotName: `LOT-${key}` },
            { productName: serialProduct, demand: "2", lotName: `SN-${key}` },
            { productName: qtyProduct, demand: "5" },
        ]);

        await inventoryPage.deliveryLinesFullFlow([
            { productName: lotProduct, demand: "2" },
            { productName: serialProduct, demand: "1" },
            { productName: qtyProduct, demand: "3" },
        ]);

        await inventoryPage.expectProductMoveRowVisible(lotProduct, "Done");
        await inventoryPage.expectProductMoveRowVisible(serialProduct, "Done");
        await inventoryPage.expectProductMoveRowVisible(qtyProduct, "Done");
    });
});

test.describe("Inventory Operations - Internal Transfers", () => {
    test.beforeAll(async ({ adminPage }) => {
        await enableOperationsSettings(adminPage);
    });

    /**
     * A validated internal transfer moves stock between locations.
     */
    test("Internal Transfer - Create And Validate Move", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Internal Flow ${key}`;

        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "40",
        });

        await inventoryPage.receiptFullFlow({
            productName,
            demand: "15",
        });

        await inventoryPage.internalTransferFullFlow({
            productName,
            demand: "5",
        });

        await inventoryPage.expectProductQuantityRowVisible(productName);
    });

    /**
     * An internal transfer adds a move row to the product moves tab.
     */
    test("Internal Transfer - Move Row Adds To Product Moves Tab", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Internal Moves ${key}`;

        await inventoryPage.createInventoryProduct({
            name: productName,
            price: "33",
        });

        await inventoryPage.receiptFullFlow({
            productName,
            demand: "18",
        });
        const movesAfterReceipt = await inventoryPage.countProductMoveRows(productName);

        await inventoryPage.internalTransferFullFlow({
            productName,
            demand: "4",
        });
        const movesAfterTransfer = await inventoryPage.countProductMoveRows(productName);

        if (movesAfterTransfer <= movesAfterReceipt) {
            throw new Error(
                `Expected moves count to grow after internal transfer: ${movesAfterReceipt} -> ${movesAfterTransfer}`
            );
        }
    });
});

test.describe("Inventory Operations - Packages", () => {
    test.beforeAll(async ({ adminPage }) => {
        await enableOperationsSettings(adminPage);
    });

    /**
     * Receiving into a destination package leaves the stock held in that package.
     */
    test("Receipt into package", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Rcpt Pkg Product ${key}`;
        const packageName = `E2E Rcpt Package ${key}`;

        await inventoryPage.createInventoryProduct({ name: productName, price: "10" });
        await inventoryPage.createPackage({ name: packageName });

        await inventoryPage.receiptIntoPackageFlow({ productName, demand: "8" }, packageName);

        await inventoryPage.expectPackageContainsProduct(packageName, productName, "8");
    });

    /**
     * Delivering a package's stock moves the package out of internal stock.
     */
    test("Delivery moves package out", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Dlv Pkg Product ${key}`;
        const packageName = `E2E Dlv Package ${key}`;

        await inventoryPage.createInventoryProduct({ name: productName, price: "10" });
        await inventoryPage.createPackage({ name: packageName });

        await inventoryPage.addOnHandQuantity(productName, "WH/Stock", "10", packageName);

        await inventoryPage.deliveryFullFlow({ productName, demand: "10" });

        await inventoryPage.expectPackageNotListed(packageName);
    });

    /**
     * A package travels a 3-step warehouse from receipt to delivery.
     */
    test("3-step warehouse - package receipt to delivery", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouseName = `WH Pkg3 ${key}`;
        const warehouseCode = `P3${key}`;
        const productName = `E2E 3S Pkg Product ${key}`;
        const packageName = `E2E 3S Package ${key}`;

        await inventoryPage.createWarehouse({
            name: warehouseName,
            code: warehouseCode,
            receptionStep: 3,
            deliveryStep: 3,
        });
        await inventoryPage.createInventoryProduct({ name: productName, price: "10" });
        await inventoryPage.createPackage({ name: packageName });

        await inventoryPage.receiptIntoPackageChainFlow(
            { productName, demand: "6", operationType: warehouseName },
            packageName
        );

        await inventoryPage.expectPackageContainsProduct(packageName, productName, "6");

        await inventoryPage.internalTransferFullFlow({
            productName,
            demand: "6",
            operationType: warehouseName,
            operationTypeName: "Pick",
        });
        await inventoryPage.chainNextTransfers();

        await inventoryPage.expectPackageNotListed(packageName);
    });

    /**
     * One package is delivered in full, another only in part.
     */
    test("Package move - full vs partial delivery", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const fullProduct = `E2E Full Pkg Prod ${key}`;
        const partialProduct = `E2E Part Pkg Prod ${key}`;
        const fullPackage = `E2E Full Package ${key}`;
        const partialPackage = `E2E Partial Package ${key}`;

        await inventoryPage.createInventoryProduct({ name: fullProduct, price: "10" });
        await inventoryPage.createInventoryProduct({ name: partialProduct, price: "10" });
        await inventoryPage.createPackage({ name: fullPackage });
        await inventoryPage.createPackage({ name: partialPackage });

        await inventoryPage.addOnHandQuantity(fullProduct, "WH/Stock", "10", fullPackage);
        await inventoryPage.addOnHandQuantity(partialProduct, "WH/Stock", "10", partialPackage);

        await inventoryPage.deliveryFullFlow({ productName: fullProduct, demand: "10" });
        await inventoryPage.deliveryFullFlow({ productName: partialProduct, demand: "4" });

        await inventoryPage.expectPackageNotListed(fullPackage);
        await inventoryPage.expectPackageContainsProduct(partialPackage, partialProduct, "6");
    });
});

test.describe("Inventory Operations - Scrap & Adjustments", () => {
    test.beforeAll(async ({ adminPage }) => {
        await enableOperationsSettings(adminPage);
    });

    /**
     * Validating a scrap reduces the product's on-hand stock by the scrapped amount.
     */
    test("Scrap reduces on-hand stock", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Scrap Product ${key}`;

        await inventoryPage.createInventoryProduct({ name: productName, price: "10" });

        await inventoryPage.addOnHandQuantity(productName, "WH/Stock", "10");

        await inventoryPage.scrapFlow({ productName, qty: "3", sourceLocation: "WH/Stock" });

        await inventoryPage.expectOnHandQuantityRow(productName, "WH/Stock", "7");
    });

    /**
     * An on-hand quantity can be adjusted inline to a new value.
     */
    test("Adjust on-hand quantity", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Adjust Qty ${key}`;

        await inventoryPage.createInventoryProduct({ name: productName, price: "10" });
        await inventoryPage.addOnHandQuantity(productName, "WH/Stock", "10");

        await inventoryPage.adjustOnHandQuantity(productName, "15");
        await inventoryPage.expectOnHandQuantityRow(productName, "WH/Stock", "15");
    });

    /**
     * Tracking is locked while stock is on hand and switches once it is adjusted away.
     */
    test("Tracking change allowed after clearing stock", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Track Adjust ${key}`;

        await inventoryPage.createInventoryProduct({ name: productName, price: "10" });
        await inventoryPage.addOnHandQuantity(productName, "WH/Stock", "10");

        await inventoryPage.editProductTracking(productName, "lot");
        await inventoryPage.expectProductTracking(productName, "qty");

        await inventoryPage.clearStockViaAdjustment(productName);
        await inventoryPage.editProductTracking(productName, "lot");
        await inventoryPage.expectProductTracking(productName, "lot");
    });
});

test.describe("Inventory Operations - Returns", () => {
    test.beforeAll(async ({ adminPage }) => {
        await enableOperationsSettings(adminPage);
    });

    /**
     * A receipt's return reverses its source and destination locations.
     */
    test("Return receipt reverses its locations", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Return Receipt ${key}`;

        await inventoryPage.createInventoryProduct({ name: productName, price: "20" });
        await inventoryPage.receiptFullFlow({ productName, demand: "10" });

        await inventoryPage.returnAndExpectReversedLocations();
    });

    /**
     * A receipt's return operation validates through to Done.
     */
    test("Return validates to done", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Return Validate ${key}`;

        await inventoryPage.createInventoryProduct({ name: productName, price: "20" });
        await inventoryPage.receiptFullFlow({ productName, demand: "10" });
        await inventoryPage.returnAndValidate();
        await inventoryPage.expectOperationDone();
    });

    /**
     * A partial return carries only the entered quantity to its return operation.
     */
    test("Partial return sets its own quantity", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Return Partial ${key}`;

        await inventoryPage.createInventoryProduct({ name: productName, price: "20" });
        await inventoryPage.receiptFullFlow({ productName, demand: "10" });

        await inventoryPage.partialReturnAndExpectReversedLocations(productName, "4");
    });

    /**
     * A delivery's return reverses its source and destination locations.
     */
    test("Return delivery reverses its locations", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Return Delivery ${key}`;

        await inventoryPage.createInventoryProduct({ name: productName, price: "20" });
        await inventoryPage.receiptFullFlow({ productName, demand: "10" });
        await inventoryPage.deliveryFullFlow({ productName, demand: "10" });

        await inventoryPage.returnAndExpectReversedLocations();
    });

    /**
     * An internal transfer's return reverses its source and destination locations.
     */
    test("Return internal transfer reverses its locations", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const productName = `E2E Return Internal ${key}`;

        await inventoryPage.createInventoryProduct({ name: productName, price: "20" });
        await inventoryPage.receiptFullFlow({ productName, demand: "10" });
        await inventoryPage.internalTransferFullFlow({ productName, demand: "10" });

        await inventoryPage.returnAndExpectReversedLocations();
    });
});

/**
 * Back orders on a 3-step reception warehouse: receiving fewer units than
 * demanded leaves a remainder, and the operation type's create-backorder policy
 * decides whether a follow-up (back order) receipt is created for it.
 */
test.describe("Inventory Operations - Backorders", () => {
    test.beforeAll(async ({ adminPage }) => {
        await enableOperationsSettings(adminPage);
    });

    /**
     * Ask (the warehouse default): a partial receipt opens the "Create Back
     * Order?" modal; confirming it creates a second receipt for the remainder.
     */
    test("Backorder - Ask creates a backorder", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouse = { name: `WH BO Ask ${key}`, code: `BOA${key}`, receptionStep: 3 as const, deliveryStep: 1 as const };
        const product = `E2E BO Ask Prod ${key}`;
        const origin = `E2E-BO-ASK-${key}`;

        await inventoryPage.createWarehouse(warehouse);
        await inventoryPage.createInventoryProduct({ name: product, price: "10" });
        await inventoryPage.createReceipt({ operationType: warehouse.name, productName: product, demand: "10", origin });

        await inventoryPage.receivePartialWithBackorder("6", "ask");

        await inventoryPage.expectReceiptCountByOrigin(origin, 2);
    });

    /**
     * Never: a partial receipt validates straight through with no modal and no
     * back order for the remainder.
     */
    test("Backorder - Never skips the backorder", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouse = { name: `WH BO Never ${key}`, code: `BON${key}`, receptionStep: 3 as const, deliveryStep: 1 as const };
        const product = `E2E BO Never Prod ${key}`;
        const origin = `E2E-BO-NEVER-${key}`;

        await inventoryPage.createWarehouse(warehouse);
        await inventoryPage.editOperationTypeBackorderForWarehouse(warehouse.name, "never");
        await inventoryPage.createInventoryProduct({ name: product, price: "10" });
        await inventoryPage.createReceipt({ operationType: warehouse.name, productName: product, demand: "10", origin });

        await inventoryPage.receivePartialWithBackorder("6", "never");

        await inventoryPage.expectReceiptCountByOrigin(origin, 1);
    });

    /**
     * Always: a partial receipt validates with no modal but still creates the
     * back order for the remainder automatically.
     */
    test("Backorder - Always creates a backorder without asking", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouse = { name: `WH BO Always ${key}`, code: `BOL${key}`, receptionStep: 3 as const, deliveryStep: 1 as const };
        const product = `E2E BO Always Prod ${key}`;
        const origin = `E2E-BO-ALWAYS-${key}`;

        await inventoryPage.createWarehouse(warehouse);
        await inventoryPage.editOperationTypeBackorderForWarehouse(warehouse.name, "always");
        await inventoryPage.createInventoryProduct({ name: product, price: "10" });
        await inventoryPage.createReceipt({ operationType: warehouse.name, productName: product, demand: "10", origin });

        await inventoryPage.receivePartialWithBackorder("6", "always");

        await inventoryPage.expectReceiptCountByOrigin(origin, 2);
    });
});
