import { test } from "../../../setup";
import { InvoiceManagementPage } from "../../../pages/06_invoiceManagement";

test.describe("Vendor Bills E2E", () => {
    test.beforeEach(async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.ensureInvoicePluginInstalled();
    });

    test("Bills Listing - Loads Table", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.gotoVendorBillsPage();
    });

    test("Create Bill - Validation Errors (Missing Vendor And Product)", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.gotoVendorBillsPage();
        await invoicePage.erpLocators.invoiceCreateButton.click();
        await invoicePage.erpLocators.invoiceSaveButton.click();
        await invoicePage.expectValidationErrors();
    });

    test("Create Bill - Valid Inputs", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const vendorName = `E2E Bill Vendor ${key}`;
        const productName = `E2E Bill Product ${key}`;

        await invoicePage.createVendor({
            name: vendorName,
            email: `bill.vendor+${key}@example.com`,
        });

        await invoicePage.createVendorProduct({
            name: productName,
            price: "170",
        });

        await invoicePage.createVendorBill({
            partnerName: vendorName,
            productName,
            quantity: "2",
        });
    });

    test("Edit Bill - Updates Quantity", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const vendorName = `E2E Bill Vendor ${key}`;
        const productName = `E2E Bill Product ${key}`;

        await invoicePage.createVendor({
            name: vendorName,
            email: `bill.vendor+${key}@example.com`,
        });

        await invoicePage.createVendorProduct({
            name: productName,
            price: "185",
        });

        await invoicePage.createVendorBill({
            partnerName: vendorName,
            productName,
            quantity: "1",
        });

        await invoicePage.editVendorBillQuantity(vendorName, "3");
    });

    test("Delete Bill - Removes Draft", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const vendorName = `E2E Bill Vendor ${key}`;
        const productName = `E2E Bill Product ${key}`;

        await invoicePage.createVendor({
            name: vendorName,
            email: `bill.vendor+${key}@example.com`,
        });

        await invoicePage.createVendorProduct({
            name: productName,
            price: "190",
        });

        await invoicePage.createVendorBill({
            partnerName: vendorName,
            productName,
            quantity: "1",
        });

        await invoicePage.deleteVendorBill(vendorName);
    });
});
