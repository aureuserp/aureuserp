import { test, withAdminPage } from "../../setup";
import { SalesFlowPage } from "../../pages/04_salesManagement";

test.describe("Sales Products E2E", () => {
    test.beforeAll(async ({ browser }) => {
        await withAdminPage(browser, async (adminPage) => {
            const salesPage = new SalesFlowPage(adminPage);
            await salesPage.ensureSalesPluginInstalled();
        });
    });

    test("Products Listing - Loads Table", async ({ adminPage }) => {
        const salesPage = new SalesFlowPage(adminPage);
        await salesPage.gotoProductsPage();
    });

    test("Create Product - Valid Inputs", async ({ adminPage }) => {
        const salesPage = new SalesFlowPage(adminPage);
        const key = Date.now();

        await salesPage.createProduct({
            name: `E2E Sales Product ${key}`,
            price: "99",
        });
    });

    test("Edit Product - Updates Name", async ({ adminPage }) => {
        const salesPage = new SalesFlowPage(adminPage);
        const key = Date.now();
        const originalName = `E2E Sales Product ${key}`;
        const updatedName = `E2E Sales Product Updated ${key}`;

        await salesPage.createProduct({
            name: originalName,
            price: "49",
        });

        await salesPage.editProduct(originalName, { name: updatedName, price: "59" });
    });

    test("Delete Product - Removes Record", async ({ adminPage }) => {
        const salesPage = new SalesFlowPage(adminPage);
        const key = Date.now();
        const productName = `E2E Sales Product ${key}`;

        await salesPage.createProduct({
            name: productName,
            price: "29",
        });

        await salesPage.deleteProduct(productName);
    });
});
