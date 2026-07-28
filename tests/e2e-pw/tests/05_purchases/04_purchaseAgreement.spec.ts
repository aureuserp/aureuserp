import { test } from "../../setup";
import { PurchaseFlowPage } from "../../pages/05_purchaseManagement";

test.describe("Purchase Agreements E2E", () => {
    test.beforeAll(async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        await purchasePage.ensurePurchasesPluginInstalled();
        await purchasePage.setPurchaseAgreementsEnabled(true);
    });

    test("Purchase Agreements Listing - Loads Table", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        await purchasePage.gotoPurchaseAgreementsPage();
    });

    test("Purchase Agreement Settings - Enable Agreements", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        await purchasePage.setPurchaseAgreementsEnabled(true);
        await purchasePage.gotoPurchaseAgreementsPage();
    });

    test("Create Purchase Agreement - Valid Inputs", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const key = Date.now();
        const vendorName = `E2E Agreement Vendor ${key}`;
        const productName = `E2E Agreement Product ${key}`;

        await purchasePage.createVendor({
            name: vendorName,
            email: `purchase.agreement.vendor+${key}@example.com`,
        });

        await purchasePage.createProduct({
            name: productName,
            price: "175",
        });

        await purchasePage.createPurchaseAgreement({
            vendorName,
            productName,
            quantity: "5",
            unitPrice: "175",
            reference: `E2E-AGR-${key}`,
        });
    });

    test("Create Purchase Agreement - Validation Errors (Missing Vendor And Product)", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        await purchasePage.createPurchaseAgreementExpectingValidationError();
    });

    test("Edit Purchase Agreement - Update Reference", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const key = Date.now();
        const vendorName = `E2E Agreement Vendor ${key}`;
        const productName = `E2E Agreement Product ${key}`;
        const reference = `E2E-AGR-${key}`;
        const updatedReference = `E2E-AGR-UPDATED-${key}`;

        await purchasePage.createVendor({
            name: vendorName,
            email: `purchase.agreement.vendor+${key}@example.com`,
        });

        await purchasePage.createProduct({
            name: productName,
            price: "205",
        });

        await purchasePage.createPurchaseAgreement({
            vendorName,
            productName,
            quantity: "4",
            unitPrice: "205",
            reference,
        });

        await purchasePage.editPurchaseAgreement(vendorName, {
            reference: updatedReference,
            quantity: "6",
            unitPrice: "215",
        });
    });

    test("Delete Purchase Agreement - Removes Draft", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const key = Date.now();
        const vendorName = `E2E Agreement Vendor ${key}`;
        const productName = `E2E Agreement Product ${key}`;
        const reference = `E2E-AGR-${key}`;

        await purchasePage.createVendor({
            name: vendorName,
            email: `purchase.agreement.vendor+${key}@example.com`,
        });

        await purchasePage.createProduct({
            name: productName,
            price: "195",
        });

        await purchasePage.createPurchaseAgreement({
            vendorName,
            productName,
            quantity: "2",
            unitPrice: "195",
            reference,
        });

        await purchasePage.deletePurchaseAgreement(vendorName);
    });

    test("Confirm Purchase Agreement - Moves To Confirmed", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const key = Date.now();
        const vendorName = `E2E Agreement Vendor ${key}`;
        const productName = `E2E Agreement Product ${key}`;

        await purchasePage.createVendor({
            name: vendorName,
            email: `purchase.agreement.vendor+${key}@example.com`,
        });

        await purchasePage.createProduct({
            name: productName,
            price: "225",
        });

        await purchasePage.createPurchaseAgreement({
            vendorName,
            productName,
            quantity: "8",
            unitPrice: "225",
            reference: `E2E-CONFIRM-${key}`,
        });

        await purchasePage.confirmCurrentPurchaseAgreement();
    });
});
