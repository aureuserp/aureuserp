import { test, withAdminPage } from "../../setup";
import { CRMFlowPage, type CRMLayout } from "../../pages/07_crmManagement";

const SALES_PAGES: { name: string; path: string; title: RegExp; layouts: CRMLayout[]; defaultGrouping?: string; boardGrouping?: string }[] = [
    { name: "Activities", path: "/admin/crm/sales/activities", title: /Activities/, layouts: ["list", "kanban", "graph"] },
    { name: "Customers", path: "/admin/crm/sales/customers", title: /Contacts|Customers/, layouts: ["list", "kanban"], defaultGrouping: "user.name", boardGrouping: "country.name" },
    { name: "Quotations", path: "/admin/crm/sales/quotations", title: /Quotations/, layouts: ["list", "kanban", "graph"], defaultGrouping: "medium.name", boardGrouping: "state" },
];

test.describe("CRM Sales Pages E2E", () => {
    test.beforeAll(async ({ browser }) => {
        await withAdminPage(browser, async (adminPage) => {
            const crmPage = new CRMFlowPage(adminPage);
            await crmPage.ensureCRMPluginInstalled();
        });
    });

    for (const salesPage of SALES_PAGES) {
        test(`${salesPage.name} - Loads Page`, async ({ adminPage }) => {
            const crmPage = new CRMFlowPage(adminPage);
            await crmPage.gotoCRMPage(salesPage.path, salesPage.title);
        });

        test(`${salesPage.name} Layouts - ${salesPage.layouts.join(", ")}`, async ({ adminPage }) => {
            const crmPage = new CRMFlowPage(adminPage);

            for (const layout of salesPage.layouts) {
                if (layout === "kanban" && salesPage.defaultGrouping) {
                    await crmPage.expectDefaultGroupBy(salesPage.path, salesPage.defaultGrouping);
                    await crmPage.expectLayout(`${salesPage.path}?grouping=${salesPage.boardGrouping}`, layout);

                    continue;
                }

                await crmPage.expectLayout(salesPage.path, layout);
            }
        });
    }
});
