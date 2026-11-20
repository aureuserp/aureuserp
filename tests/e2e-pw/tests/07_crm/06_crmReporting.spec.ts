import { test, withAdminPage } from "../../setup";
import { CRMFlowPage, type CRMLayout } from "../../pages/07_crmManagement";

const REPORTS: { name: string; path: string; title: RegExp; layouts: CRMLayout[] }[] = [
    { name: "Forecast", path: "/admin/crm/reporting/forecast", title: /Forecast/, layouts: ["kanban", "list", "graph"] },
    { name: "Pipeline Analysis", path: "/admin/crm/reporting/pipeline-analysis", title: /Pipeline Analysis/, layouts: ["list", "graph"] },
    { name: "Leads Analysis", path: "/admin/crm/reporting/lead-analysis", title: /Leads Analysis/, layouts: ["list", "graph"] },
    { name: "Activities Analysis", path: "/admin/crm/reporting/activity-analysis", title: /Activities Analysis/, layouts: ["list", "graph"] },
];

test.describe("CRM Reporting E2E", () => {
    test.beforeAll(async ({ browser }) => {
        await withAdminPage(browser, async (adminPage) => {
            const crmPage = new CRMFlowPage(adminPage);
            await crmPage.ensureCRMPluginInstalled();
        });
    });

    for (const report of REPORTS) {
        test(`${report.name} - Loads Report`, async ({ adminPage }) => {
            const crmPage = new CRMFlowPage(adminPage);
            await crmPage.gotoCRMPage(report.path, report.title);
        });

        test(`${report.name} Layouts - ${report.layouts.join(", ")}`, async ({ adminPage }) => {
            const crmPage = new CRMFlowPage(adminPage);

            for (const layout of report.layouts) {
                await crmPage.expectLayout(report.path, layout);
            }
        });
    }
});
