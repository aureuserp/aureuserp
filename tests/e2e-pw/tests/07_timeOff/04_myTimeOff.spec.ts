import { test, expect } from "../../setup";
import { TimeOffManagementPage } from "../../pages/07_timeOffManagement";
import { UserManagementPage, type UserData } from "../../pages/03_userManagement";
import { CompanyManagementPage } from "../../pages/02_companyManagement";

/**
 * Seeded employees (plugins/webkul/employees/database/seeders/EmployeeSeeder.php),
 * available once the Employees plugin is installed. Picked distinct from every
 * employee already claimed by a sibling 07_timeOff spec file (Paul Williams /
 * John Doe / Jane Smith / Ravi Kumar in 02_timeOffManagement.spec.ts, Paul
 * Williams again in 03_timeOffAllocations.spec.ts, Emily Davis / Michael Brown in
 * 06_timeOffFlow.spec.ts) purely so the overlap-guard's per-employee date window
 * never has to contend across files.
 */
const FORM_CHECK_EMPLOYEE = "Linda Ndlovu";
const LEAK_CHECK_EMPLOYEE = "Grace Wilson";

/**
 * Build a "YYYY-MM-DD" date string N days from today, matching the format the Time
 * Off request's DatePicker fields expect.
 */
function futureDate(daysFromNow: number): string {
    const date = new Date();
    date.setDate(date.getDate() + daysFromNow);
    return date.toISOString().slice(0, 10);
}

/**
 * Spread each test's date window far apart (and randomized) so parallel workers/spec
 * files creating Time Off requests for the same seeded employee around the same
 * moment never collide with the overlap guardrail.
 */
function uniqueDayOffset(key: number, salt: number): number {
    return 1000 + (key % 5000) + salt * 137 + Math.floor(Math.random() * 5000);
}

/**
 * Open a fresh browser context and log in as an arbitrary (non-admin) user. There is
 * no shared helper for this in the suite - utils/admin.ts's loginAsAdmin only knows
 * the fixed admin credentials - so this mirrors its exact navigation/wait pattern
 * locally: goto /admin/login, fill email/password, arm the "left /admin/login"
 * wait before pressing Enter so a login that redirects immediately is never missed.
 */
async function loginAsUser(
    browser: import("@playwright/test").Browser,
    email: string,
    password: string
): Promise<import("@playwright/test").Page> {
    const context = await browser.newContext();
    const page = await context.newPage();

    await page.goto("/admin/login");
    await page.fill('input[type="email"]', email);
    await page.fill('input[type="password"]', password);

    await Promise.all([
        page.waitForURL((url: URL) => !url.toString().includes("/admin/login"), { timeout: 60000 }),
        page.press('input[type="password"]', "Enter"),
    ]);
    await page.waitForLoadState("networkidle").catch(() => undefined);

    return page;
}

/**
 * MyTime cluster self-service: My Time Off and My Allocations, exercised as a plain
 * low-privilege user (no linked Employee record) in addition to the admin fixture.
 *
 * Source-verified findings this suite documents rather than assumes:
 *   - CreateMyAllocation::mutateFormDataBeforeCreate() halts (no record created, a
 *     warning notification, page stays on /create) when
 *     Employee::where('user_id', Auth::id()) resolves to null for the logged-in user.
 *   - MyTimeOffResource::table() literally reuses TimeOffResource::table(), and
 *     neither resource overrides getEloquentQuery() (nor does the Leave model carry
 *     a global scope) - so the My Time Off list is NOT scoped to the logged-in
 *     user's own Employee at all; any Leave record is visible there. The
 *     Approve/Refuse row actions likewise only ->hidden() on the record's own
 *     state, never on the acting user, so a self-service user - even one with no
 *     Employee record whatsoever - can approve/refuse a request that isn't theirs.
 */
test.describe("MyTime Self-Service (My Time Off & My Allocations)", () => {
    const setupKey = Date.now();
    const secondUserEmail = `mytime.selfservice+${setupKey}@example.com`;
    const secondUserPassword = "Test@12345";
    const noAllocationLeaveTypeName = `MyTime NoEmployee Leave Type ${setupKey}`;

    let secondUserPage: import("@playwright/test").Page;

    test.beforeAll(async ({ adminPage, browser }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        await timeOffPage.ensureBaseDependentPluginsInstalled();

        // A Leave Type with default (No Limit) allocation settings, purely so the
        // blocked My Allocation create below has a valid holiday_status_id to pick -
        // the create is halted before the allocation-balance guardrails even run.
        await timeOffPage.createLeaveType({ name: noAllocationLeaveTypeName });

        // A plain, non-admin user with NO linked Employee record. Role "Admin" is
        // fine here - the plugin-manager's InstallCommand syncs every generated
        // Shield permission (time-off's included) onto Role::first() on plugin
        // install, so this is not a permission-gate test; the only thing
        // distinguishing this user from the admin fixture is the missing Employee.
        const companyPage = new CompanyManagementPage(adminPage);
        const userPage = new UserManagementPage(adminPage);
        const companyName = `MyTime Self-Service Co ${setupKey}`;

        await companyPage.gotoCompaniesPage();
        await companyPage.createCompany({
            name: companyName,
            email: `mytime-selfservice-co+${setupKey}@example.com`,
        });

        const secondUserData: UserData = {
            name: `MyTime Self-Service User ${setupKey}`,
            email: secondUserEmail,
            password: secondUserPassword,
            role: "Admin",
            company: companyName,
        };

        await userPage.gotoUsersPage();
        await userPage.createUser(secondUserData);

        secondUserPage = await loginAsUser(browser, secondUserEmail, secondUserPassword);
    });

    test.afterAll(async () => {
        await secondUserPage?.context().close().catch(() => undefined);
    });

    /**
     * The admin fixture's own user has no linked Employee record either (the
     * Employees plugin seeder only ever sets creator_id, never user_id - see
     * EmployeeSeeder), so createMyTimeOff()'s server-side auto-derivation would hit
     * the exact same "no Employee" gap this file deliberately exploits below.
     * Proving the underlying Leave record/form works end-to-end is done via the
     * admin-visible Time Off resource instead (an explicit employee), which the
     * task treats as an equivalent stand-in for "the form works".
     */
    test("A Time Off request can be created and lands in To Approve", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        const key = Date.now();
        const leaveTypeName = `MyTime Form Leave Type ${key}`;
        const description = `MyTime Form Check ${key}`;
        const offset = uniqueDayOffset(key, 5);

        await timeOffPage.createLeaveType({ name: leaveTypeName });
        await timeOffPage.createTimeOff({
            employeeName: FORM_CHECK_EMPLOYEE,
            leaveTypeName,
            dateFrom: futureDate(offset),
            dateTo: futureDate(offset + 1),
            description,
        });

        await timeOffPage.expectStateBadge("To Approve");

        // The same record is reachable from the MyTime self-service list too - see
        // the access-leak test below for why (no per-employee query scope at all).
        await timeOffPage.gotoMyTimeOffPage();
        await timeOffPage.expectRowVisible(description);
    });

    /**
     * CreateMyAllocation::mutateFormDataBeforeCreate() halts (no redirect, no
     * record) with a warning notification when Employee::where('user_id', ...)
     * resolves to null for the logged-in user.
     */
    test("A user with no Employee record is blocked from creating a My Allocation", async () => {
        const timeOffPage = new TimeOffManagementPage(secondUserPage);
        const key = Date.now();

        await timeOffPage.attemptCreateMyAllocation({
            name: `Blocked MyTime Allocation ${key}`,
            leaveTypeName: noAllocationLeaveTypeName,
            numberOfDays: "1",
        });

        await timeOffPage.expectNoEmployeeProfileBlocked();
        await expect(secondUserPage).toHaveURL(/my-allocations\/create/);
    });

    /**
     * Access-leak check: MyTimeOffResource reuses TimeOffResource's table
     * definition verbatim, with no query scope and no ownership guard on the
     * Approve row action (Refuse follows the identical unguarded pattern - both
     * are plain Action::make() calls hidden only by the record's own state, never
     * by the acting user - so it is not re-tested separately here). A self-service
     * user with no Employee record at all can still see - and approve - a request
     * that belongs to a real, unrelated employee.
     */
    test("My Time Off list exposes another employee's request, Approve included, to a self-service user with no Employee record", async ({
        adminPage,
    }) => {
        const adminTimeOffPage = new TimeOffManagementPage(adminPage);
        const secondUserTimeOffPage = new TimeOffManagementPage(secondUserPage);
        const key = Date.now();
        const leaveTypeName = `MyTime Leak Leave Type ${key}`;
        const description = `MyTime Leak Request ${key}`;
        const offset = uniqueDayOffset(key, 9);

        await adminTimeOffPage.createLeaveType({ name: leaveTypeName });
        await adminTimeOffPage.createTimeOff({
            employeeName: LEAK_CHECK_EMPLOYEE,
            leaveTypeName,
            dateFrom: futureDate(offset),
            dateTo: futureDate(offset + 1),
            description,
        });

        await secondUserTimeOffPage.gotoMyTimeOffPage();
        await secondUserTimeOffPage.expectRowVisible(description);

        await secondUserTimeOffPage.expectApproveActionVisibleForRow(description);
        await secondUserTimeOffPage.approveTimeOffRow(description);
        await secondUserTimeOffPage.expectStateBadge("Approved");
    });
});
