import { test } from "../../../setup";
import { InvoiceManagementPage } from "../../../pages/06_invoiceManagement";

test.describe("Customer Credit Notes E2E", () => {
    test.beforeEach(async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.ensureInvoicePluginInstalled();
    });

    test("Credit Notes Listing - Loads Table", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        await invoicePage.gotoCustomerCreditNotesPage();
    });

    test("Create Credit Note - Valid Inputs", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const customerName = `E2E Credit Note Customer ${key}`;
        const productName = `E2E Credit Note Product ${key}`;

        await invoicePage.createCustomer({
            name: customerName,
            email: `credit.note.customer+${key}@example.com`,
        });

        await invoicePage.createCustomerProduct({
            name: productName,
            price: "90",
        });

        await invoicePage.createCustomerCreditNote({
            partnerName: customerName,
            productName,
            quantity: "1",
        });
    });

    test("Edit Credit Note - Updates Quantity", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const customerName = `E2E Credit Note Customer ${key}`;
        const productName = `E2E Credit Note Product ${key}`;

        await invoicePage.createCustomer({
            name: customerName,
            email: `credit.note.customer+${key}@example.com`,
        });

        await invoicePage.createCustomerProduct({
            name: productName,
            price: "110",
        });

        await invoicePage.createCustomerCreditNote({
            partnerName: customerName,
            productName,
            quantity: "2",
        });

        await invoicePage.editCustomerCreditNoteQuantity(customerName, "3");
    });

    test("Delete Credit Note - Removes Draft", async ({ adminPage }) => {
        const invoicePage = new InvoiceManagementPage(adminPage);
        const key = Date.now();
        const customerName = `E2E Credit Note Customer ${key}`;
        const productName = `E2E Credit Note Product ${key}`;

        await invoicePage.createCustomer({
            name: customerName,
            email: `credit.note.customer+${key}@example.com`,
        });

        await invoicePage.createCustomerProduct({
            name: productName,
            price: "130",
        });

        await invoicePage.createCustomerCreditNote({
            partnerName: customerName,
            productName,
            quantity: "1",
        });

        await invoicePage.deleteCustomerCreditNote(customerName);
    });
});
