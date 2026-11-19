import { test } from "../../../setup";
import { InvoiceManagementPage } from "../../../pages/06_invoiceManagement";

test.describe("Vendor Refunds E2E", () => {
    test.beforeEach(async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.ensureInvoicePluginInstalled();
    });

    test("Refunds Listing - Loads Table", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.gotoVendorRefundsPage();
    });

    test("Create Refund - Valid Inputs", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const vendorName = `E2E Refund Vendor ${key}`;
        const productName = `E2E Refund Product ${key}`;

        await invoicePage.createVendor({
            name: vendorName,
            email: `refund.vendor+${key}@example.com`,
        });

        await invoicePage.createVendorProduct({
            name: productName,
            price: "115",
        });

        await invoicePage.createVendorRefund({
            partnerName: vendorName,
            productName,
            quantity: "1",
        });
    });

    test("Edit Refund - Updates Quantity", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const vendorName = `E2E Refund Vendor ${key}`;
        const productName = `E2E Refund Product ${key}`;

        await invoicePage.createVendor({
            name: vendorName,
            email: `refund.vendor+${key}@example.com`,
        });

        await invoicePage.createVendorProduct({
            name: productName,
            price: "125",
        });

        await invoicePage.createVendorRefund({
            partnerName: vendorName,
            productName,
            quantity: "2",
        });

        await invoicePage.editVendorRefundQuantity(vendorName, "4");
    });

    test("Delete Refund - Removes Draft", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const vendorName = `E2E Refund Vendor ${key}`;
        const productName = `E2E Refund Product ${key}`;

        await invoicePage.createVendor({
            name: vendorName,
            email: `refund.vendor+${key}@example.com`,
        });

        await invoicePage.createVendorProduct({
            name: productName,
            price: "135",
        });

        await invoicePage.createVendorRefund({
            partnerName: vendorName,
            productName,
            quantity: "1",
        });

        await invoicePage.deleteVendorRefund(vendorName);
    });
});
