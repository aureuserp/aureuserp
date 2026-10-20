import { test, expect } from "../../setup";
import { TimeOffManagementPage } from "../../pages/07_timeOffManagement";

/**
 * Seeded employee (plugins/webkul/employees/database/seeders/EmployeeSeeder.php) that
 * becomes available once the Employees plugin is installed. Used only to give the
 * Reporting > By Employee grouping something real to group by.
 */
const EMPLOYEE_REPORTING = "Paul Williams";

/**
 * Build a "YYYY-MM-DD" date string N days from today, matching the format the Time Off
 * request's DatePicker fields expect.
 */
function futureDate(daysFromNow: number): string {
    const date = new Date();
    date.setDate(date.getDate() + daysFromNow);
    return date.toISOString().slice(0, 10);
}

/**
 * Spread this file's date window far from other spec files creating Time Off requests
 * for the same seeded employee, so the overlap guardrail never fires by accident.
 */
function uniqueDayOffset(key: number): number {
    return 6000 + (key % 3000) + Math.floor(Math.random() * 3000);
}

/**
 * Reporting - By Employee: the exact Time Off table/actions, pre-grouped by employee.
 * Kept as a light smoke duplicate of the Management > Time Off coverage rather than
 * re-testing every guardrail here.
 */
test.describe("Time Off Reporting - By Employee", () => {
    test.beforeAll(async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        await timeOffPage.ensureBaseDependentPluginsInstalled();
    });

    /**
     * Seed one real Time Off request so the report has at least one row to group,
     * then confirm the list is grouped by employee and still exposes the same
     * Approve/Refuse row actions as the admin Time Off list.
     */
    test("By Employee listing groups by employee and exposes Approve/Refuse", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        const key = Date.now();
        const leaveTypeName = `E2E Report LeaveType ${key}`;
        const description = `E2E Report TimeOff ${key}`;
        const offset = uniqueDayOffset(key);

        await timeOffPage.createLeaveType({ name: leaveTypeName });
        await timeOffPage.createTimeOff({
            employeeName: EMPLOYEE_REPORTING,
            leaveTypeName,
            dateFrom: futureDate(offset),
            dateTo: futureDate(offset + 1),
            description,
        });

        await timeOffPage.gotoByEmployeeReportPage();
        await expect(timeOffPage.erpLocators.timeOffReportGroupHeader).toBeVisible({ timeout: 15000 });

        await timeOffPage.expectApproveActionVisibleForRow(description);
    });
});

/**
 * Reporting - By Type: a dashboard-style page whose only widget is a bar chart. The
 * chart's internal state-key grouping does not fully match the real State enum, so
 * this only asserts the page loads and the widget renders - not specific bar values.
 */
test.describe("Time Off Reporting - By Type", () => {
    test.beforeAll(async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        await timeOffPage.ensureBaseDependentPluginsInstalled();
    });

    /**
     * The By Type page loads and its chart widget is visible.
     */
    test("By Type page renders its chart widget", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        await timeOffPage.gotoByTypeReportPage();
    });
});

/**
 * Overview - the top-level, org-wide calendar page (not nested under any cluster).
 * Calendar/date interactions are inherently flaky in Playwright, so this stays to
 * open/close the header Create modal rather than any drag-select date range.
 */
test.describe("Time Off Overview", () => {
    test.beforeAll(async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        await timeOffPage.ensureBaseDependentPluginsInstalled();
    });

    /**
     * The Overview page loads with its org-wide calendar widget visible.
     */
    test("Overview page renders its calendar widget", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        await timeOffPage.gotoOverviewPage();
    });

    /**
     * The header Create action opens the same leave-request modal form used
     * elsewhere in the plugin, and it can be closed again without saving.
     */
    test("Header Create action opens the leave request modal and closes without saving", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);

        await timeOffPage.gotoOverviewPage();
        await timeOffPage.openOverviewCreateModal();
        await timeOffPage.closeModal();

        await expect(timeOffPage.erpLocators.timeOffModal).not.toBeVisible();
    });
});

/**
 * Dashboard - the MyTime cluster's personal landing page: its own calendar widget,
 * the read-only "Holidays" slide-over, and the stats widget (available-days per
 * dashboard-flagged Leave Type plus "Pending Requests").
 */
test.describe("Time Off Dashboard", () => {
    test.beforeAll(async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        await timeOffPage.ensureBaseDependentPluginsInstalled();
    });

    /**
     * The Dashboard page loads with its personal calendar widget visible.
     */
    test("Dashboard page renders its personal calendar widget", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        await timeOffPage.gotoDashboardPage();
    });

    /**
     * The "Holidays" header action opens a read-only slide-over listing Public and
     * Mandatory holidays, with no submit/cancel buttons, and it can be closed again.
     */
    test("Holidays header action opens a read-only slide-over", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);

        await timeOffPage.gotoDashboardPage();
        await timeOffPage.openHolidaysSlideOver();

        await expect(
            timeOffPage.erpLocators.timeOffHolidaysSlideOver.getByRole("button", { name: /^(Submit|Save|Confirm)$/i })
        ).toHaveCount(0);

        await timeOffPage.closeModal();
        await expect(timeOffPage.erpLocators.timeOffHolidaysSlideOver).not.toBeVisible();
    });

    /**
     * The stats widget renders, showing at least the "Pending Requests" stat -
     * exact numbers are not asserted since no data is deliberately set up here to
     * make one meaningful.
     */
    test("Stats widget shows the Pending Requests stat", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);

        await timeOffPage.gotoDashboardPage();
        await timeOffPage.expectPendingRequestsStatVisible();
    });
});
