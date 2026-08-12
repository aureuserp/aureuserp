import { test } from "../../setup";
import { PurchaseFlowPage } from "../../pages/05_purchaseManagement";

test.describe("Purchase Vendors E2E", () => {
    test.beforeAll(async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        await purchasePage.ensurePurchasesPluginInstalled();
    });

    test("Vendors Listing - Loads Table", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        await purchasePage.gotoVendorsPage();
    });

    test("Create Vendor - Valid Inputs", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const key = Date.now();

        await purchasePage.createVendor({
            name: `E2E Purchase Vendor ${key}`,
            email: `purchase.vendor+${key}@example.com`,
        });
    });

    test("Edit Vendor - Updates Name", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const key = Date.now();
        const originalName = `E2E Purchase Vendor ${key}`;
        const updatedName = `E2E Purchase Vendor Updated ${key}`;

        await purchasePage.createVendor({
            name: originalName,
            email: `purchase.vendor+${key}@example.com`,
        });

        await purchasePage.editVendor(originalName, {
            name: updatedName,
            email: `purchase.vendor.updated+${key}@example.com`,
        });
    });

    test("Delete Vendor - Removes Record", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const key = Date.now();
        const vendorName = `E2E Purchase Vendor ${key}`;

        await purchasePage.createVendor({
            name: vendorName,
            email: `purchase.vendor+${key}@example.com`,
        });

        await purchasePage.deleteVendor(vendorName);
    });
});
