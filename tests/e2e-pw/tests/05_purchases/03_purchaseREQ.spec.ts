import { test } from "../../setup";
import { PurchaseFlowPage } from "../../pages/05_purchaseManagement";

test.describe("Purchase RFQ E2E", () => {
    test.beforeAll(async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        await purchasePage.ensurePurchasesPluginInstalled();
    });

    test("RFQ Listing - Loads Table", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        await purchasePage.gotoQuotationsPage();
    });

    test("Create RFQ - Valid Inputs", async ({ adminPage }) => {
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
    });

    test("Create RFQ - Validation Errors (Missing Vendor And Product)", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        await purchasePage.gotoQuotationsPage();
        await purchasePage.erpLocators.purchaseQuotationCreateButton.click();
        await purchasePage.erpLocators.purchaseQuotationSaveButton.click();
        await purchasePage.expectQuotationValidationErrors();
    });

    test("Edit RFQ - Update Quantity", async ({ adminPage }) => {
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
            price: "160",
        });

        await purchasePage.createQuotation({
            vendorName,
            productName,
            quantity: "1",
            unitPrice: "160",
        });

        await purchasePage.editQuotationQuantity(vendorName, "3", "175");
    });

    test("Delete RFQ - Removes Draft", async ({ adminPage }) => {
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
            price: "110",
        });

        await purchasePage.createQuotation({
            vendorName,
            productName,
            quantity: "1",
            unitPrice: "110",
        });

        await purchasePage.deleteQuotation(vendorName);
    });
});
