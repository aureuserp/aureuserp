import { test, expect } from "../../setup";
import { TimeOffManagementPage } from "../../pages/07_timeOffManagement";

/**
 * Management - Allocations: the richest state-transition surface in the
 * time-off plugin. Covers the admin Allocation Edit page's three header
 * actions (Approved / Refuse / Mark as Ready to Confirm), a lighter duplicate
 * check of the list's row-level Approve/Refuse (a separate code path per the
 * plugin source), the preset view tabs, and delete.
 *
 * The Allocations table has no "name" column (only employee/holiday-type/
 * amount/allocation-type/state are searchable columns - see AllocationResource
 * ::table()), so every row is located here by its Leave Type name rather than
 * the allocation's own `name` field - each test creates a fresh, uniquely
 * named Leave Type precisely so `searchList()` resolves to a single row.
 *
 * "Paul Williams" is one of the fixed employees the employees plugin seeds on
 * install (see EmployeeSeeder) - Allocation creation has no overlap/guardrail
 * checks (those only apply to Leave/Time Off requests), so reusing the same
 * seeded employee across every test here is safe even under parallel workers.
 */
const ALLOCATION_EMPLOYEE = "Paul Williams";

test.describe("Time Off Allocations", () => {
    test.beforeAll(async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        await timeOffPage.ensureBaseDependentPluginsInstalled();
    });

    /**
     * The Allocations listing table renders.
     */
    test("Listing Page", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        await timeOffPage.gotoAllocationsPage();
    });

    /**
     * Every new Allocation is created in the "To Approve" (confirm) state -
     * there is no draft state anywhere in this plugin.
     */
    test("Create allocation starts as To Approve", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        const key = Date.now();
        const leaveTypeName = `Alloc Leave Type ${key}`;

        await timeOffPage.createLeaveType({ name: leaveTypeName });
        await timeOffPage.createAllocation({
            name: `Allocation ${key}`,
            leaveTypeName,
            employeeName: ALLOCATION_EMPLOYEE,
            numberOfDays: "10",
        });

        await timeOffPage.gotoAllocationsPage();
        await timeOffPage.searchList(leaveTypeName);
        await expect(timeOffPage.erpLocators.timeOffTableRows.filter({ hasText: leaveTypeName }).first()).toBeVisible();
        await timeOffPage.expectStateBadge("To Approve");
    });

    /**
     * The Edit page's "Approved" header action (visible only while state is
     * "To Approve") sets state straight to Approved and then hides itself,
     * since it is only ever shown for a non-final state.
     */
    test("Approve from the Edit page reaches Approved and hides the action", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        const key = Date.now();
        const leaveTypeName = `Alloc Leave Type Appr ${key}`;

        await timeOffPage.createLeaveType({ name: leaveTypeName });
        await timeOffPage.createAllocation({
            name: `Allocation Approve ${key}`,
            leaveTypeName,
            employeeName: ALLOCATION_EMPLOYEE,
            numberOfDays: "5",
        });

        await timeOffPage.openAllocationEditPage(leaveTypeName);
        await timeOffPage.approveAllocationFromEditPage();

        await timeOffPage.gotoAllocationsPage();
        await timeOffPage.searchList(leaveTypeName);
        await timeOffPage.expectStateBadge("Approved");

        await timeOffPage.openAllocationEditPage(leaveTypeName);
        await expect(timeOffPage.erpLocators.timeOffAllocationEditApprovedHeaderButton).not.toBeVisible();
    });

    /**
     * The Edit page's "Refuse" header action sets state to Refused, which
     * reveals "Mark as Ready to Confirm" - the only reset-to-earlier-state
     * action in the whole plugin (there is no draft state). Clicking it
     * returns the record to "To Approve".
     */
    test("Refuse from the Edit page reveals Mark as Ready to Confirm, which resets to To Approve", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        const key = Date.now();
        const leaveTypeName = `Alloc Leave Type Refuse ${key}`;

        await timeOffPage.createLeaveType({ name: leaveTypeName });
        await timeOffPage.createAllocation({
            name: `Allocation Refuse ${key}`,
            leaveTypeName,
            employeeName: ALLOCATION_EMPLOYEE,
            numberOfDays: "5",
        });

        await timeOffPage.openAllocationEditPage(leaveTypeName);
        await timeOffPage.refuseAllocationFromEditPage();

        await timeOffPage.gotoAllocationsPage();
        await timeOffPage.searchList(leaveTypeName);
        await timeOffPage.expectStateBadge("Refused");

        await timeOffPage.openAllocationEditPage(leaveTypeName);
        await expect(timeOffPage.erpLocators.timeOffAllocationEditMarkReadyHeaderButton).toBeVisible();
        await timeOffPage.markAllocationReadyToConfirm();

        await timeOffPage.gotoAllocationsPage();
        await timeOffPage.searchList(leaveTypeName);
        await timeOffPage.expectStateBadge("To Approve");
    });

    /**
     * Lighter duplicate check: the list's row-level Approve action (a
     * separate code path from the Edit page's header action) also drives
     * state to Approved, and then hides itself since Approve is only visible
     * for a non-final state.
     */
    test("Row action Approve reaches Approved and then hides itself", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        const key = Date.now();
        const leaveTypeName = `Alloc Leave Type RowAppr ${key}`;

        await timeOffPage.createLeaveType({ name: leaveTypeName });
        await timeOffPage.createAllocation({
            name: `Allocation Row Approve ${key}`,
            leaveTypeName,
            employeeName: ALLOCATION_EMPLOYEE,
            numberOfDays: "4",
        });

        await timeOffPage.approveAllocation(leaveTypeName);

        await timeOffPage.gotoAllocationsPage();
        await timeOffPage.searchList(leaveTypeName);
        await timeOffPage.expectStateBadge("Approved");

        await timeOffPage.erpLocators.timeOffRowActionsButton.first().click();
        await expect(timeOffPage.erpLocators.timeOffMenuApproveAction).not.toBeVisible();
    });

    /**
     * Lighter duplicate check: the list's row-level Refuse action drives
     * state to Refused and then hides itself, but Approve remains available
     * since a refused record can still be re-approved from the row action.
     */
    test("Row action Refuse reaches Refused and Approve remains available", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        const key = Date.now();
        const leaveTypeName = `Alloc Leave Type RowRef ${key}`;

        await timeOffPage.createLeaveType({ name: leaveTypeName });
        await timeOffPage.createAllocation({
            name: `Allocation Row Refuse ${key}`,
            leaveTypeName,
            employeeName: ALLOCATION_EMPLOYEE,
            numberOfDays: "4",
        });

        await timeOffPage.refuseAllocation(leaveTypeName);

        await timeOffPage.gotoAllocationsPage();
        await timeOffPage.searchList(leaveTypeName);
        await timeOffPage.expectStateBadge("Refused");

        await timeOffPage.erpLocators.timeOffRowActionsButton.first().click();
        await expect(timeOffPage.erpLocators.timeOffMenuRefuseAction).not.toBeVisible();
        await expect(timeOffPage.erpLocators.timeOffMenuApproveAction).toBeVisible();
    });

    /**
     * The "Approved" and "Refused" preset view tabs each surface the record
     * matching their query - a smoke check of the same HasTableViews tabs
     * exercised on the Time Off list.
     */
    test("Preset views show Approved and Refused allocations", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        const key = Date.now();
        const approvedType = `Alloc Leave Type PVAppr ${key}`;
        const refusedType = `Alloc Leave Type PVRef ${key}`;

        await timeOffPage.createLeaveType({ name: approvedType });
        await timeOffPage.createAllocation({
            name: `Allocation PV Approve ${key}`,
            leaveTypeName: approvedType,
            employeeName: ALLOCATION_EMPLOYEE,
            numberOfDays: "3",
        });
        await timeOffPage.approveAllocation(approvedType);

        await timeOffPage.createLeaveType({ name: refusedType });
        await timeOffPage.createAllocation({
            name: `Allocation PV Refuse ${key}`,
            leaveTypeName: refusedType,
            employeeName: ALLOCATION_EMPLOYEE,
            numberOfDays: "3",
        });
        await timeOffPage.refuseAllocation(refusedType);

        await timeOffPage.gotoAllocationsPage();
        await timeOffPage.selectPresetView("Approved");
        await timeOffPage.searchList(approvedType);
        await expect(timeOffPage.erpLocators.timeOffTableRows.filter({ hasText: approvedType }).first()).toBeVisible();

        await timeOffPage.gotoAllocationsPage();
        await timeOffPage.selectPresetView("Refused");
        await timeOffPage.searchList(refusedType);
        await expect(timeOffPage.erpLocators.timeOffTableRows.filter({ hasText: refusedType }).first()).toBeVisible();
    });

    /**
     * An Allocation can be deleted from its grouped row actions.
     */
    test("Delete removes the allocation from the list", async ({ adminPage }) => {
        const timeOffPage = new TimeOffManagementPage(adminPage);
        const key = Date.now();
        const leaveTypeName = `Alloc Leave Type Del ${key}`;

        await timeOffPage.createLeaveType({ name: leaveTypeName });
        await timeOffPage.createAllocation({
            name: `Allocation Del ${key}`,
            leaveTypeName,
            employeeName: ALLOCATION_EMPLOYEE,
            numberOfDays: "2",
        });

        await timeOffPage.deleteAllocation(leaveTypeName);

        await timeOffPage.gotoAllocationsPage();
        await timeOffPage.searchList(leaveTypeName);
        await expect(timeOffPage.erpLocators.timeOffTableRows.filter({ hasText: leaveTypeName })).toHaveCount(0);
    });
});
