import { test } from "../../../setup";
import { InvoiceManagementPage } from "../../../pages/06_invoiceManagement";

test.describe("Customer Payments E2E", () => {
    test.beforeEach(async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.ensureInvoicePluginInstalled();
    });

    test("Payments Listing - Loads Table", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.gotoCustomerPaymentsPage();
    });

    test("Create Payment - Valid Inputs", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const customerName = `E2E Payment Customer ${key}`;

        await invoicePage.createCustomer({
            name: customerName,
            email: `customer.payment+${key}@example.com`,
        });

        await invoicePage.createCustomerPayment({
            partnerName: customerName,
            amount: "250",
            memo: `Customer payment ${key}`,
        });
    });

    test("Edit Payment - Updates Amount", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const customerName = `E2E Payment Customer ${key}`;

        await invoicePage.createCustomer({
            name: customerName,
            email: `customer.payment+${key}@example.com`,
        });

        await invoicePage.createCustomerPayment({
            partnerName: customerName,
            amount: "175",
            memo: `Customer payment ${key}`,
        });

        await invoicePage.editCustomerPayment(customerName, {
            amount: "225",
            memo: `Customer payment updated ${key}`,
        });
    });

    test("Delete Payment - Removes Record", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const customerName = `E2E Payment Customer ${key}`;

        await invoicePage.createCustomer({
            name: customerName,
            email: `customer.payment+${key}@example.com`,
        });

        await invoicePage.createCustomerPayment({
            partnerName: customerName,
            amount: "140",
            memo: `Customer payment ${key}`,
        });

        await invoicePage.deleteCustomerPayment(customerName);
    });
});
