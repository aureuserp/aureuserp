import { test } from "../../setup";
import { PurchaseFlowPage } from "../../pages/05_purchaseManagement";
import { uniqueKey } from "../../utils/unique";

test.describe("Purchase Products E2E", () => {
    test.beforeAll(async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        await purchasePage.ensurePurchasesPluginInstalled();
    });

    test("Products Listing - Loads Table", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        await purchasePage.gotoProductsPage();
    });

    test("Create Product - Valid Inputs", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const key = uniqueKey();

        await purchasePage.createProduct({
            name: `E2E Purchase Product ${key}`,
            price: "99",
        });
    });

    test("Edit Product - Updates Name", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const key = uniqueKey();
        const originalName = `E2E Purchase Product ${key}`;
        const updatedName = `E2E Purchase Product Updated ${key}`;

        await purchasePage.createProduct({
            name: originalName,
            price: "125",
        });

        await purchasePage.editProduct(originalName, {
            name: updatedName,
            price: "155",
        });
    });

    test("Delete Product - Removes Record", async ({ adminPage }) => {
        const purchasePage = new PurchaseFlowPage(adminPage);
        const key = uniqueKey();
        const productName = `E2E Purchase Product ${key}`;

        await purchasePage.createProduct({
            name: productName,
            price: "85",
        });

        await purchasePage.deleteProduct(productName);
    });
});
