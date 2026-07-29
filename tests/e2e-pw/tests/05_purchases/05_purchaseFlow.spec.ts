import { test } from "../../setup";
import { PurchaseFlowPage } from "../../pages/05_purchaseManagement";
import { InventoriesManagementPage } from "../../pages/06_inventoriesManagement";

/** Stock location of the seeded default warehouse, where purchase orders receive into. */
const DEFAULT_STOCK_LOCATION = "WH/Stock";

/** The only purchase tax the app seeds; it is applied on top of the untaxed amount. */
const PURCHASE_TAX_NAME = "15 %";

/**
 * Enable the inventory settings the purchase-to-inventory tests rely on: locations and
 * multi-step warehouses, traceability for lot/serial products, and operations for
 * packages. Each describe calls this from its own beforeAll so a shard that runs only a
 * subset of the describes still provisions what its tests need, keeping CI sharding and
 * fullyParallel runs order-independent.
 */
async function enablePurchaseInventorySettings(adminPage: import("@playwright/test").Page) {
    const purchasePage = new PurchaseFlowPage(adminPage);
    const inventoryPage = new InventoriesManagementPage(adminPage);

    await purchasePage.ensurePurchasesPluginInstalled();
    await inventoryPage.ensureBaseDependentPluginsInstalled();
    await inventoryPage.enableManageWarehousesToggles();
    await inventoryPage.enableManageTraceabilityToggles();
    await inventoryPage.enableManageOperationsToggles();
}

test.describe("Purchase Flow E2E", () => {
    test.beforeAll(async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        await purchasePage.ensurePurchasesPluginInstalled();
    });

    test("Purchase Flow - RFQ To Purchase Order", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E Purchase Vendor ${key}`;
        const productName = `E2E Purchase Product ${key}`;

        await purchasePage.createVendor({
            name: vendorName,
            email: `purchase.vendor+${key}@example.com`,
        });

        await purchasePage.createProduct({
            name: productName,
            price: "145",
        });

        await purchasePage.createQuotation({
            vendorName,
            productName,
            quantity: "2",
            unitPrice: "145",
        });

        await purchasePage.confirmCurrentQuotation();
        await purchasePage.expectPurchaseOrderVisible(vendorName);
    });

    test("Purchase Flow - Confirmed Agreement To Purchase Order", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E Blanket Vendor ${key}`;
        const productName = `E2E Blanket Product ${key}`;

        await purchasePage.setPurchaseAgreementsEnabled(true);

        await purchasePage.createVendor({
            name: vendorName,
            email: `purchase.blanket.vendor+${key}@example.com`,
        });

        await purchasePage.createProduct({
            name: productName,
            price: "210",
        });

        await purchasePage.createPurchaseAgreement({
            vendorName,
            productName,
            quantity: "8",
            unitPrice: "210",
            reference: `E2E-BLANKET-${key}`,
        });

        await purchasePage.confirmCurrentPurchaseAgreement();

        await purchasePage.createQuotationFromAgreement({
            vendorName,
            quantity: "3",
        });

        await purchasePage.confirmCurrentQuotation();
        await purchasePage.expectPurchaseOrderVisible(vendorName);
    });
});

test.describe("Purchase Flow - Inventory Integration", () => {
    test.beforeAll(async ({ adminPage }) => {
        await enablePurchaseInventorySettings(adminPage);
    });

    /**
     * Confirming a purchase order opens a receipt. Nothing can be billed until that
     * receipt is validated, after which the stock is on hand and the order is billable.
     */
    test("Purchase order - receive stock and create bill", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E PO Recv Vendor ${key}`;
        const productName = `E2E PO Recv Product ${key}`;

        await purchasePage.createVendor({ name: vendorName, email: `po.recv+${key}@example.com` });
        await purchasePage.createProduct({ name: productName, price: "100" });

        await purchasePage.createOrderWithLines({
            vendorName,
            lines: [{ productName, quantity: "5", unitPrice: "100" }],
        });
        await purchasePage.confirmCurrentQuotation();

        const orderRef = purchasePage.currentRecordRef();

        await purchasePage.expectReceiptCount(1);
        await purchasePage.expectReceiptState("/IN/", "Ready");

        // Nothing has been received, so the order has no invoiceable line yet.
        await purchasePage.openBillsForCurrentOrder();
        await purchasePage.expectNoBills();

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.openReceiptByIndex(0);
        await inventoryPage.confirmAndValidateOperation();
        await inventoryPage.expectOperationDone();

        await inventoryPage.expectOnHandQuantityRow(productName, DEFAULT_STOCK_LOCATION, "5");
        await inventoryPage.expectProductMoveRowVisible(productName, "Done");

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectReceiptState("/IN/", "Done");

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectReceivedQuantity(0, "5");
        await purchasePage.createBill();

        await purchasePage.openBillsForCurrentOrder();
        await purchasePage.expectBillRowPresent();
    });

    /**
     * A lot-tracked product is bought and received under a lot entered on the receipt.
     */
    test("Purchase order - lot tracked product", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E PO Lot Vendor ${key}`;
        const productName = `E2E PO Lot Product ${key}`;
        const lotName = `LOT-PO-${key}`;

        await purchasePage.createVendor({ name: vendorName, email: `po.lot+${key}@example.com` });
        await purchasePage.createProduct({ name: productName, price: "50", tracking: "lot" });

        await purchasePage.createOrderWithLines({
            vendorName,
            lines: [{ productName, quantity: "8", unitPrice: "50" }],
        });
        await purchasePage.confirmCurrentQuotation();

        const orderRef = purchasePage.currentRecordRef();

        await purchasePage.openReceiptByIndex(0);
        await inventoryPage.clickMarkAsTodoIfVisible();
        await inventoryPage.generateLotOnMove(lotName, "8");
        await inventoryPage.confirmAndValidateOperation();

        await inventoryPage.expectLotListed(lotName);
        await inventoryPage.expectOnHandQuantityRow(productName, DEFAULT_STOCK_LOCATION, "8");
        await inventoryPage.expectProductMoveRowVisible(productName, "Done");

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectReceivedQuantity(0, "8");
    });

    /**
     * A serial-tracked product is bought and received as individual serials.
     */
    test("Purchase order - serial tracked product", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E PO Serial Vendor ${key}`;
        const productName = `E2E PO Serial Product ${key}`;
        const serialPrefix = `SN-PO-${key}`;

        await purchasePage.createVendor({ name: vendorName, email: `po.serial+${key}@example.com` });
        await purchasePage.createProduct({ name: productName, price: "80", tracking: "serial" });

        await purchasePage.createOrderWithLines({
            vendorName,
            lines: [{ productName, quantity: "3", unitPrice: "80" }],
        });
        await purchasePage.confirmCurrentQuotation();

        const orderRef = purchasePage.currentRecordRef();

        await purchasePage.openReceiptByIndex(0);
        await inventoryPage.clickMarkAsTodoIfVisible();
        await inventoryPage.generateLotOnMove(serialPrefix, "3");
        await inventoryPage.confirmAndValidateOperation();

        await inventoryPage.expectLotListed(serialPrefix);
        await inventoryPage.expectProductQuantityRowVisible(productName);
        await inventoryPage.expectProductMoveRowVisible(productName, "Done");

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectReceivedQuantity(0, "3");
    });

    /**
     * Goods bought from a vendor are received straight into a package.
     */
    test("Purchase order - receive into a package", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E PO Pack Vendor ${key}`;
        const productName = `E2E PO Pack Product ${key}`;
        const packageTypeName = `E2E PO PkgType ${key}`;
        const packageName = `E2E PO PKG ${key}`;

        await purchasePage.createVendor({ name: vendorName, email: `po.pack+${key}@example.com` });
        await purchasePage.createProduct({ name: productName, price: "40" });

        await inventoryPage.createPackageType({ name: packageTypeName });
        await inventoryPage.createPackage({ name: packageName, packageType: packageTypeName });

        await purchasePage.createOrderWithLines({
            vendorName,
            lines: [{ productName, quantity: "6", unitPrice: "40" }],
        });
        await purchasePage.confirmCurrentQuotation();

        const orderRef = purchasePage.currentRecordRef();

        await purchasePage.openReceiptByIndex(0);
        await inventoryPage.setResultPackageForProduct(packageName, productName);
        await inventoryPage.confirmAndValidateOperation();

        await inventoryPage.expectPackageContainsProduct(packageName, productName, "6");
        await inventoryPage.expectOnHandQuantityRow(productName, DEFAULT_STOCK_LOCATION, "6");

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectReceivedQuantity(0, "6");
    });

    /**
     * One purchase order receives three lines at once: quantity, lot and serial tracked.
     */
    test("Purchase order - mixed lot, serial & quantity lines", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E PO Mixed Vendor ${key}`;
        const qtyProduct = `E2E PO Mix Qty ${key}`;
        const lotProduct = `E2E PO Mix Lot ${key}`;
        const serialProduct = `E2E PO Mix Serial ${key}`;

        await purchasePage.createVendor({ name: vendorName, email: `po.mixed+${key}@example.com` });
        await purchasePage.createProduct({ name: qtyProduct, price: "10" });
        await purchasePage.createProduct({ name: lotProduct, price: "20", tracking: "lot" });
        await purchasePage.createProduct({ name: serialProduct, price: "30", tracking: "serial" });

        await purchasePage.createOrderWithLines({
            vendorName,
            lines: [
                { productName: qtyProduct, quantity: "10", unitPrice: "10" },
                { productName: lotProduct, quantity: "4", unitPrice: "20" },
                { productName: serialProduct, quantity: "2", unitPrice: "30" },
            ],
        });
        await purchasePage.confirmCurrentQuotation();

        const orderRef = purchasePage.currentRecordRef();

        await purchasePage.openReceiptByIndex(0);
        await inventoryPage.clickMarkAsTodoIfVisible();
        await inventoryPage.expectOperationMoveCount(3);
        await inventoryPage.generateLotOnMove(`LOT-MIX-${key}`, "4", 1);
        await inventoryPage.generateLotOnMove(`SN-MIX-${key}`, "2", 2);
        await inventoryPage.confirmAndValidateOperation();

        await inventoryPage.expectOnHandQuantityRow(qtyProduct, DEFAULT_STOCK_LOCATION, "10");
        await inventoryPage.expectOnHandQuantityRow(lotProduct, DEFAULT_STOCK_LOCATION, "4");
        await inventoryPage.expectProductQuantityRowVisible(serialProduct);
        await inventoryPage.expectLotListed(`LOT-MIX-${key}`);
        await inventoryPage.expectLotListed(`SN-MIX-${key}`);

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectReceivedQuantity(0, "10");
        await purchasePage.expectReceivedQuantity(1, "4");
        await purchasePage.expectReceivedQuantity(2, "2");
    });

    /**
     * Receiving less than was ordered prompts for a back order; confirming it leaves the
     * order with a second receipt carrying the outstanding quantity.
     */
    test("Purchase order - partial receipt creates backorder", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E PO Backorder Vendor ${key}`;
        const productName = `E2E PO Backorder Product ${key}`;

        await purchasePage.createVendor({ name: vendorName, email: `po.backorder+${key}@example.com` });
        await purchasePage.createProduct({ name: productName, price: "60" });

        await purchasePage.createOrderWithLines({
            vendorName,
            lines: [{ productName, quantity: "10", unitPrice: "60" }],
        });
        await purchasePage.confirmCurrentQuotation();

        const orderRef = purchasePage.currentRecordRef();

        await purchasePage.expectReceiptCount(1);
        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.openReceiptByIndex(0);
        await inventoryPage.receivePartialWithBackorder("6", "ask");

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectReceiptCount(2);

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.openPendingReceipt();
        await inventoryPage.expectOperationMoveDemand("4");

        await inventoryPage.expectOnHandQuantityRow(productName, DEFAULT_STOCK_LOCATION, "6");

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectReceivedQuantity(0, "6");
    });

    /**
     * Returning a validated receipt sends the goods back to the vendor and clears the
     * stock they had added.
     */
    test("Purchase order - return received goods", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E PO Return Vendor ${key}`;
        const productName = `E2E PO Return Product ${key}`;

        await purchasePage.createVendor({ name: vendorName, email: `po.return+${key}@example.com` });
        await purchasePage.createProduct({ name: productName, price: "45" });

        await purchasePage.createOrderWithLines({
            vendorName,
            lines: [{ productName, quantity: "6", unitPrice: "45" }],
        });
        await purchasePage.confirmCurrentQuotation();

        const orderRef = purchasePage.currentRecordRef();

        await purchasePage.openReceiptByIndex(0);
        await inventoryPage.confirmAndValidateOperation();
        await inventoryPage.expectOnHandQuantityRow(productName, DEFAULT_STOCK_LOCATION, "6");

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.openReceiptByIndex(0);
        await inventoryPage.returnAndValidate("2");
        await inventoryPage.expectOnReturnOperationPage();
        await inventoryPage.expectOperationDone();

        await inventoryPage.expectOnHandQuantityRow(productName, DEFAULT_STOCK_LOCATION, "4");
        await inventoryPage.expectProductMoveRowVisible(productName, "Done");
    });
});

test.describe("Purchase Flow - Multi Step Reception", () => {
    test.beforeAll(async ({ adminPage }) => {
        await enablePurchaseInventorySettings(adminPage);
    });

    /**
     * A 2-step reception lands the goods in Input first; only validating the follow-up
     * internal transfer puts them into Stock.
     */
    test("Purchase order - 2-step reception (input, stock)", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E PO 2Step Vendor ${key}`;
        const productName = `E2E PO 2Step Product ${key}`;
        const warehouseName = `PO In2Step ${key}`;
        const warehouseCode = `PO2${key}`;

        await inventoryPage.createWarehouse({
            name: warehouseName,
            code: warehouseCode,
            receptionStep: 2,
            deliveryStep: 1,
        });

        await purchasePage.createVendor({ name: vendorName, email: `po.2step+${key}@example.com` });
        await purchasePage.createProduct({ name: productName, price: "25" });

        await purchasePage.createOrderWithLines({
            vendorName,
            warehouseName,
            lines: [{ productName, quantity: "7", unitPrice: "25" }],
        });
        await purchasePage.confirmCurrentQuotation();

        const orderRef = purchasePage.currentRecordRef();

        await purchasePage.openReceiptByReference("/IN/");
        await inventoryPage.confirmAndValidateOperation();
        await inventoryPage.chainNextTransfers();

        await inventoryPage.expectOnHandQuantityRow(productName, `${warehouseCode}/Stock`, "7");
        await inventoryPage.expectProductMoveRowVisible(productName, "Done");

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectReceivedQuantity(0, "7");
    });

    /**
     * A 3-step reception walks Receipt, Quality Control and Storage before the goods are
     * counted as on-hand stock.
     */
    test("Purchase order - 3-step reception (input, quality, stock)", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E PO 3Step Vendor ${key}`;
        const productName = `E2E PO 3Step Product ${key}`;
        const warehouseName = `PO In3Step ${key}`;
        const warehouseCode = `PO3${key}`;

        await inventoryPage.createWarehouse({
            name: warehouseName,
            code: warehouseCode,
            receptionStep: 3,
            deliveryStep: 1,
        });

        await purchasePage.createVendor({ name: vendorName, email: `po.3step+${key}@example.com` });
        await purchasePage.createProduct({ name: productName, price: "35" });

        await purchasePage.createOrderWithLines({
            vendorName,
            warehouseName,
            lines: [{ productName, quantity: "9", unitPrice: "35" }],
        });
        await purchasePage.confirmCurrentQuotation();

        const orderRef = purchasePage.currentRecordRef();

        await purchasePage.openReceiptByReference("/IN/");
        await inventoryPage.confirmAndValidateOperation();
        await inventoryPage.chainNextTransfers();

        await inventoryPage.expectOnHandQuantityRow(productName, `${warehouseCode}/Stock`, "9");
        await inventoryPage.expectProductMoveRowVisible(productName, "Done");

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectReceivedQuantity(0, "9");
    });
});

test.describe("Purchase Flow - Amounts", () => {
    test.beforeAll(async ({ adminPage }) => {
        await enablePurchaseInventorySettings(adminPage);
    });

    /**
     * A line with no tax contributes its full subtotal: the "Tax" row is not rendered at
     * all and the total equals the untaxed amount.
     */
    test("Purchase order - amounts without tax", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E PO Amt NoTax Vendor ${key}`;
        const productName = `E2E PO Amt NoTax Product ${key}`;

        await purchasePage.createVendor({ name: vendorName, email: `po.amt.notax+${key}@example.com` });
        await purchasePage.createProduct({ name: productName, price: "100" });

        await purchasePage.createOrderWithLines({
            vendorName,
            lines: [{ productName, quantity: "3", unitPrice: "100" }],
        });

        await purchasePage.expectLineSubtotal(0, "300");
        await purchasePage.expectOrderTotals({ untaxed: "$300.00", total: "$300.00" });

        await purchasePage.confirmCurrentQuotation();

        const orderRef = purchasePage.currentRecordRef();
        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectOrderTotals({ untaxed: "$300.00", total: "$300.00" });
    });

    /**
     * The seeded 15% purchase tax is added on top of the untaxed amount, so 3 x 100
     * becomes 300 untaxed, 45 tax and 345 total. The line subtotal stays untaxed.
     */
    test("Purchase order - amounts with 15% tax", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E PO Amt Tax Vendor ${key}`;
        const productName = `E2E PO Amt Tax Product ${key}`;

        await purchasePage.createVendor({ name: vendorName, email: `po.amt.tax+${key}@example.com` });
        await purchasePage.createProduct({ name: productName, price: "100" });

        await purchasePage.createOrderWithLines({
            vendorName,
            lines: [{ productName, quantity: "3", unitPrice: "100", taxName: PURCHASE_TAX_NAME }],
        });

        await purchasePage.expectLineSubtotal(0, "300");
        await purchasePage.expectOrderTotals({ untaxed: "$300.00", tax: "$45.00", total: "$345.00" });

        await purchasePage.confirmCurrentQuotation();

        const orderRef = purchasePage.currentRecordRef();
        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectOrderTotals({ untaxed: "$300.00", tax: "$45.00", total: "$345.00" });
    });
});

test.describe("Purchase Flow - Confirmed Order Changes", () => {
    test.beforeAll(async ({ adminPage }) => {
        await enablePurchaseInventorySettings(adminPage);
    });

    /**
     * Raising an ordered quantity on a confirmed order does not spawn a second receipt:
     * the extra units are merged into the open receipt's existing move.
     */
    test("Purchase order - increasing quantity after confirm grows the receipt", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E PO Qty Up Vendor ${key}`;
        const productName = `E2E PO Qty Up Product ${key}`;

        await purchasePage.createVendor({ name: vendorName, email: `po.qty.up+${key}@example.com` });
        await purchasePage.createProduct({ name: productName, price: "20" });

        await purchasePage.createOrderWithLines({
            vendorName,
            lines: [{ productName, quantity: "5", unitPrice: "20" }],
        });
        await purchasePage.confirmCurrentQuotation();

        const orderRef = purchasePage.currentRecordRef();

        await purchasePage.openReceiptByIndex(0);
        await inventoryPage.expectOperationMoveDemandForProduct(productName, "5");

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.updateLineQuantity(0, "8");
        await purchasePage.saveOrder();

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectLineQuantity(0, "8");

        await purchasePage.expectReceiptCount(1);
        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.openReceiptByIndex(0);
        await inventoryPage.expectOperationMoveCount(1);
        await inventoryPage.expectOperationMoveDemandForProduct(productName, "8");
    });

    /**
     * Lowering an ordered quantity on a confirmed order shrinks the open receipt's move
     * rather than cancelling and re-creating the transfer.
     */
    test("Purchase order - decreasing quantity after confirm shrinks the receipt", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E PO Qty Down Vendor ${key}`;
        const productName = `E2E PO Qty Down Product ${key}`;

        await purchasePage.createVendor({ name: vendorName, email: `po.qty.down+${key}@example.com` });
        await purchasePage.createProduct({ name: productName, price: "20" });

        await purchasePage.createOrderWithLines({
            vendorName,
            lines: [{ productName, quantity: "8", unitPrice: "20" }],
        });
        await purchasePage.confirmCurrentQuotation();

        const orderRef = purchasePage.currentRecordRef();

        await purchasePage.openReceiptByIndex(0);
        await inventoryPage.expectOperationMoveDemandForProduct(productName, "8");

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.updateLineQuantity(0, "5");
        await purchasePage.saveOrder();

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectLineQuantity(0, "5");

        await purchasePage.expectReceiptCount(1);
        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.openReceiptByIndex(0);
        await inventoryPage.expectOperationMoveCount(1);
        await inventoryPage.expectOperationMoveDemandForProduct(productName, "5");
    });

    /**
     * Once units have been received, the order refuses to be reduced below them.
     */
    test("Purchase order - quantity cannot drop below the received quantity", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E PO Qty Guard Vendor ${key}`;
        const productName = `E2E PO Qty Guard Product ${key}`;

        await purchasePage.createVendor({ name: vendorName, email: `po.qty.guard+${key}@example.com` });
        await purchasePage.createProduct({ name: productName, price: "20" });

        await purchasePage.createOrderWithLines({
            vendorName,
            lines: [{ productName, quantity: "5", unitPrice: "20" }],
        });
        await purchasePage.confirmCurrentQuotation();

        const orderRef = purchasePage.currentRecordRef();

        await purchasePage.openReceiptByIndex(0);
        await inventoryPage.confirmAndValidateOperation();

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectReceivedQuantity(0, "5");

        await purchasePage.updateLineQuantity(0, "3");
        await purchasePage.saveOrder();
        await purchasePage.expectQuantityBelowReceivedError("5");

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectLineQuantity(0, "5");
    });

    /**
     * A product added to a confirmed order whose receipt is still open joins that receipt
     * as a second move instead of opening a new transfer.
     */
    test("Purchase order - adding a product after confirm extends the open receipt", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E PO AddLine Vendor ${key}`;
        const firstProduct = `E2E PO AddLine First ${key}`;
        const secondProduct = `E2E PO AddLine Second ${key}`;

        await purchasePage.createVendor({ name: vendorName, email: `po.add.line+${key}@example.com` });
        await purchasePage.createProduct({ name: firstProduct, price: "20" });
        await purchasePage.createProduct({ name: secondProduct, price: "30" });

        await purchasePage.createOrderWithLines({
            vendorName,
            lines: [{ productName: firstProduct, quantity: "4", unitPrice: "20" }],
        });
        await purchasePage.confirmCurrentQuotation();

        const orderRef = purchasePage.currentRecordRef();

        await purchasePage.openReceiptByIndex(0);
        await inventoryPage.expectOperationMoveCount(1);

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.addLineToOpenOrder(secondProduct, "2", "30");
        await purchasePage.saveOrder();

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectLineQuantity(1, "2");
        await purchasePage.expectReceiptCount(1);

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.openReceiptByIndex(0);
        await inventoryPage.expectOperationMoveCount(2);
        await inventoryPage.expectOperationMoveDemandForProduct(firstProduct, "4");
        await inventoryPage.expectOperationMoveDemandForProduct(secondProduct, "2");
    });

    /**
     * Once the order's receipt is validated there is no open transfer left to extend, so a
     * newly added product is received by a brand-new receipt.
     */
    test("Purchase order - adding a product after receipt is done creates a new receipt", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const inventoryPage = new InventoriesManagementPage(adminPage);
        const key = Date.now();

        const vendorName = `E2E PO AddAfter Vendor ${key}`;
        const firstProduct = `E2E PO AddAfter First ${key}`;
        const secondProduct = `E2E PO AddAfter Second ${key}`;

        await purchasePage.createVendor({ name: vendorName, email: `po.add.after+${key}@example.com` });
        await purchasePage.createProduct({ name: firstProduct, price: "20" });
        await purchasePage.createProduct({ name: secondProduct, price: "30" });

        await purchasePage.createOrderWithLines({
            vendorName,
            lines: [{ productName: firstProduct, quantity: "4", unitPrice: "20" }],
        });
        await purchasePage.confirmCurrentQuotation();

        const orderRef = purchasePage.currentRecordRef();

        await purchasePage.openReceiptByIndex(0);
        await inventoryPage.confirmAndValidateOperation();

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.addLineToOpenOrder(secondProduct, "2", "30");
        await purchasePage.saveOrder();

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectLineQuantity(1, "2");
        await purchasePage.expectReceiptCount(2);

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.openPendingReceipt();
        await inventoryPage.expectOperationMoveCount(1);
        await inventoryPage.expectOperationMoveDemandForProduct(secondProduct, "2");

        await inventoryPage.confirmAndValidateOperation();

        await inventoryPage.expectOnHandQuantityRow(firstProduct, DEFAULT_STOCK_LOCATION, "4");
        await inventoryPage.expectOnHandQuantityRow(secondProduct, DEFAULT_STOCK_LOCATION, "2");

        await purchasePage.gotoOrderEdit(orderRef);
        await purchasePage.expectReceivedQuantity(0, "4");
        await purchasePage.expectReceivedQuantity(1, "2");
    });
});
