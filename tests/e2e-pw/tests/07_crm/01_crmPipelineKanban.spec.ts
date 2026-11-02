import { expect } from "@playwright/test";
import { test, withAdminPage } from "../../setup";
import { CRMFlowPage } from "../../pages/07_crmManagement";

test.describe("CRM Pipeline Kanban E2E", () => {
    test.beforeAll(async ({ browser }) => {
        await withAdminPage(browser, async (adminPage) => {
            const crmPage = new CRMFlowPage(adminPage);
            await crmPage.ensureCRMPluginInstalled();
        });
    });

    test("Pipeline Kanban - Loads Board", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        await crmPage.gotoPipeline();
    });

    test("Quick Create - Adds Card To Column", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        const name = `E2E CRM Opportunity ${Date.now()}`;

        await crmPage.gotoPipeline();
        await crmPage.quickCreateOpportunity(0, name);
        await crmPage.deleteOpportunity(name);
    });

    test("Drag Card - Moves To Another Stage And Stays After Reload", async ({ adminPage }) => {
        const crmPage = new CRMFlowPage(adminPage);
        const name = `E2E CRM Opportunity ${Date.now()}`;

        await crmPage.gotoPipeline();
        await crmPage.quickCreateOpportunity(0, name);

        const fromCount = await crmPage.columnCount(0);
        const toCount = await crmPage.columnCount(1);

        await crmPage.dragCardToColumn(name, 1);

        await crmPage.expectCardInColumn(name, 1);
        expect(await crmPage.columnCount(0)).toBe(fromCount - 1);
        expect(await crmPage.columnCount(1)).toBe(toCount + 1);

        await adminPage.waitForLoadState("networkidle").catch(() => undefined);
        await crmPage.gotoPipeline();

        await crmPage.expectCardInColumn(name, 1);

        await crmPage.deleteOpportunity(name);
    });
});
