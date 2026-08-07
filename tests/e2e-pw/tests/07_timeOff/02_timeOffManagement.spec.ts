import { test } from "../../setup";
import { TimeOffManagementPage } from "../../pages/07_timeOffManagement";

/**
 * Seeded employees (plugins/webkul/employees/database/seeders/EmployeeSeeder.php) that
 * become available once the Employees plugin is installed. Each test below uses its own
 * employee so parallel workers never contend for the same overlap-guard window.
 */
const EMPLOYEE_APPROVE = "Hiro Tanaka";
const EMPLOYEE_REFUSE = "John Doe";
const EMPLOYEE_OVERLAP = "Jane Smith";
const EMPLOYEE_VIEWS = "Ravi Kumar";

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
 * Spread each test's date window far apart (and randomized) so parallel workers/spec
 * files creating Time Off requests for the same seeded employee around the same moment
 * never collide with the overlap guardrail.
 */
function uniqueDayOffset(key: number, salt: number): number {
    return 1000 + (key % 5000) + salt * 137 + Math.floor(Math.random() * 5000);
}

/**
 * Install the plugins the Management > Time Off tests rely on. Called from every
 * describe's own beforeAll so a shard/worker running only a subset of the describes
 * still provisions what its tests need, keeping CI sharding and fullyParallel runs
 * order-independent.
 */
async function ensureTimeOffPlugins(adminPage: import("@playwright/test").Page) {
    const timeOffPage = new TimeOffManagementPage(adminPage);
    await timeOffPage.ensureBaseDependentPluginsInstalled();
}

test.describe("Time Off Management - Admin Time Off Requests", () => {
    test.beforeAll(async ({ adminPage }) => {
        await ensureTimeOffPlugins(adminPage);
    });

    /**
     * A Time Off request against a Leave Type with no allocation requirement (No Limit)
     * always lands in "To Approve". Clicking the row's Approve action then jumps it
     * straight to "Approved" (the plugin's approve handler always sets validate_two,
     * regardless of the current state), and the Approve action disappears once it is
     * the terminal state.
     */
    test("Time Off request moves from To Approve to Approved and Approve disappears", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        const key = Date.now();
        const leaveTypeName = `E2E No Limit Leave Approve ${key}`;
        const description = `E2E TimeOff Approve ${key}`;
        const offset = uniqueDayOffset(key, 1);

        await timeOffPage.createLeaveType({ name: leaveTypeName });

        await timeOffPage.createTimeOff({
            employeeName: EMPLOYEE_APPROVE,
            leaveTypeName,
            dateFrom: futureDate(offset),
            dateTo: futureDate(offset + 1),
            description,
        });

        await timeOffPage.gotoTimeOffRequestsPage();
        await timeOffPage.searchList(description);
        await timeOffPage.expectStateBadge("To Approve");

        await timeOffPage.approveTimeOffRow(description);
        await timeOffPage.expectStateBadge("Approved");

        await timeOffPage.expectApproveActionAbsentForRow(description);
    });

    /**
     * Refusing a request sets its state to "Refused". Per the plugin's actual hide
     * logic, the Approve row action stays available on a refused record (it is only
     * hidden once the record is already Approved), and clicking it re-approves the
     * request successfully.
     */
    test("Refused Time Off request keeps its Approve action and can be re-approved", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        const key = Date.now();
        const leaveTypeName = `E2E No Limit Leave Refuse ${key}`;
        const description = `E2E TimeOff Refuse ${key}`;
        const offset = uniqueDayOffset(key, 2);

        await timeOffPage.createLeaveType({ name: leaveTypeName });

        await timeOffPage.createTimeOff({
            employeeName: EMPLOYEE_REFUSE,
            leaveTypeName,
            dateFrom: futureDate(offset),
            dateTo: futureDate(offset + 1),
            description,
        });

        await timeOffPage.gotoTimeOffRequestsPage();
        await timeOffPage.refuseTimeOffRow(description);
        await timeOffPage.expectStateBadge("Refused");

        await timeOffPage.expectApproveActionVisibleForRow(description);

        await timeOffPage.approveTimeOffRow(description);
        await timeOffPage.expectStateBadge("Approved");
    });

    /**
     * A second Time Off request for the same employee whose dates fall inside an
     * existing request's range is blocked with a danger notification mentioning the
     * overlap; the create is halted server-side and no second record is created.
     */
    test("Overlapping Time Off request for the same employee is blocked", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        const key = Date.now();
        const leaveTypeName = `E2E No Limit Leave Overlap ${key}`;
        const offset = uniqueDayOffset(key, 3);

        await timeOffPage.createLeaveType({ name: leaveTypeName });

        await timeOffPage.createTimeOff({
            employeeName: EMPLOYEE_OVERLAP,
            leaveTypeName,
            dateFrom: futureDate(offset),
            dateTo: futureDate(offset + 4),
            description: `E2E TimeOff Overlap First ${key}`,
        });

        await timeOffPage.attemptCreateTimeOff({
            employeeName: EMPLOYEE_OVERLAP,
            leaveTypeName,
            dateFrom: futureDate(offset + 1),
            dateTo: futureDate(offset + 2),
            description: `E2E TimeOff Overlap Second ${key}`,
        });

        await timeOffPage.expectOverlapBlocked();
    });
});

test.describe("Time Off Management - Preset View Tabs", () => {
    test.beforeAll(async ({ adminPage }) => {
        await ensureTimeOffPlugins(adminPage);
    });

    /**
     * A newly created request (state "To Approve") shows up under the default
     * "Waiting For Me" tab but not under "Refused". Approving it then surfaces it
     * under "Approved" instead, and it still does not show under "Refused".
     */
    test("A request surfaces under its matching preset view tab, not an unrelated one", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        const key = Date.now();
        const leaveTypeName = `E2E No Limit Leave Views ${key}`;
        const description = `E2E TimeOff Views ${key}`;
        const offset = uniqueDayOffset(key, 4);

        await timeOffPage.createLeaveType({ name: leaveTypeName });

        await timeOffPage.createTimeOff({
            employeeName: EMPLOYEE_VIEWS,
            leaveTypeName,
            dateFrom: futureDate(offset),
            dateTo: futureDate(offset + 1),
            description,
        });

        await timeOffPage.gotoTimeOffRequestsPage();
        await timeOffPage.selectPresetView("Waiting For Me");
        await timeOffPage.expectRowVisible(description);

        await timeOffPage.selectPresetView("Refused");
        await timeOffPage.expectRowNotVisible(description);

        await timeOffPage.gotoTimeOffRequestsPage();
        await timeOffPage.approveTimeOffRow(description);

        await timeOffPage.selectPresetView("Approved");
        await timeOffPage.expectRowVisible(description);

        await timeOffPage.selectPresetView("Refused");
        await timeOffPage.expectRowNotVisible(description);
    });
});
