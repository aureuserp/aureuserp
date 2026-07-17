import { test } from "../../setup";
import { InventoriesManagementPage } from "../../pages/06_inventoriesManagement";

/**
 * Warehouse step-configuration auto-creation, verified by record presence (not brittle counts).
 */
test.describe("Inventory Warehouse", () => {
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
    test("Listing Page", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoWarehousesPage();
    });

    /**
     * A one-step warehouse creates only the single-step records.
     */
    test("Create - One Step (Receive / Deliver)", async ({ adminPage }) => {
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
    test("Create - Two Steps (Receive Then Store / Pick Then Deliver)", async ({ adminPage }) => {
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
    test("Create - Three Steps (Receive, QC, Store / Pick, Pack, Deliver)", async ({ adminPage }) => {
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
    test("Create - Asymmetric Steps (Two-Step Receive / One-Step Deliver)", async ({ adminPage }) => {
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
    test("Switch From One Step To Three Steps", async ({ adminPage }) => {
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

/**
 * Location configuration resource CRUD.
 */
test.describe("Inventory Location", () => {
    /**
     * Locations must be enabled to expose the location and scrap-form selects.
     */
    test.beforeAll(async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.ensureBaseDependentPluginsInstalled();
        await inventoryPage.enableManageWarehousesToggles();
    });

    /**
     * The Locations listing table renders.
     */
    test("Listing Page", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.gotoLocationsPage();
    });

    /**
     * A location can be created with just a name.
     */
    test("Create Location", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const name = `E2E Location ${Date.now()}`;

        await inventoryPage.createLocation(name);
        await inventoryPage.gotoLocationsPage();
        await inventoryPage.expectListContains(name);
    });

    /**
     * A location can be deleted from its row.
     */
    test("Delete Location", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const name = `E2E Location Del ${Date.now()}`;

        await inventoryPage.createLocation(name);
        await inventoryPage.deleteLocation(name);
    });

    /**
     * Vendor Location type persists.
     */
    test("Type - Vendor Location", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);

        await inventoryPage.createLocationOfType(`E2E Loc Vendor ${Date.now()}`, "supplier");
        await inventoryPage.expectInfolistField("Location Type", "Vendor Location");
    });

    /**
     * View type persists.
     */
    test("Type - View", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);

        await inventoryPage.createLocationOfType(`E2E Loc View ${Date.now()}`, "view");
        await inventoryPage.expectInfolistField("Location Type", "View");
    });

    /**
     * Internal Location type persists.
     */
    test("Type - Internal Location", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);

        await inventoryPage.createLocationOfType(`E2E Loc Internal ${Date.now()}`, "internal");
        await inventoryPage.expectInfolistField("Location Type", "Internal Location");
    });

    /**
     * Customer Location type persists.
     */
    test("Type - Customer Location", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);

        await inventoryPage.createLocationOfType(`E2E Loc Customer ${Date.now()}`, "customer");
        await inventoryPage.expectInfolistField("Location Type", "Customer Location");
    });

    /**
     * Inventory Loss type persists.
     */
    test("Type - Inventory Loss", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);

        await inventoryPage.createLocationOfType(`E2E Loc Inventory ${Date.now()}`, "inventory");
        await inventoryPage.expectInfolistField("Location Type", "Inventory Loss");
    });

    /**
     * Production type persists.
     */
    test("Type - Production", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);

        await inventoryPage.createLocationOfType(`E2E Loc Production ${Date.now()}`, "production");
        await inventoryPage.expectInfolistField("Location Type", "Production");
    });

    /**
     * Transit Location type persists.
     */
    test("Type - Transit Location", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);

        await inventoryPage.createLocationOfType(`E2E Loc Transit ${Date.now()}`, "transit");
        await inventoryPage.expectInfolistField("Location Type", "Transit Location");
    });

    /**
     * A scrap location is selectable as the Scrap Location in a scrap operation.
     */
    test("Scrap location usable in scrap", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const scrapLocation = `E2E Scrap Loc ${key}`;
        const product = `E2E Scrap Product ${key}`;

        await inventoryPage.createScrapLocation(scrapLocation);
        await inventoryPage.createInventoryProduct({ name: product, price: "10" });

        await inventoryPage.createScrapAtLocation(product, "1", scrapLocation);
        await inventoryPage.gotoCurrentOperationView();
        await inventoryPage.expectInfolistField("Destination Location", scrapLocation);
    });

    /**
     * A child location keeps its selected parent.
     */
    test("Parent location persists", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const parent = `E2E Parent ${key}`;
        const child = `E2E Child ${key}`;

        await inventoryPage.createLocation(parent);
        await inventoryPage.createLocationWithParent(child, parent);
        await inventoryPage.expectInfolistField("Parent Location", parent);
    });

    /**
     * A location holding stock cannot be deleted.
     */
    test("Delete blocked when location holds stock", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const location = `E2E Loc Stock ${key}`;
        const product = `E2E Loc Product ${key}`;

        await inventoryPage.createLocation(location);
        await inventoryPage.createInventoryProduct({ name: product, price: "10" });
        await inventoryPage.addOnHandQuantity(product, location, "5");
        await inventoryPage.deleteLocationExpectingBlocked(location);
    });
});

/**
 * Operation-type create-form fields, a real stock transfer, and the dropship
 * setting gate — driven end-to-end and verified on the saved record.
 */
test.describe("Inventory Operation Type", () => {
    /**
     * Locations, dropshipping, and traceability must be enabled for the operation
     * type's source/destination, the Dropship type, and the Lots/Serial toggles.
     */
    test.beforeAll(async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.ensureBaseDependentPluginsInstalled();
        await inventoryPage.enableAllInventorySettings();
    });

    /**
     * Receipt type persists.
     */
    test("Type - Receipt", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const source = `E2E OpSrc ${key}`;
        const destination = `E2E OpDst ${key}`;

        await inventoryPage.createLocation(source);
        await inventoryPage.createLocation(destination);
        await inventoryPage.createOperationTypeWithFlow({
            name: `E2E OpType Receipt ${key}`,
            sequenceCode: "E2ER",
            type: "incoming",
            sourceLocation: source,
            destinationLocation: destination,
        });
        await inventoryPage.expectInfolistField("Operation Type", "Receipt");
    });

    /**
     * Delivery type persists.
     */
    test("Type - Delivery", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const source = `E2E OpSrc ${key}`;
        const destination = `E2E OpDst ${key}`;

        await inventoryPage.createLocation(source);
        await inventoryPage.createLocation(destination);
        await inventoryPage.createOperationTypeWithFlow({
            name: `E2E OpType Delivery ${key}`,
            sequenceCode: "E2ED",
            type: "outgoing",
            sourceLocation: source,
            destinationLocation: destination,
        });
        await inventoryPage.expectInfolistField("Operation Type", "Delivery");
    });

    /**
     * Internal type persists.
     */
    test("Type - Internal", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const source = `E2E OpSrc ${key}`;
        const destination = `E2E OpDst ${key}`;

        await inventoryPage.createLocation(source);
        await inventoryPage.createLocation(destination);
        await inventoryPage.createOperationTypeWithFlow({
            name: `E2E OpType Internal ${key}`,
            sequenceCode: "E2EI",
            type: "internal",
            sourceLocation: source,
            destinationLocation: destination,
        });
        await inventoryPage.expectInfolistField("Operation Type", "Internal");
    });

    /**
     * Dropship type persists (requires the dropshipping setting enabled).
     */
    test("Type - Dropship", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const source = `E2E OpSrc ${key}`;
        const destination = `E2E OpDst ${key}`;

        await inventoryPage.createLocation(source);
        await inventoryPage.createLocation(destination);
        await inventoryPage.createOperationTypeWithFlow({
            name: `E2E OpType Dropship ${key}`,
            sequenceCode: "E2EDS",
            type: "dropship",
            sourceLocation: source,
            destinationLocation: destination,
        });
        await inventoryPage.expectInfolistField("Operation Type", "Dropship");
    });

    /**
     * Lots/Serial - the "Create New" option persists (needs traceability enabled).
     */
    test("Lots - Create New persists", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const source = `E2E OpSrc ${key}`;
        const destination = `E2E OpDst ${key}`;

        await inventoryPage.createLocation(source);
        await inventoryPage.createLocation(destination);
        await inventoryPage.createOperationTypeWithFlow({
            name: `E2E OpType Lots New ${key}`,
            sequenceCode: "E2ELN",
            type: "incoming",
            sourceLocation: source,
            destinationLocation: destination,
            lots: "create",
        });
        await inventoryPage.expectOperationTypeLots("create");
    });

    /**
     * Lots/Serial - the "Use Existing" option persists (needs traceability enabled).
     */
    test("Lots - Use Existing persists", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const source = `E2E OpSrc ${key}`;
        const destination = `E2E OpDst ${key}`;

        await inventoryPage.createLocation(source);
        await inventoryPage.createLocation(destination);
        await inventoryPage.createOperationTypeWithFlow({
            name: `E2E OpType Lots Existing ${key}`,
            sequenceCode: "E2ELE",
            type: "incoming",
            sourceLocation: source,
            destinationLocation: destination,
            lots: "existing",
        });
        await inventoryPage.expectOperationTypeLots("existing");
    });

    /**
     * The Return Type accepts another operation type.
     */
    test("Return type from another operation type", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const source = `E2E OpSrc ${key}`;
        const destination = `E2E OpDst ${key}`;
        const returnType = `E2E OpType Base ${key}`;

        await inventoryPage.createLocation(source);
        await inventoryPage.createLocation(destination);
        await inventoryPage.createOperationTypeWithFlow({
            name: returnType,
            sequenceCode: "E2EBS",
            type: "incoming",
            sourceLocation: source,
            destinationLocation: destination,
        });
        await inventoryPage.createOperationTypeWithFlow({
            name: `E2E OpType Return ${key}`,
            sequenceCode: "E2ERT",
            type: "incoming",
            sourceLocation: source,
            destinationLocation: destination,
            returnTypeName: returnType,
        });
        await inventoryPage.expectInfolistField("Return Operation Type", returnType);
    });

    /**
     * The create-backorder policy persists as chosen.
     */
    test("Create backorder policy persists", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const source = `E2E OpSrc ${key}`;
        const destination = `E2E OpDst ${key}`;

        await inventoryPage.createLocation(source);
        await inventoryPage.createLocation(destination);
        await inventoryPage.createOperationTypeWithFlow({
            name: `E2E OpType Backorder ${key}`,
            sequenceCode: "E2EB",
            type: "incoming",
            sourceLocation: source,
            destinationLocation: destination,
            createBackorder: "never",
        });
        await inventoryPage.expectInfolistField("Create Backorder", "Never");
    });

    /**
     * A receipt using a custom operation type lands the product at its destination.
     */
    test("Product moves through a custom operation type", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const destination = `E2E Move Dst ${key}`;
        const operationType = `E2E Move OpType ${key}`;
        const product = `E2E Move Product ${key}`;

        await inventoryPage.createLocation(destination);
        await inventoryPage.createOperationTypeWithFlow({
            name: operationType,
            sequenceCode: "E2EMV",
            type: "incoming",
            destinationLocation: destination,
        });
        await inventoryPage.createInventoryProduct({ name: product, price: "10" });

        await inventoryPage.createReceipt({
            productName: product,
            demand: "7",
            operationTypeName: operationType,
        });
        await inventoryPage.confirmAndValidateOperation();

        await inventoryPage.expectOnHandQuantityRow(product, destination, "7");
    });

    /**
     * Returning a validated operation uses the operation type's configured return
     * type and reverses its locations (source = original destination, destination
     * = the incoming return type's destination).
     */
    test("Return uses the custom return type", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const returnDest = `E2E Ret Dest ${key}`;
        const mainDest = `E2E Main Dest ${key}`;
        const returnType = `E2E Ret Type ${key}`;
        const mainType = `E2E Main Type ${key}`;
        const product = `E2E Ret Product ${key}`;

        await inventoryPage.createLocation(returnDest);
        await inventoryPage.createOperationTypeWithFlow({
            name: returnType,
            sequenceCode: "E2ERA",
            type: "incoming",
            destinationLocation: returnDest,
        });

        await inventoryPage.createLocation(mainDest);
        await inventoryPage.createOperationTypeWithFlow({
            name: mainType,
            sequenceCode: "E2ERB",
            type: "incoming",
            destinationLocation: mainDest,
            returnTypeName: returnType,
        });

        await inventoryPage.createInventoryProduct({ name: product, price: "10" });
        await inventoryPage.createReceipt({
            productName: product,
            demand: "4",
            operationTypeName: mainType,
        });
        await inventoryPage.confirmAndValidateOperation();

        await inventoryPage.returnCurrentOperation();
        await inventoryPage.gotoCurrentOperationView();
        await inventoryPage.expectInfolistField("Operation Type", returnType);
        await inventoryPage.expectInfolistField("Source Location", mainDest);
        await inventoryPage.expectInfolistField("Destination Location", returnDest);
    });

    /**
     * Ask backorder policy: validating a partial delivery opens the modal, and
     * confirming it creates a backorder for the remaining quantity.
     */
    test("Create backorder - Ask creates a backorder", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const source = `E2E BO Src ${key}`;
        const product = `E2E BO Product ${key}`;
        const opType = `E2E BO Ask Type ${key}`;
        const origin = `E2E BO Ask ${key}`;

        await inventoryPage.createLocation(source);
        await inventoryPage.createInventoryProduct({ name: product, price: "10" });
        await inventoryPage.addOnHandQuantity(product, source, "5");

        await inventoryPage.createOperationTypeWithFlow({
            name: opType,
            sequenceCode: "E2EBA",
            type: "outgoing",
            sourceLocation: source,
            createBackorder: "ask",
        });

        await inventoryPage.createDelivery({
            productName: product,
            demand: "10",
            operationTypeName: opType,
            origin,
        });
        await inventoryPage.validateCreatingBackorder();

        await inventoryPage.expectDeliveryCountByOrigin(origin, 2);
    });

    /**
     * Never backorder policy: validating a partial delivery shows no modal and
     * creates no backorder, so only the original delivery exists.
     */
    test("Create backorder - Never skips the backorder", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const source = `E2E BO Src ${key}`;
        const product = `E2E BO Product ${key}`;
        const opType = `E2E BO Never Type ${key}`;
        const origin = `E2E BO Never ${key}`;

        await inventoryPage.createLocation(source);
        await inventoryPage.createInventoryProduct({ name: product, price: "10" });
        await inventoryPage.addOnHandQuantity(product, source, "5");

        await inventoryPage.createOperationTypeWithFlow({
            name: opType,
            sequenceCode: "E2EBN",
            type: "outgoing",
            sourceLocation: source,
            createBackorder: "never",
        });

        await inventoryPage.createDelivery({
            productName: product,
            demand: "10",
            operationTypeName: opType,
            origin,
        });
        await inventoryPage.validateWithoutBackorderModal();

        await inventoryPage.expectDeliveryCountByOrigin(origin, 1);
    });

    /**
     * Always backorder policy: validating a partial delivery shows no modal and
     * still creates a backorder for the remaining quantity.
     */
    test("Create backorder - Always creates a backorder without asking", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const source = `E2E BO Src ${key}`;
        const product = `E2E BO Product ${key}`;
        const opType = `E2E BO Always Type ${key}`;
        const origin = `E2E BO Always ${key}`;

        await inventoryPage.createLocation(source);
        await inventoryPage.createInventoryProduct({ name: product, price: "10" });
        await inventoryPage.addOnHandQuantity(product, source, "5");

        await inventoryPage.createOperationTypeWithFlow({
            name: opType,
            sequenceCode: "E2EBW",
            type: "outgoing",
            sourceLocation: source,
            createBackorder: "always",
        });

        await inventoryPage.createDelivery({
            productName: product,
            demand: "10",
            operationTypeName: opType,
            origin,
        });
        await inventoryPage.validateWithoutBackorderModal();

        await inventoryPage.expectDeliveryCountByOrigin(origin, 2);
    });

    /**
     * At Confirm reservation: confirming the delivery (Mark as Todo) reserves the
     * stock, shown as Reserved Quantity on the product's Quantities tab.
     */
    test("Reservation - At Confirm reserves on confirm", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const source = `E2E Rsv Src ${key}`;
        const product = `E2E Rsv Product ${key}`;
        const opType = `E2E Rsv AtConfirm ${key}`;

        await inventoryPage.createLocation(source);
        await inventoryPage.createInventoryProduct({ name: product, price: "10" });
        await inventoryPage.addOnHandQuantity(product, source, "10");

        await inventoryPage.createOperationTypeWithFlow({
            name: opType,
            sequenceCode: "E2ERC",
            type: "outgoing",
            sourceLocation: source,
        });

        await inventoryPage.createDelivery({
            productName: product,
            demand: "5",
            operationTypeName: opType,
        });
        await inventoryPage.markAsTodo();

        await inventoryPage.expectReservedQuantityRow(product, source, "5");
    });

    /**
     * Manual reservation: the Manual radio must persist, confirming reserves
     * nothing, and only Check Availability reserves the stock. Fails until the
     * app persists the Manual radio (it currently saves At Confirm).
     */
    test("Reservation - Manual reserves on Check Availability", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const source = `E2E Rsv Src ${key}`;
        const product = `E2E Rsv Product ${key}`;
        const opType = `E2E Rsv Manual ${key}`;

        await inventoryPage.createLocation(source);
        await inventoryPage.createInventoryProduct({ name: product, price: "10" });
        await inventoryPage.addOnHandQuantity(product, source, "10");

        await inventoryPage.createOperationTypeWithFlow({
            name: opType,
            sequenceCode: "E2ERM",
            type: "outgoing",
            sourceLocation: source,
            reservation: "manual",
        });
        await inventoryPage.expectInfolistField("Reservation Method", "Manual", 5000);

        await inventoryPage.createDelivery({
            productName: product,
            demand: "5",
            operationTypeName: opType,
        });
        const deliveryUrl = adminPage.url();

        await inventoryPage.markAsTodo();
        await inventoryPage.expectCheckAvailabilityVisible();
        await inventoryPage.expectReservedQuantityRow(product, source, "0");

        await adminPage.goto(deliveryUrl);
        await inventoryPage.checkAvailability();
        await inventoryPage.expectReservedQuantityRow(product, source, "5");
    });

    /**
     * With dropshipping off, the type field must not offer Dropship (fails until
     * the app gates the option — the type select currently always lists it).
     */
    test("Dropship type hidden when dropshipping off", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);

        await inventoryPage.disableDropshipping();
        await inventoryPage.gotoOperationTypeCreatePage();
        await inventoryPage.expectOperationTypeOptionAbsent("dropship");
    });

    /**
     * The following scenarios exercise the operation type's Lots/Serial toggles
     * (Create New / Use Existing) on serial-tracked products. They only apply to
     * receipts (vendor source); deliveries pick an existing serial from stock.
     */

    /**
     * Scenario 1 - Receipt, Create New ON / Use Existing OFF: a brand-new serial
     * is created on receipt and the operation validates.
     */
    test("Receipt - Create New serial", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const product = `E2E Serial Prod ${key}`;
        const opType = `E2E Rcpt CreateNew ${key}`;
        const serial = `SN-CN-${key}`;

        await inventoryPage.createInventoryProduct({ name: product, price: "10", tracking: "serial" });
        await inventoryPage.createOperationTypeWithFlow({
            name: opType,
            sequenceCode: "E2ESCN",
            type: "incoming",
            lots: "create",
        });
        await inventoryPage.receiptSerialGenerateNew(opType, product, serial, "1");
        await inventoryPage.expectOperationDone();
        await inventoryPage.expectLotListed(serial);
    });

    /**
     * Scenario 2 - Receipt, Create New OFF / Use Existing ON: only an existing
     * serial can be selected; no new serial is created (the serial stays unique).
     */
    test("Receipt - Use Existing serial", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const product = `E2E Serial Prod ${key}`;
        const seedType = `E2E Rcpt Seed ${key}`;
        const existingType = `E2E Rcpt UseExisting ${key}`;
        const serial = `SN-EX-${key}`;

        await inventoryPage.createInventoryProduct({ name: product, price: "10", tracking: "serial" });
        await inventoryPage.createOperationTypeWithFlow({ name: seedType, sequenceCode: "E2ESD", type: "incoming", lots: "create" });
        await inventoryPage.receiptSerialGenerateNew(seedType, product, serial, "1");

        await inventoryPage.createOperationTypeWithFlow({ name: existingType, sequenceCode: "E2ESE", type: "incoming", lots: "existing" });
        await inventoryPage.openSerialReceiptLinesModal(existingType, product, "1");
        await inventoryPage.expectGenerateSerialAction(false);
        await inventoryPage.expectExistingSerialOption(serial);
    });

    /**
     * Scenario 3 - Receipt, Create New ON / Use Existing ON: the move line offers
     * both paths - the Generate (create-new) action and the existing serial in
     * the Lot/Serial dropdown.
     */
    test("Receipt - Create New and Use Existing both", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const product = `E2E Serial Prod ${key}`;
        const seedType = `E2E Rcpt Seed ${key}`;
        const bothType = `E2E Rcpt Both ${key}`;
        const serial = `SN-BO-${key}`;

        await inventoryPage.createInventoryProduct({ name: product, price: "10", tracking: "serial" });
        await inventoryPage.createOperationTypeWithFlow({ name: seedType, sequenceCode: "E2ESD3", type: "incoming", lots: "create" });
        await inventoryPage.receiptSerialGenerateNew(seedType, product, serial, "1");

        await inventoryPage.createOperationTypeWithFlow({ name: bothType, sequenceCode: "E2ESB", type: "incoming", lots: "both" });
        await inventoryPage.openSerialReceiptLinesModal(bothType, product, "1");
        await inventoryPage.expectGenerateSerialAction(true);
        await inventoryPage.expectExistingSerialOption(serial);
    });

    /**
     * Scenario 4 - Receipt, Create New OFF / Use Existing OFF: no serial can be
     * assigned (no lot column, no Generate action). The receipt still validates
     * to Done and records no lot for the product (the app does not block it).
     */
    test("Receipt - No lot options when both off", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const product = `E2E Serial Prod ${key}`;
        const opType = `E2E Rcpt NoLots ${key}`;

        await inventoryPage.createInventoryProduct({ name: product, price: "10", tracking: "serial" });
        await inventoryPage.createOperationTypeWithFlow({ name: opType, sequenceCode: "E2ESN", type: "incoming" });
        await inventoryPage.openSerialReceiptLinesModal(opType, product, "1");
        await inventoryPage.expectGenerateSerialAction(false);
        await inventoryPage.expectSerialLotColumnAbsent();
        await adminPage.keyboard.press("Escape");
        await inventoryPage.confirmAndValidateOperation();
        await inventoryPage.expectOperationDone();
        await inventoryPage.expectNoLotForProduct(product);
    });

    /**
     * Scenario 6 - Delivery, Create New OFF / Use Existing ON: an existing serial
     * from stock is delivered and the operation validates. (Deliveries draw the
     * serial from stock; the lot toggles do not gate them.)
     */
    test("Delivery - Use Existing serial", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const product = `E2E Serial Prod ${key}`;
        const seedType = `E2E Rcpt Seed ${key}`;
        const outType = `E2E Dlv UseExisting ${key}`;
        const serial = `SN-DE-${key}`;

        await inventoryPage.createInventoryProduct({ name: product, price: "10", tracking: "serial" });
        await inventoryPage.createOperationTypeWithFlow({ name: seedType, sequenceCode: "E2EDSD", type: "incoming", lots: "create" });
        await inventoryPage.receiptSerialGenerateNew(seedType, product, serial, "1");

        await inventoryPage.createOperationTypeWithFlow({ name: outType, sequenceCode: "E2EDUE", type: "outgoing", lots: "existing" });
        await inventoryPage.deliverSerialFullFlow(outType, product, "1");
        await inventoryPage.expectOperationDone();
    });

    /**
     * Scenario 5 - Delivery, Create New ON / Use Existing OFF: the delivery still
     * draws the existing serial from stock (no new serial is created on delivery)
     * and validates. The Create New toggle has no effect on deliveries.
     */
    test("Delivery - Create New does not add serial", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const product = `E2E Serial Prod ${key}`;
        const seedType = `E2E Rcpt Seed ${key}`;
        const outType = `E2E Dlv CreateNew ${key}`;
        const serial = `SN-DC-${key}`;

        await inventoryPage.createInventoryProduct({ name: product, price: "10", tracking: "serial" });
        await inventoryPage.createOperationTypeWithFlow({ name: seedType, sequenceCode: "E2EDSC", type: "incoming", lots: "create" });
        await inventoryPage.receiptSerialGenerateNew(seedType, product, serial, "1");

        await inventoryPage.createOperationTypeWithFlow({ name: outType, sequenceCode: "E2EDCN", type: "outgoing", lots: "create" });
        await inventoryPage.deliverSerialFullFlow(outType, product, "1");
        await inventoryPage.expectOperationDone();
        await inventoryPage.expectLotListed(serial);
    });

    /**
     * Scenario 7 - Delivery, Create New ON / Use Existing ON: the existing serial
     * is delivered from stock and validates (both toggles inert on deliveries).
     */
    test("Delivery - Both toggles on", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const product = `E2E Serial Prod ${key}`;
        const seedType = `E2E Rcpt Seed ${key}`;
        const outType = `E2E Dlv Both ${key}`;
        const serial = `SN-DB-${key}`;

        await inventoryPage.createInventoryProduct({ name: product, price: "10", tracking: "serial" });
        await inventoryPage.createOperationTypeWithFlow({ name: seedType, sequenceCode: "E2EDSB", type: "incoming", lots: "create" });
        await inventoryPage.receiptSerialGenerateNew(seedType, product, serial, "1");

        await inventoryPage.createOperationTypeWithFlow({ name: outType, sequenceCode: "E2EDBO", type: "outgoing", lots: "both" });
        await inventoryPage.deliverSerialFullFlow(outType, product, "1");
        await inventoryPage.expectOperationDone();
    });

    /**
     * Scenario 8 - Delivery, Create New OFF / Use Existing OFF: the delivery still
     * draws the existing serial from stock and validates - the lot toggles do not
     * block a delivery (the serial comes from reserved stock, not the toggles).
     */
    test("Delivery - Both toggles off still delivers", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const product = `E2E Serial Prod ${key}`;
        const seedType = `E2E Rcpt Seed ${key}`;
        const outType = `E2E Dlv NoLots ${key}`;
        const serial = `SN-DN-${key}`;

        await inventoryPage.createInventoryProduct({ name: product, price: "10", tracking: "serial" });
        await inventoryPage.createOperationTypeWithFlow({ name: seedType, sequenceCode: "E2EDSN", type: "incoming", lots: "create" });
        await inventoryPage.receiptSerialGenerateNew(seedType, product, serial, "1");

        await inventoryPage.createOperationTypeWithFlow({ name: outType, sequenceCode: "E2EDNL", type: "outgoing" });
        await inventoryPage.deliverSerialFullFlow(outType, product, "1");
        await inventoryPage.expectOperationDone();
    });
});

/**
 * Putaway rules redirect a product arriving in a warehouse's Stock to a
 * configured sub-location. Verified end-to-end through a 3-step reception: the
 * final Store transfer lands in Stock, the rule fires on validation, and the
 * product's on-hand quantity ends up in the sub-location.
 */
test.describe("Inventory Putaway Rules", () => {
    test.beforeAll(async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        await inventoryPage.ensureBaseDependentPluginsInstalled();
        await inventoryPage.enableAllInventorySettings();
    });

    /**
     * Scenario 1 - a product-specific rule routes a 3-step reception's stock into
     * a shelf sub-location carrying a storage category.
     */
    test("Putaway - product routed to shelf sub-location", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouse = { name: `WH Putaway A ${key}`, code: `WPA${key}`, receptionStep: 3 as const, deliveryStep: 1 as const };
        const stock = `${warehouse.code}/Stock`;
        const shelf = `Shelf A ${key}`;
        const storageCategory = `E2E StoreCat ${key}`;
        const product = `E2E Putaway Laptop ${key}`;

        await inventoryPage.createWarehouse(warehouse);
        await inventoryPage.createStorageCategory(storageCategory);
        await inventoryPage.createSubLocation(shelf, stock, storageCategory);
        await inventoryPage.createInventoryProduct({ name: product, price: "100" });
        await inventoryPage.createPutawayRule({ inLocation: stock, outLocation: shelf, productName: product, storageCategory });

        await inventoryPage.receiptChainFullFlow({ operationType: warehouse.name, productName: product, demand: "5" });
        await inventoryPage.expectOnHandQuantityRow(product, shelf, "5");
    });

    /**
     * Scenario 2 - a different product and shelf, larger quantity: the rule still
     * routes the whole received quantity into its sub-location.
     */
    test("Putaway - second product routed to its own shelf", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouse = { name: `WH Putaway B ${key}`, code: `WPB${key}`, receptionStep: 3 as const, deliveryStep: 1 as const };
        const stock = `${warehouse.code}/Stock`;
        const shelf = `Shelf B ${key}`;
        const storageCategory = `E2E StoreCat B ${key}`;
        const product = `E2E Putaway Monitor ${key}`;

        await inventoryPage.createWarehouse(warehouse);
        await inventoryPage.createStorageCategory(storageCategory);
        await inventoryPage.createSubLocation(shelf, stock, storageCategory);
        await inventoryPage.createInventoryProduct({ name: product, price: "50" });
        await inventoryPage.createPutawayRule({ inLocation: stock, outLocation: shelf, productName: product, storageCategory });

        await inventoryPage.receiptChainFullFlow({ operationType: warehouse.name, productName: product, demand: "12" });
        await inventoryPage.expectOnHandQuantityRow(product, shelf, "12");
    });

    /**
     * Scenario 3 - a product-only rule (no storage category on the shelf) still
     * redirects the arriving product to its sub-location on validation.
     */
    test("Putaway - product-only rule without storage category", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouse = { name: `WH Putaway C ${key}`, code: `WPC${key}`, receptionStep: 3 as const, deliveryStep: 1 as const };
        const stock = `${warehouse.code}/Stock`;
        const shelf = `Shelf C ${key}`;
        const product = `E2E Putaway Keyboard ${key}`;

        await inventoryPage.createWarehouse(warehouse);
        await inventoryPage.createSubLocation(shelf, stock);
        await inventoryPage.createInventoryProduct({ name: product, price: "20" });
        await inventoryPage.createPutawayRule({ inLocation: stock, outLocation: shelf, productName: product });

        await inventoryPage.receiptChainFullFlow({ operationType: warehouse.name, productName: product, demand: "8" });
        await inventoryPage.expectOnHandQuantityRow(product, shelf, "8");
    });

    /**
     * Scenario 4 - a lot-tracked product: the lot is created at receipt and the
     * whole lot quantity is put away into the shelf sub-location.
     */
    test("Putaway - lot-tracked product routed to shelf", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouse = { name: `WH Putaway L ${key}`, code: `WPL${key}`, receptionStep: 3 as const, deliveryStep: 1 as const };
        const stock = `${warehouse.code}/Stock`;
        const shelf = `Shelf L ${key}`;
        const storageCategory = `E2E StoreCat L ${key}`;
        const product = `E2E Putaway LotProd ${key}`;
        const lot = `LOT-${key}`;

        await inventoryPage.createWarehouse(warehouse);
        await inventoryPage.createStorageCategory(storageCategory);
        await inventoryPage.createSubLocation(shelf, stock, storageCategory);
        await inventoryPage.createInventoryProduct({ name: product, price: "40", tracking: "lot" });
        await inventoryPage.createPutawayRule({ inLocation: stock, outLocation: shelf, productName: product, storageCategory });

        await inventoryPage.receiptWithLotChainFlow({ operationType: warehouse.name, productName: product, demand: "6" }, lot);
        await inventoryPage.expectOnHandQuantityRow(product, shelf, "6");
    });

    /**
     * Scenario 5 - a serial-tracked product: a serial is created at receipt and
     * the unit is put away into the shelf sub-location.
     */
    test("Putaway - serial-tracked product routed to shelf", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouse = { name: `WH Putaway S ${key}`, code: `WPS${key}`, receptionStep: 3 as const, deliveryStep: 1 as const };
        const stock = `${warehouse.code}/Stock`;
        const shelf = `Shelf S ${key}`;
        const storageCategory = `E2E StoreCat S ${key}`;
        const product = `E2E Putaway SerialProd ${key}`;
        const serial = `SN-${key}`;

        await inventoryPage.createWarehouse(warehouse);
        await inventoryPage.createStorageCategory(storageCategory);
        await inventoryPage.createSubLocation(shelf, stock, storageCategory);
        await inventoryPage.createInventoryProduct({ name: product, price: "80", tracking: "serial" });
        await inventoryPage.createPutawayRule({ inLocation: stock, outLocation: shelf, productName: product, storageCategory });

        await inventoryPage.receiptWithLotChainFlow({ operationType: warehouse.name, productName: product, demand: "4" }, serial);
        await inventoryPage.expectOnHandQuantityRow(product, shelf, "1");
    });

    /**
     * Scenario 6 - one receipt with three products of different tracking
     * (quantity, lot, serial), each with its own putaway rule and shelf: every
     * product ends up in its own sub-location with the expected on-hand.
     */
    test("Putaway - multiple products with different tracking", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouse = { name: `WH Putaway M ${key}`, code: `WPM${key}`, receptionStep: 3 as const, deliveryStep: 1 as const };
        const stock = `${warehouse.code}/Stock`;
        const shelfQty = `Shelf QM ${key}`;
        const shelfLot = `Shelf LM ${key}`;
        const shelfSerial = `Shelf SM ${key}`;
        const productQty = `E2E Multi Qty ${key}`;
        const productLot = `E2E Multi Lot ${key}`;
        const productSerial = `E2E Multi Serial ${key}`;
        const lot = `MLOT-${key}`;
        const serial = `MSN-${key}`;

        await inventoryPage.createWarehouse(warehouse);
        await inventoryPage.createSubLocation(shelfQty, stock);
        await inventoryPage.createSubLocation(shelfLot, stock);
        await inventoryPage.createSubLocation(shelfSerial, stock);
        await inventoryPage.createInventoryProduct({ name: productQty, price: "10" });
        await inventoryPage.createInventoryProduct({ name: productLot, price: "20", tracking: "lot" });
        await inventoryPage.createInventoryProduct({ name: productSerial, price: "30", tracking: "serial" });
        await inventoryPage.createPutawayRule({ inLocation: stock, outLocation: shelfQty, productName: productQty });
        await inventoryPage.createPutawayRule({ inLocation: stock, outLocation: shelfLot, productName: productLot });
        await inventoryPage.createPutawayRule({ inLocation: stock, outLocation: shelfSerial, productName: productSerial });

        await inventoryPage.receiptLinesChainFlow(
            [
                { productName: productQty, demand: "4" },
                { productName: productLot, demand: "6", lotName: lot },
                { productName: productSerial, demand: "1", lotName: serial },
            ],
            warehouse.name,
        );

        await inventoryPage.expectOnHandQuantityRow(productQty, shelfQty, "4");
        await inventoryPage.expectOnHandQuantityRow(productLot, shelfLot, "6");
        await inventoryPage.expectOnHandQuantityRow(productSerial, shelfSerial, "1");
    });

    /**
     * Scenario 7 - after putaway stores the product in its shelf, a delivery ships
     * part of it: the delivery reserves from the shelf and the shelf's on-hand
     * drops by the delivered quantity.
     */
    test("Putaway - delivery ships from the shelf", async ({ adminPage }) => {
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();
        const warehouse = { name: `WH Putaway D ${key}`, code: `WPD${key}`, receptionStep: 3 as const, deliveryStep: 1 as const };
        const stock = `${warehouse.code}/Stock`;
        const shelf = `Shelf D ${key}`;
        const storageCategory = `E2E StoreCat D ${key}`;
        const product = `E2E Putaway DlvProd ${key}`;
        const outType = `E2E Dlv FromShelf ${key}`;

        await inventoryPage.createWarehouse(warehouse);
        await inventoryPage.createStorageCategory(storageCategory);
        await inventoryPage.createSubLocation(shelf, stock, storageCategory);
        await inventoryPage.createInventoryProduct({ name: product, price: "60" });
        await inventoryPage.createPutawayRule({ inLocation: stock, outLocation: shelf, productName: product, storageCategory });

        await inventoryPage.receiptChainFullFlow({ operationType: warehouse.name, productName: product, demand: "10" });
        await inventoryPage.expectOnHandQuantityRow(product, shelf, "10");

        await inventoryPage.createOperationTypeWithFlow({ name: outType, sequenceCode: "E2EPDL", type: "outgoing", sourceLocation: stock });
        await inventoryPage.createDelivery({ operationTypeName: outType, productName: product, demand: "4" });
        await inventoryPage.confirmAndValidateOperation();
        await inventoryPage.expectOperationDone();
        await inventoryPage.expectOnHandQuantityRow(product, shelf, "6");
    });
});
