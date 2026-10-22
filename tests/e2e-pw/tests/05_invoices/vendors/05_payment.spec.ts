import { test } from "../../../setup";
import { InvoiceManagementPage } from "../../../pages/06_invoiceManagement";

test.describe("Vendor Payments E2E", () => {
    test.beforeEach(async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.ensureInvoicePluginInstalled();
    });

    test("Payments Listing - Loads Table", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.gotoVendorPaymentsPage();
    });

    test("Create Payment - Valid Inputs", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const vendorName = `E2E Vendor Payment ${key}`;

        await invoicePage.createVendor({
            name: vendorName,
            email: `vendor.payment+${key}@example.com`,
        });

        await invoicePage.createVendorPayment({
            partnerName: vendorName,
            amount: "310",
            memo: `Vendor payment ${key}`,
        });
    });

    test("Edit Payment - Updates Amount", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const vendorName = `E2E Vendor Payment ${key}`;

        await invoicePage.createVendor({
            name: vendorName,
            email: `vendor.payment+${key}@example.com`,
        });

        await invoicePage.createVendorPayment({
            partnerName: vendorName,
            amount: "260",
            memo: `Vendor payment ${key}`,
        });

        await invoicePage.editVendorPayment(vendorName, {
            amount: "330",
            memo: `Vendor payment updated ${key}`,
        });
    });

    test("Delete Payment - Removes Record", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const vendorName = `E2E Vendor Payment ${key}`;

        await invoicePage.createVendor({
            name: vendorName,
            email: `vendor.payment+${key}@example.com`,
        });

        await invoicePage.createVendorPayment({
            partnerName: vendorName,
            amount: "210",
            memo: `Vendor payment ${key}`,
        });

        await invoicePage.deleteVendorPayment(vendorName);
    });
});
