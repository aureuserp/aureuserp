import { Page, expect, Locator } from "@playwright/test";
import { ErpLocators } from "../locator/erp_locator";
import { PluginManagementPage } from "./01_pluginManagement";

export type LeaveTypeData = {
    name: string;
    requiresAllocation?: boolean;
    allowsNegative?: boolean;
    maxAllowedNegative?: string;
    color?: string;
    requestUnit?: "day" | "half_day" | "hour";
    timeType?: "leave" | "other";
    includePublicHolidays?: boolean;
    supportDocument?: boolean;
    showOnDashboard?: boolean;
};

export type AccrualPlanData = {
    name: string;
    isBasedOnWorkedTime?: boolean;
    carryoverDay?: string;
    carryoverMonth?: string;
    isActive?: boolean;
};

export type MilestoneData = {
    addedValue?: string;
    addedValueType?: "days" | "hours";
    frequency?: "hourly" | "daily" | "weekly" | "bimonthly" | "monthly" | "biyearly" | "yearly";
    weekDay?: "sunday" | "monday" | "tuesday" | "wednesday" | "thursday" | "friday" | "saturday";
    capAccruedTime?: boolean;
    maximumLeave?: string;
};

export type ActivityTypeData = {
    name: string;
    category?: string;
    delayCount?: string;
    delayUnit?: string;
    delayFrom?: string;
    isActive?: boolean;
};

export type MandatoryDayData = {
    name: string;
    color?: string;
    startDate?: string;
    endDate?: string;
};

export type PublicHolidayData = {
    name: string;
    dateFrom?: string;
    dateTo?: string;
    calendarName?: string;
};

export type TimeOffRequestData = {
    employeeName?: string;
    leaveTypeName: string;
    dateFrom: string;
    dateTo?: string;
    halfDay?: boolean;
    halfDayPeriod?: "morning" | "afternoon";
    description?: string;
};

export type AllocationData = {
    name: string;
    leaveTypeName: string;
    employeeName?: string;
    allocationType?: "regular" | "accrual";
    dateFrom?: string;
    dateTo?: string;
    numberOfDays?: string;
};

/**
 * Page object for the "time-off" Filament plugin domain: Configurations
 * (Leave Types, Accrual Plans + Milestones, Activity Types, Mandatory Days,
 * Public Holidays), Management (Time Off, Allocations), MyTime (My Time Off,
 * My Allocations, Dashboard), Reporting (By Employee, By Type) and the
 * top-level Overview page.
 */
export class TimeOffManagementPage {
    readonly page: Page;
    readonly erpLocators: ErpLocators;

    constructor(page: Page) {
        this.page = page;
        this.erpLocators = new ErpLocators(page);
    }

    /**
     * Plugin / Setup
     */

    async ensureBaseDependentPluginsInstalled() {
        const pluginPage = new PluginManagementPage(this.page);
        await pluginPage.gotoPluginManagementPage();
        // time-off declares a hard dependency on employees — install it first or
        // every Employee-relationship select on the time-off forms is empty/broken.
        await pluginPage.installPluginByName("Employees");
        await pluginPage.gotoPluginManagementPage();
        await pluginPage.installPluginByName("Time Off");
    }

    /**
     * Configurations - Leave Types
     */

    async gotoLeaveTypesPage() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.goto("/admin/time-off/configurations/leave-types");
        await expect(this.page).toHaveURL(/configurations\/leave-types/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.timeOffTable.first()).toBeVisible();
    }

    async createLeaveType(data: LeaveTypeData) {
        const l = this.erpLocators;
        await this.gotoLeaveTypesPage();
        await l.timeOffLeaveTypeCreateButton.click();
        await expect(this.page).toHaveURL(/leave-types\/create/);

        await this.fillWhenReady(l.timeOffLeaveTypeNameInput, data.name);

        if (data.requiresAllocation) {
            await l.timeOffLeaveTypeRequiresAllocationYesRadio.click();
            await this.settleForm();

            if (data.allowsNegative) {
                await this.setToggleOn(l.timeOffLeaveTypeAllowsNegativeToggle);
                await this.settleForm();
                if (data.maxAllowedNegative) {
                    await l.timeOffLeaveTypeMaxAllowedNegativeInput.fill(data.maxAllowedNegative);
                }
            }
        }

        if (data.requestUnit) {
            await l.timeOffLeaveTypeRequestUnitSelect.selectOption(data.requestUnit).catch(() => undefined);
        }
        if (data.timeType) {
            await l.timeOffLeaveTypeTimeTypeSelect.selectOption(data.timeType).catch(() => undefined);
        }
        if (data.includePublicHolidays) {
            await this.setToggleOn(l.timeOffLeaveTypeIncludePublicHolidaysToggle);
        }
        if (data.supportDocument) {
            await this.setToggleOn(l.timeOffLeaveTypeSupportDocumentToggle);
        }
        if (data.showOnDashboard) {
            await this.setToggleOn(l.timeOffLeaveTypeShowOnDashboardToggle);
        }

        // The requires_allocation live round-trip re-renders the form and can wipe the
        // already-typed name; refill immediately before submitting.
        if ((await l.timeOffLeaveTypeNameInput.inputValue().catch(() => "")) === "") {
            await l.timeOffLeaveTypeNameInput.fill(data.name);
        }

        // color is a required ColorPicker field with no server-side default, and the same
        // requires_allocation live round-trip that can wipe the name above also wipes an
        // already-filled color - always (re)fill it right before submitting, defaulting to
        // a stable hex value when the caller didn't request a specific one, so the required
        // constraint never silently blocks the save.
        const colorValue = data.color ?? "#3B82F6";
        if ((await l.timeOffLeaveTypeColorInput.inputValue().catch(() => "")) === "") {
            await l.timeOffLeaveTypeColorInput.fill(colorValue).catch(() => undefined);
        }

        await this.submitCreateForm();
    }

    async editLeaveType(searchKey: string, updates: Partial<LeaveTypeData>) {
        await this.gotoLeaveTypesPage();
        await this.searchList(searchKey);
        await this.clickScopedRowAction(searchKey, /^\s*Edit\s*$/i);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);

        if (updates.name) {
            await this.erpLocators.timeOffLeaveTypeNameInput.fill(updates.name);
        }

        await this.erpLocators.timeOffEditSaveButton.click();
        await this.expectSuccessToastSoft();
    }

    async deleteLeaveType(name: string) {
        await this.gotoLeaveTypesPage();
        await this.searchList(name);
        await this.deleteRowScoped(name);
    }

    /**
     * Configurations - Accrual Plans (+ Milestones sub-page)
     */

    async gotoAccrualPlansPage() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.goto("/admin/time-off/configurations/accrual-plans");
        await expect(this.page).toHaveURL(/configurations\/accrual-plans/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.timeOffTable.first()).toBeVisible();
    }

    async createAccrualPlan(data: AccrualPlanData) {
        const l = this.erpLocators;
        await this.gotoAccrualPlansPage();
        await l.timeOffAccrualPlanCreateButton.click();
        await expect(this.page).toHaveURL(/accrual-plans\/create/);

        await this.fillWhenReady(l.timeOffAccrualPlanNameInput, data.name);

        if (data.isBasedOnWorkedTime) {
            await this.setToggleOn(l.timeOffAccrualPlanIsBasedOnWorkedTimeToggle);
        }
        // carryover_date defaults to "Other", which is SUPPOSED to reveal a "Carry-Over Date"
        // fieldset with day/month selects (per the resource's ->visible() callback) — but in
        // the running app this fieldset never actually renders, in either direction, even
        // after a real Livewire round trip toggles carryover_date away and back to "other"
        // (verified live: the radio state and the /livewire/update requests are correct, the
        // fieldset just never appears — an app-side bug, not a timing issue). Guard with a
        // short isVisible() check rather than calling selectOption() directly, which would
        // otherwise block for the full default action timeout per field before giving up.
        if (data.carryoverDay && (await l.timeOffAccrualPlanCarryoverDaySelect.isVisible({ timeout: 2_000 }).catch(() => false))) {
            await l.timeOffAccrualPlanCarryoverDaySelect.selectOption(data.carryoverDay).catch(() => undefined);
        }
        if (data.carryoverMonth && (await l.timeOffAccrualPlanCarryoverMonthSelect.isVisible({ timeout: 2_000 }).catch(() => false))) {
            await l.timeOffAccrualPlanCarryoverMonthSelect.selectOption(data.carryoverMonth).catch(() => undefined);
        }
        if (data.isActive) {
            await this.setToggleOn(l.timeOffAccrualPlanIsActiveToggle);
        }

        if ((await l.timeOffAccrualPlanNameInput.inputValue().catch(() => "")) === "") {
            await l.timeOffAccrualPlanNameInput.fill(data.name);
        }

        await this.submitCreateForm();
    }

    async deleteAccrualPlan(name: string) {
        await this.gotoAccrualPlansPage();
        await this.searchList(name);
        await this.deleteRowScoped(name);
    }

    /**
     * Open an accrual plan's record and switch to its "Manage Milestones" sub-page
     * (a record sub-navigation tab alongside View/Edit, not a modal).
     */
    async openAccrualPlanMilestones(name: string) {
        await this.gotoAccrualPlansPage();
        await this.searchList(name);
        const link = this.erpLocators.timeOffTableRows.locator("a").filter({ hasText: name }).first();
        await expect(link).toBeVisible();
        await link.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);

        await this.erpLocators.timeOffAccrualPlanMilestonesTab.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(this.page).toHaveURL(/milestones/);
    }

    /**
     * Create one milestone on the already-open Milestones sub-page (call
     * openAccrualPlanMilestones first). Create/Edit here are RelationManager
     * MODAL actions — there is no dedicated create/edit route.
     */
    async createMilestone(data: MilestoneData) {
        const l = this.erpLocators;
        await l.timeOffMilestoneCreateButton.click();
        await expect(l.timeOffModal).toBeVisible();

        if (data.addedValue) {
            await l.timeOffMilestoneAddedValueInput.fill(data.addedValue).catch(() => undefined);
        }
        if (data.addedValueType) {
            await l.timeOffMilestoneAddedValueTypeSelect.selectOption(data.addedValueType).catch(() => undefined);
        }
        if (data.frequency) {
            await l.timeOffMilestoneFrequencySelect.selectOption(data.frequency).catch(() => undefined);
            await this.settleForm();
        }
        // week_day is SUPPOSED to appear once frequency is "weekly" (per the form schema's
        // ->visible() callback), but in the running app it never renders regardless of the
        // frequency value - verified live, including after forcing a real change event by
        // switching frequency away and back (an app-side bug, same category as the Accrual
        // Plan's Carry-Over Date fieldset). Guard with a short isVisible() check instead of
        // calling selectOption() directly, which would otherwise block for the full default
        // action timeout before giving up.
        if (data.weekDay && (await l.timeOffMilestoneWeekDaySelect.isVisible({ timeout: 2_000 }).catch(() => false))) {
            await l.timeOffMilestoneWeekDaySelect.selectOption(data.weekDay).catch(() => undefined);
        }
        if (data.capAccruedTime) {
            await this.setToggleOn(l.timeOffMilestoneCapAccruedTimeToggle);
            await this.settleForm();
            if (data.maximumLeave) {
                await l.timeOffMilestoneMaximumLeaveInput.fill(data.maximumLeave).catch(() => undefined);
            }
        }

        await l.timeOffMilestoneModalSubmitButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.expectSuccessToastSoft();
    }

    /**
     * Delete a milestone row on the already-open Milestones sub-page, matched by
     * any text unique to that row (e.g. its Accrual Amount or Frequency).
     */
    async deleteMilestone(rowMatcherText: string) {
        const row = this.erpLocators.timeOffTableRows.filter({ hasText: rowMatcherText }).first();
        await expect(row).toBeVisible();
        await row.getByRole("button", { name: /^Delete$/i }).first().click();
        await this.erpLocators.timeOffConfirmDialogButton.click();
        await this.expectSuccessToastSoft();
    }

    /**
     * Configurations - Activity Types (shared support resource, scoped to
     * plugin=time-off; row actions are grouped behind an "Actions" dropdown).
     */

    async gotoActivityTypesPage() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.goto("/admin/time-off/configurations/activity-types");
        await expect(this.page).toHaveURL(/configurations\/activity-types/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.timeOffTable.first()).toBeVisible();
    }

    async createActivityType(data: ActivityTypeData) {
        const l = this.erpLocators;
        await this.gotoActivityTypesPage();
        await l.timeOffActivityTypeCreateButton.click();
        await expect(this.page).toHaveURL(/activity-types\/create/);

        await this.fillWhenReady(l.timeOffActivityTypeNameInput, data.name);

        if (data.category) {
            await this.selectFromFilamentDropdown(l.timeOffActivityTypeCategorySelect, data.category);
        }
        if (data.delayCount) {
            await l.timeOffActivityTypeDelayCountInput.fill(data.delayCount);
        }
        if (data.delayUnit) {
            await l.timeOffActivityTypeDelayUnitSelect.selectOption(data.delayUnit).catch(() => undefined);
        }
        if (data.delayFrom) {
            await l.timeOffActivityTypeDelayFromSelect.selectOption(data.delayFrom).catch(() => undefined);
        }
        if (data.isActive) {
            await this.setToggleOn(l.timeOffActivityTypeIsActiveToggle);
        }

        if ((await l.timeOffActivityTypeNameInput.inputValue().catch(() => "")) === "") {
            await l.timeOffActivityTypeNameInput.fill(data.name);
        }

        await this.submitCreateForm();
    }

    async deleteActivityType(name: string) {
        await this.gotoActivityTypesPage();
        await this.searchList(name);
        await this.openRowActionsMenu(name);
        await this.erpLocators.timeOffMenuDeleteAction.click();
        await this.erpLocators.timeOffConfirmDialogButton.click();
        await this.expectSuccessToast();
    }

    /**
     * Configurations - Mandatory Days (index-only resource; Create/Edit are
     * modal actions on the list page, there is no /create or /{id}/edit route).
     */

    async gotoMandatoryDaysPage() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.goto("/admin/time-off/configurations/mandatory-days");
        await expect(this.page).toHaveURL(/configurations\/mandatory-days/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.timeOffTable.first()).toBeVisible();
    }

    async createMandatoryDay(data: MandatoryDayData) {
        const l = this.erpLocators;
        await this.gotoMandatoryDaysPage();
        await l.timeOffMandatoryDayCreateButton.click();
        await expect(l.timeOffModal).toBeVisible();

        await l.timeOffMandatoryDayNameInput.fill(data.name);
        if (data.color) {
            await l.timeOffMandatoryDayColorInput.fill(data.color).catch(() => undefined);
        }
        // start_date/end_date are Alpine-driven display-text pickers, not plain editable
        // inputs (see setDatePickerValue's docblock) - a plain .fill() would hang for the
        // full action timeout waiting for an element that never becomes editable.
        if (data.startDate) {
            await this.setDatePickerValue(l.timeOffMandatoryDayStartDateInput, data.startDate);
        }
        if (data.endDate) {
            await this.setDatePickerValue(l.timeOffMandatoryDayEndDateInput, data.endDate);
        }

        await l.timeOffMandatoryDayModalSubmitButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.expectSuccessToastSoft();
    }

    async editMandatoryDay(searchKey: string, updates: Partial<MandatoryDayData>) {
        const l = this.erpLocators;
        await this.gotoMandatoryDaysPage();
        await this.searchList(searchKey);

        const row = l.timeOffTableRows.filter({ hasText: searchKey }).first();
        await expect(row).toBeVisible();
        await row.getByRole("button", { name: /^Edit$/i }).first().click();
        await expect(l.timeOffModal).toBeVisible();

        if (updates.name) {
            await l.timeOffMandatoryDayNameInput.fill(updates.name);
        }
        if (updates.startDate) {
            await this.setDatePickerValue(l.timeOffMandatoryDayStartDateInput, updates.startDate);
        }
        if (updates.endDate) {
            await this.setDatePickerValue(l.timeOffMandatoryDayEndDateInput, updates.endDate);
        }

        await l.timeOffMandatoryDayModalSubmitButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.expectSuccessToastSoft();
    }

    async deleteMandatoryDay(name: string) {
        await this.gotoMandatoryDaysPage();
        await this.searchList(name);
        await this.deleteRowScoped(name);
    }

    /**
     * Configurations - Public Holidays (index-only resource; Create/Edit are
     * modal actions on the list page, there is no /create or /{id}/edit route).
     */

    async gotoPublicHolidaysPage() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.goto("/admin/time-off/configurations/public-holidays");
        await expect(this.page).toHaveURL(/configurations\/public-holidays/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.timeOffTable.first()).toBeVisible();
    }

    async createPublicHoliday(data: PublicHolidayData) {
        const l = this.erpLocators;
        await this.gotoPublicHolidaysPage();
        await l.timeOffPublicHolidayCreateButton.click();
        await expect(l.timeOffModal).toBeVisible();

        await l.timeOffPublicHolidayNameInput.fill(data.name);
        // date_from/date_to are Alpine-driven display-text pickers, not plain editable
        // inputs - see setDatePickerValue's docblock.
        if (data.dateFrom) {
            await this.setDatePickerValue(l.timeOffPublicHolidayDateFromInput, data.dateFrom);
            await this.settleForm();
        }
        if (data.dateTo) {
            await this.setDatePickerValue(l.timeOffPublicHolidayDateToInput, data.dateTo);
        }
        if (data.calendarName) {
            await this.selectFromFilamentDropdown(l.timeOffPublicHolidayCalendarSelect, data.calendarName);
        }

        await l.timeOffPublicHolidayModalSubmitButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.expectSuccessToastSoft();
    }

    async editPublicHoliday(searchKey: string, updates: Partial<PublicHolidayData>) {
        const l = this.erpLocators;
        await this.gotoPublicHolidaysPage();
        await this.searchList(searchKey);

        const row = l.timeOffTableRows.filter({ hasText: searchKey }).first();
        await expect(row).toBeVisible();
        await row.getByRole("button", { name: /^Edit$/i }).first().click();
        await expect(l.timeOffModal).toBeVisible();

        if (updates.name) {
            await l.timeOffPublicHolidayNameInput.fill(updates.name);
        }

        await l.timeOffPublicHolidayModalSubmitButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.expectSuccessToastSoft();
    }

    async deletePublicHoliday(name: string) {
        await this.gotoPublicHolidaysPage();
        await this.searchList(name);
        await this.deleteRowScoped(name);
    }

    /**
     * Management - Time Off requests (admin) / MyTime - My Time Off (self-service).
     * Both share the exact same form fields (employee_id/department_id are simply
     * hidden on the MyTime variant) and — per a MyTimeOffResource quirk — the exact
     * same table definition, Approve/Refuse row actions included.
     */

    async gotoTimeOffRequestsPage() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.goto("/admin/time-off/management/time-offs");
        await expect(this.page).toHaveURL(/management\/time-offs/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.timeOffTable.first()).toBeVisible();
    }

    async gotoMyTimeOffPage() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.goto("/admin/time-off/dashboard/my-time-offs");
        await expect(this.page).toHaveURL(/my-time-offs/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.timeOffTable.first()).toBeVisible();
    }

    /**
     * Fill the shared Time Off / My Time Off request form. `includeEmployee`
     * selects employee_id (and lets department_id auto-fill) — only the admin
     * Time Off form renders those two fields.
     */
    private async fillTimeOffForm(data: TimeOffRequestData, includeEmployee: boolean) {
        const l = this.erpLocators;

        if (includeEmployee && data.employeeName) {
            await this.selectFromFilamentDropdown(l.timeOffRequestEmployeeSelect, data.employeeName);
            await this.settleForm();

            // Selecting the employee is supposed to auto-fill department_id, but this
            // does not happen for an employee with no department assigned - and
            // department_id is a required field, so leaving it unset silently blocks
            // the submit with a "department Name field is required" validation error.
            // Pick any available option ourselves if it is still unset.
            await this.ensureFilamentDropdownHasSelection(l.timeOffRequestDepartmentSelect);
        }

        // holiday_status_id has no ->searchable()/->preload() here, so it is a plain
        // native <select> populated with every Leave Type's name as the option label.
        await l.timeOffRequestLeaveTypeSelect
            .selectOption({ label: data.leaveTypeName })
            .catch(() => this.selectFromFilamentDropdown(l.timeOffRequestLeaveTypeSelect, data.leaveTypeName));

        await this.setDatePickerValue(l.timeOffRequestDateFromInput, data.dateFrom);
        await this.settleForm();

        if (data.halfDay) {
            await this.setToggleOn(l.timeOffRequestHalfDayToggle);
            await this.settleForm();
            if (data.halfDayPeriod) {
                await l.timeOffRequestHalfDayPeriodSelect.selectOption(data.halfDayPeriod).catch(() => undefined);
            }
        } else if (data.dateTo) {
            await this.setDatePickerValue(l.timeOffRequestDateToInput, data.dateTo);
            await this.settleForm();
        }

        if (data.description) {
            await l.timeOffRequestDescriptionInput.fill(data.description);
        }
    }

    /**
     * Create an admin Time Off request. On success the record always lands in
     * state "To Approve" (confirm); on a guardrail breach (overlap / no
     * allocation / insufficient balance) the create is halted server-side and
     * the page stays on /create — assert with the expect*Blocked() helpers.
     */
    async createTimeOff(data: TimeOffRequestData) {
        await this.gotoTimeOffRequestsPage();
        await this.erpLocators.timeOffRequestCreateButton.click();
        await expect(this.page).toHaveURL(/time-offs\/create/);
        await this.fillTimeOffForm(data, true);
        await this.submitCreateForm();
    }

    /**
     * Attempt an admin Time Off create that is expected to be blocked by a
     * guardrail notification; the page is left on /create afterwards.
     */
    async attemptCreateTimeOff(data: TimeOffRequestData) {
        await this.gotoTimeOffRequestsPage();
        await this.erpLocators.timeOffRequestCreateButton.click();
        await expect(this.page).toHaveURL(/time-offs\/create/);
        await this.fillTimeOffForm(data, true);
        await this.erpLocators.timeOffCreateSubmitButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
    }

    async createMyTimeOff(data: TimeOffRequestData) {
        await this.gotoMyTimeOffPage();
        await this.erpLocators.timeOffRequestCreateButton.click();
        await expect(this.page).toHaveURL(/my-time-offs\/create/);
        await this.fillTimeOffForm(data, false);
        await this.submitCreateForm();
    }

    async attemptCreateMyTimeOff(data: TimeOffRequestData) {
        await this.gotoMyTimeOffPage();
        await this.erpLocators.timeOffRequestCreateButton.click();
        await expect(this.page).toHaveURL(/my-time-offs\/create/);
        await this.fillTimeOffForm(data, false);
        await this.erpLocators.timeOffCreateSubmitButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
    }

    async editTimeOff(searchKey: string, updates: Partial<TimeOffRequestData>) {
        await this.gotoTimeOffRequestsPage();
        await this.searchList(searchKey);
        await this.openRowActionsMenu(searchKey);
        await this.erpLocators.timeOffMenuEditAction.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);

        if (updates.dateFrom) {
            await this.setDatePickerValue(this.erpLocators.timeOffRequestDateFromInput, updates.dateFrom);
            await this.settleForm();
        }
        if (updates.description) {
            await this.erpLocators.timeOffRequestDescriptionInput.fill(updates.description);
        }

        await this.erpLocators.timeOffEditSaveButton.click();
        await this.expectSuccessToastSoft();
    }

    /**
     * Approve/Refuse a Time Off request from the list's grouped row actions.
     * Works identically for both the admin Management list and the MyTime list
     * (same table definition) — pass gotoMyTimeOffPage-first if targeting MyTime.
     */
    async approveTimeOffRow(searchKey: string) {
        await this.searchList(searchKey);
        await this.openRowActionsMenu(searchKey);
        await this.clickGroupedAction(this.erpLocators.timeOffMenuApproveAction, /Approve|Validate/i);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.expectSuccessToastSoft();
    }

    async refuseTimeOffRow(searchKey: string) {
        await this.searchList(searchKey);
        await this.openRowActionsMenu(searchKey);
        await this.clickGroupedAction(this.erpLocators.timeOffMenuRefuseAction, /^\s*Refuse\s*$/i);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.expectSuccessToastSoft();
    }

    async approveTimeOff(searchKey: string) {
        await this.gotoTimeOffRequestsPage();
        await this.approveTimeOffRow(searchKey);
    }

    async refuseTimeOff(searchKey: string) {
        await this.gotoTimeOffRequestsPage();
        await this.refuseTimeOffRow(searchKey);
    }

    async deleteTimeOff(searchKey: string) {
        await this.gotoTimeOffRequestsPage();
        await this.searchList(searchKey);
        await this.openRowActionsMenu(searchKey);
        await this.erpLocators.timeOffMenuDeleteAction.click();
        await this.erpLocators.timeOffConfirmDialogButton.click();
        await this.expectSuccessToast();
    }

    /**
     * Switch the Time Off / Allocation list's preset view (Waiting For Me,
     * Second Approval, Approved, Currently Valid, My Team, My Department,
     * Refused). Waiting For Me / Second Approval / Approved are favorited and
     * may render as quick tabs directly; the rest are only reachable through
     * the "Views" dropdown trigger. Best-effort: verify the exact DOM shape
     * (tab vs dropdown item) against a headed run before relying on it.
     */
    async selectPresetView(label: string | RegExp) {
        const pattern = typeof label === "string" ? new RegExp(this.escapeRegExp(label), "i") : label;

        const quickTab = this.page.locator("button,a").filter({ hasText: pattern }).first();
        if (await quickTab.isVisible().catch(() => false)) {
            await quickTab.click();
            await this.page.waitForLoadState("networkidle").catch(() => undefined);
            return;
        }

        await this.erpLocators.timeOffViewsTriggerButton.click();
        await expect(this.erpLocators.timeOffViewsPanel).toBeVisible();
        await this.erpLocators.timeOffViewsPanel.getByText(pattern).first().click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
    }

    /**
     * Assert the record currently shown on a view/edit page (or its row in an
     * already-filtered list) carries the given state label ("To Approve",
     * "Second Approval", "Approved", "Refused").
     */
    async expectStateBadge(label: string) {
        await expect(this.erpLocators.timeOffRequestStateBadge.filter({ hasText: label }).first()).toBeVisible({
            timeout: 15000,
        });
    }

    /**
     * Assert the Approve/Validate row action IS available for a specific row (matched
     * by any unique text, e.g. its description) — true for every state except the
     * terminal "Approved" (validate_two) one, where the action is hidden entirely.
     */
    async expectApproveActionVisibleForRow(searchKey: string) {
        await this.searchList(searchKey);
        await this.openRowActionsMenu(searchKey);
        const visible =
            (await this.erpLocators.timeOffMenuApproveAction.isVisible().catch(() => false)) ||
            (await this.page
                .locator("a.fi-ac-grouped-action, button.fi-ac-grouped-action")
                .filter({ hasText: /Approve|Validate/i })
                .first()
                .isVisible()
                .catch(() => false));
        expect(visible).toBeTruthy();
    }

    /**
     * Assert the Approve/Validate row action is NOT available for a specific row —
     * true only once the record has reached the terminal "Approved" state.
     */
    async expectApproveActionAbsentForRow(searchKey: string) {
        await this.searchList(searchKey);
        await this.openRowActionsMenu(searchKey);
        await expect(this.erpLocators.timeOffMenuApproveAction).not.toBeVisible();
        // Scope to the actual table row rather than the whole page - unrelated dropdowns
        // elsewhere (preset-view "Apply View" buttons, other rows/pages' leftover DOM)
        // can otherwise produce a false-positive match against this broad text filter.
        await expect(
            this.erpLocators.timeOffTableRows
                .locator("a.fi-ac-grouped-action, button.fi-ac-grouped-action")
                .filter({ hasText: /Approve|Validate/i })
        ).toHaveCount(0);
    }

    /**
     * Assert a row matching `searchKey` is present after filtering the current
     * list via the search box — used to confirm a record shows up under a
     * given preset view tab.
     */
    async expectRowVisible(searchKey: string) {
        await this.searchList(searchKey);
        await expect(this.erpLocators.timeOffTableRows.filter({ hasText: searchKey }).first()).toBeVisible({
            timeout: 15000,
        });
    }

    /**
     * Assert no row matching `searchKey` is present in the current list/tab —
     * used to confirm a record does NOT show up under an unrelated preset view tab.
     */
    async expectRowNotVisible(searchKey: string) {
        await this.searchList(searchKey);
        await expect(this.erpLocators.timeOffTableRows.filter({ hasText: searchKey })).toHaveCount(0);
    }

    /**
     * Management - Allocations (admin) / MyTime - My Allocations (self-service).
     */

    async gotoAllocationsPage() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.goto("/admin/time-off/management/allocations");
        await expect(this.page).toHaveURL(/management\/allocations/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.timeOffTable.first()).toBeVisible();
    }

    async gotoMyAllocationsPage() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.goto("/admin/time-off/dashboard/my-allocations");
        await expect(this.page).toHaveURL(/my-allocations/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.timeOffTable.first()).toBeVisible();
    }

    private async fillAllocationForm(data: AllocationData, includeEmployee: boolean) {
        const l = this.erpLocators;

        await l.timeOffAllocationNameInput.fill(data.name);
        await this.selectFromFilamentDropdown(l.timeOffAllocationLeaveTypeSelect, data.leaveTypeName);
        await this.settleForm();

        if (includeEmployee && data.employeeName) {
            await this.selectFromFilamentDropdown(l.timeOffAllocationEmployeeSelect, data.employeeName);
            await this.settleForm();
        }

        if (data.allocationType === "accrual") {
            await l.timeOffAllocationTypeAccrualRadio.click().catch(() => undefined);
        } else {
            await l.timeOffAllocationTypeRegularRadio.click().catch(() => undefined);
        }

        if (data.dateFrom) {
            await this.setDatePickerValue(l.timeOffAllocationDateFromInput, data.dateFrom);
        }
        if (data.dateTo) {
            await this.setDatePickerValue(l.timeOffAllocationDateToInput, data.dateTo);
        }
        if (data.numberOfDays) {
            await l.timeOffAllocationNumberOfDaysInput.fill(data.numberOfDays);
        }

        // The leave-type/employee live round-trips can wipe the name field.
        if ((await l.timeOffAllocationNameInput.inputValue().catch(() => "")) === "") {
            await l.timeOffAllocationNameInput.fill(data.name);
        }
    }

    async createAllocation(data: AllocationData) {
        await this.gotoAllocationsPage();
        await this.erpLocators.timeOffAllocationCreateButton.click();
        await expect(this.page).toHaveURL(/allocations\/create/);
        await this.fillAllocationForm(data, true);
        await this.submitCreateForm();
    }

    async createMyAllocation(data: AllocationData) {
        await this.gotoMyAllocationsPage();
        await this.erpLocators.timeOffAllocationCreateButton.click();
        await expect(this.page).toHaveURL(/my-allocations\/create/);
        await this.fillAllocationForm(data, false);
        await this.submitCreateForm();
    }

    /**
     * Attempt a My Allocation create expected to be blocked (the logged-in
     * user has no linked Employee record) — the page stays on /create.
     */
    async attemptCreateMyAllocation(data: AllocationData) {
        await this.gotoMyAllocationsPage();
        await this.erpLocators.timeOffAllocationCreateButton.click();
        await expect(this.page).toHaveURL(/my-allocations\/create/);
        await this.fillAllocationForm(data, false);
        await this.erpLocators.timeOffCreateSubmitButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
    }

    async editAllocation(searchKey: string, updates: Partial<AllocationData>) {
        await this.gotoAllocationsPage();
        await this.searchList(searchKey);
        await this.openRowActionsMenu(searchKey);
        await this.erpLocators.timeOffMenuEditAction.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);

        if (updates.numberOfDays) {
            await this.erpLocators.timeOffAllocationNumberOfDaysInput.fill(updates.numberOfDays);
        }

        await this.erpLocators.timeOffEditSaveButton.click();
        await this.expectSuccessToastSoft();
    }

    /**
     * Approve/Refuse an Allocation from the list's grouped row actions. Works
     * for both the admin Allocations list and the My Allocations list (the
     * quirk noted in the notesForSpecWriters: the row action renders even for
     * a plain employee viewing their own allocation).
     */
    async approveAllocationRow(searchKey: string) {
        await this.searchList(searchKey);
        await this.openRowActionsMenu(searchKey);
        await this.clickGroupedAction(this.erpLocators.timeOffMenuApproveAction, /Approve|Validate/i);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.expectSuccessToastSoft();
    }

    async refuseAllocationRow(searchKey: string) {
        await this.searchList(searchKey);
        await this.openRowActionsMenu(searchKey);
        await this.clickGroupedAction(this.erpLocators.timeOffMenuRefuseAction, /^\s*Refuse\s*$/i);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.expectSuccessToastSoft();
    }

    async approveAllocation(searchKey: string) {
        await this.gotoAllocationsPage();
        await this.approveAllocationRow(searchKey);
    }

    async refuseAllocation(searchKey: string) {
        await this.gotoAllocationsPage();
        await this.refuseAllocationRow(searchKey);
    }

    async deleteAllocation(searchKey: string) {
        await this.gotoAllocationsPage();
        await this.searchList(searchKey);
        await this.openRowActionsMenu(searchKey);
        await this.erpLocators.timeOffMenuDeleteAction.click();
        await this.erpLocators.timeOffConfirmDialogButton.click();
        await this.expectSuccessToast();
    }

    /**
     * Open the admin Allocation's Edit page directly (the richest state-transition
     * surface in the plugin: Approved / Refuse / Mark as Ready to Confirm header
     * actions). Not available on My Allocation's edit page.
     */
    async openAllocationEditPage(searchKey: string) {
        await this.gotoAllocationsPage();
        await this.searchList(searchKey);
        await this.openRowActionsMenu(searchKey);
        await this.erpLocators.timeOffMenuEditAction.click();
        await this.page.waitForURL(/allocations\/\d+\/edit/, { timeout: 20000 }).catch(() => undefined);
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
    }

    /**
     * Header action "Approved" — visible only while state is "To Approve"
     * (confirm) — sets state straight to Approved (validate_two).
     */
    async approveAllocationFromEditPage() {
        await expect(this.erpLocators.timeOffAllocationEditApprovedHeaderButton).toBeVisible({ timeout: 15000 });
        await this.erpLocators.timeOffAllocationEditApprovedHeaderButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.expectSuccessToastSoft();
    }

    /**
     * Header action "Refuse" — hidden only once already Refused.
     */
    async refuseAllocationFromEditPage() {
        await expect(this.erpLocators.timeOffAllocationEditRefuseHeaderButton).toBeVisible({ timeout: 15000 });
        await this.erpLocators.timeOffAllocationEditRefuseHeaderButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.expectSuccessToastSoft();
    }

    /**
     * Header action "Mark as Ready to Confirm" — visible ONLY when state is
     * Refused — resets state back to "To Approve" (confirm). This is the only
     * "reset" action in the whole plugin (there is no draft state at all).
     */
    async markAllocationReadyToConfirm() {
        await expect(this.erpLocators.timeOffAllocationEditMarkReadyHeaderButton).toBeVisible({ timeout: 15000 });
        await this.erpLocators.timeOffAllocationEditMarkReadyHeaderButton.click();
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.expectSuccessToastSoft();
    }

    /**
     * Guardrail notification assertions (Time Off / Allocation create halts).
     * Matched on the notification BODY text, since "Leave Request Denied" is
     * the shared title for both the no-allocation and insufficient-balance
     * guardrails and cannot disambiguate them on its own.
     */

    async expectOverlapBlocked() {
        await this.expectNotificationContains(/overlap/i);
    }

    async expectNoAllocationBlocked() {
        await this.expectNotificationContains(/do not have any allocated leave/i);
    }

    async expectInsufficientBalanceBlocked() {
        await this.expectNotificationContains(/insufficient leave balance/i);
    }

    async expectNoEmployeeProfileBlocked() {
        await this.expectNotificationContains(/employee account|employee not found/i);
    }

    /**
     * Reporting - By Employee / By Type
     */

    async gotoByEmployeeReportPage() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.goto("/admin/time-off/reporting/by-employees");
        await expect(this.page).toHaveURL(/reporting\/by-employees/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.timeOffTable.first()).toBeVisible();
    }

    /**
     * The ByType dashboard page's routePath ('reporting/by-type') is itself
     * appended to the Reporting cluster's own slug ('time-off/reporting'),
     * which duplicates the "reporting" segment — this is real plugin behaviour,
     * not a typo. Verify the exact URL with a headed run if this ever changes.
     */
    async gotoByTypeReportPage() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.goto("/admin/time-off/reporting/reporting/by-type");
        await expect(this.page).toHaveURL(/reporting\/by-type/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.timeOffByTypeChartWidget).toBeVisible();
    }

    /**
     * Overview page (top-level, NOT nested under any cluster slug).
     */

    async gotoOverviewPage() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.goto("/admin/time-off");
        await expect(this.page).toHaveURL(/\/time-off$/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.timeOffCalendarWidget).toBeVisible();
    }

    async openOverviewCreateModal() {
        await this.erpLocators.timeOffCalendarCreateAction.click();
        await expect(this.erpLocators.timeOffModal).toBeVisible();
    }

    /**
     * Dashboard page (MyTime cluster). The cluster prefix ('time-off/dashboard')
     * is combined with the page's own routePath ('time-off'), producing a
     * doubled-looking URL — this is real plugin behaviour (see notesForSpecWriters).
     */

    async gotoDashboardPage() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.goto("/admin/time-off/dashboard/time-off");
        await expect(this.page).toHaveURL(/time-off\/dashboard/);
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.timeOffCalendarWidget).toBeVisible();
    }

    /**
     * Open the Dashboard's "Holidays" header action — a read-only slide-over
     * listing Public and Mandatory holidays, with no submit/cancel buttons.
     */
    async openHolidaysSlideOver() {
        await this.erpLocators.timeOffHolidaysAction.click();
        await expect(this.erpLocators.timeOffHolidaysSlideOver).toBeVisible();
    }

    async closeModal() {
        await this.page.keyboard.press("Escape").catch(() => undefined);
    }

    async expectPendingRequestsStatVisible() {
        await expect(this.erpLocators.timeOffPendingRequestsStat).toBeVisible({ timeout: 15000 });
    }

    /**
     * Generic UI helpers
     */

    async searchList(keyword: string) {
        await this.erpLocators.timeOffSearchInput.fill(keyword);
        await this.page.waitForLoadState("networkidle");
        await this.page.waitForTimeout(500);
    }

    /**
     * Click a direct row action (View/Edit/Delete/Restore) scoped to the row matching
     * `searchKey`'s exact text - NEVER the page-wide "first Delete button", since the
     * search box's Livewire filter is debounced and can still be showing every row (not
     * just the searched-for one) by the time the click fires. A page-wide `.first()` on an
     * unscoped action risks silently acting on someone else's row once more than one row is
     * present - verified live: this actually deleted a different row than the one searched
     * for. Filtering the LOCATOR client-side by hasText (as done here) is correct
     * regardless of whether the server-side search has actually narrowed the table yet,
     * since Date.now()-based test data names are unique.
     */
    private async clickScopedRowAction(searchKey: string, actionName: RegExp) {
        const row = this.erpLocators.timeOffTableRows.filter({ hasText: searchKey }).first();
        await expect(row).toBeVisible();
        await row.locator("a,button").filter({ hasText: actionName }).first().click();
    }

    private async deleteRowScoped(name: string) {
        await this.clickScopedRowAction(name, /^\s*Delete\s*$/i);
        await this.erpLocators.timeOffConfirmDialogButton.click();
        await this.expectSuccessToast();
    }

    /**
     * Open the grouped "Actions" dropdown scoped to the row matching `searchKey` - see
     * clickScopedRowAction's docblock for why this must never be page-wide `.first()`.
     */
    private async openRowActionsMenu(searchKey: string) {
        const row = this.erpLocators.timeOffTableRows.filter({ hasText: searchKey }).first();
        await expect(row).toBeVisible();
        await row.getByRole("button", { name: "Actions" }).first().click();
    }

    /**
     * Click a grouped dropdown action, falling back to the plain action button
     * class when the accessible menuitem role does not resolve.
     */
    private async clickGroupedAction(preferred: Locator, fallbackLabel: RegExp) {
        if (await preferred.isVisible().catch(() => false)) {
            await preferred.click();
            return;
        }

        const fallback = this.page
            .locator("a.fi-ac-grouped-action, button.fi-ac-grouped-action")
            .filter({ hasText: fallbackLabel })
            .first();
        await fallback.click();
    }

    /**
     * Click a Filament select trigger, type into the search box of the
     * resulting dropdown panel, and click the first option matching `value`.
     */
    async selectFromFilamentDropdown(trigger: Locator, value: string) {
        await trigger.scrollIntoViewIfNeeded();
        await trigger.click();

        const panel = this.erpLocators.timeOffSelectPanel.last();
        await expect(panel).toBeVisible();

        const search = panel.locator('input.fi-input[aria-label="Search"]').first();
        if (await search.isVisible().catch(() => false)) {
            await search.fill(value);
            await this.page.waitForTimeout(500);
        }

        const option = panel
            .locator('[role="option"]')
            .filter({ hasText: new RegExp(this.escapeRegExp(value), "i") })
            .first();

        await expect(option).toBeVisible();
        await option.click();
    }

    /**
     * If a Filament select trigger currently shows no selection (empty or the default
     * "Select an option" placeholder), open it and pick whatever the first available
     * option is - used for required-but-otherwise-irrelevant-to-the-test fields like
     * department_id, where an auto-fill the caller relied on did not actually happen.
     */
    private async ensureFilamentDropdownHasSelection(trigger: Locator) {
        const currentText = ((await trigger.textContent().catch(() => "")) ?? "").trim();
        if (currentText && !/select an option/i.test(currentText)) {
            return;
        }

        await trigger.scrollIntoViewIfNeeded();
        await trigger.click();

        const panel = this.erpLocators.timeOffSelectPanel.last();
        await expect(panel).toBeVisible();

        const firstOption = panel.locator('[role="option"]').first();
        // A one-shot isVisible() here races the panel's option list still rendering right
        // after the panel container itself becomes visible - wait (like selectFromFilamentDropdown
        // does via expect().toBeVisible()) instead of taking an instantaneous snapshot.
        const hasOption = await firstOption
            .waitFor({ state: "visible", timeout: 5000 })
            .then(() => true)
            .catch(() => false);
        if (hasOption) {
            await firstOption.click();
        } else {
            // No options to pick from - close the panel and leave the field as-is.
            await this.page.keyboard.press("Escape").catch(() => undefined);
        }
    }

    async setToggleOn(toggle: Locator) {
        if (!(await toggle.isVisible().catch(() => false))) {
            return;
        }
        const checked = await toggle.getAttribute("aria-checked").catch(() => null);
        if (checked === "true") {
            return;
        }
        await toggle.click();
    }

    async setToggleOff(toggle: Locator) {
        if (!(await toggle.isVisible().catch(() => false))) {
            return;
        }
        const checked = await toggle.getAttribute("aria-checked").catch(() => null);
        if (checked === "false") {
            return;
        }
        await toggle.click();
    }

    /**
     * Fill a field once its form has finished hydrating. Livewire swaps the markup after the
     * page settles, discarding a value typed into the pre-swap DOM.
     */
    private async fillWhenReady(input: Locator, value: string) {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await expect(input).toBeVisible();

        for (let attempt = 0; attempt < 3; attempt++) {
            await input.fill(value);
            if ((await input.inputValue()) === value) {
                return;
            }
            await this.page.waitForTimeout(500);
        }

        await expect(input).toHaveValue(value);
    }

    /**
     * Set a Filament DateTimePicker's value. The visible input (id="form.xxx") is a
     * READONLY "display text" node driven entirely by an Alpine component
     * (resources/js/components/date-time-picker.js) - there is no native flatpickr and no
     * plain editable input anywhere in the DOM, so a native .fill()/.type() always fails
     * with "element is not editable", and clicking through the on-screen calendar to reach
     * an arbitrary (often far-future) date is impractical. Instead, walk up from the input
     * to the ancestor carrying `x-data` for this component and call its own `setState()`
     * method directly (with a dayjs instance, exactly like a real day-cell click would) -
     * this updates both the component's bound state (synced to Livewire) and displayText.
     */
    private async setDatePickerValue(input: Locator, dateStr: string) {
        // Retry + read-back, same defensive pattern as fillWhenReady: under load, a sibling
        // live field's Livewire round-trip (e.g. date_from resetting date_to) can still be
        // in flight when this fires, or the x-data scope can be mid-teardown/rebuild, so a
        // single setState() call is not guaranteed to stick even though it reports "OK".
        for (let attempt = 0; attempt < 3; attempt++) {
            const result = await input.evaluate((inputEl: HTMLElement, val: string) => {
                const w = window as unknown as { Alpine?: any; dayjs?: any };
                if (!w.Alpine || !w.dayjs) {
                    return "NO_ALPINE_OR_DAYJS";
                }

                let el: HTMLElement | null = inputEl;
                let hops = 0;
                while (el && hops < 15) {
                    if (el.hasAttribute?.("x-data")) {
                        try {
                            const data = w.Alpine.$data(el);
                            if (data && typeof data.setState === "function") {
                                data.setState(w.dayjs(val));
                                return "OK";
                            }
                        } catch {
                            // keep walking up - this x-data scope isn't the date picker's
                        }
                    }
                    el = el.parentElement;
                    hops++;
                }
                return "NOT_FOUND";
            }, dateStr);

            if (result !== "OK") {
                // Fall back to a plain fill in case some other date field really is editable.
                await input.fill(dateStr).catch(() => undefined);
            }

            await this.page.waitForTimeout(400);
            const displayed = await input.inputValue().catch(() => "");
            if (displayed.trim() !== "") {
                return;
            }
        }
    }

    /**
     * Let a live field's Livewire round-trip land before the next interaction,
     * so a submit that follows is not clicked while the form is still disabled.
     */
    private async settleForm() {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.page.waitForTimeout(1000);
    }

    /**
     * Submit the open create form, retrying a click Filament silently swallows
     * while a Livewire request is already in flight (the submit button is
     * disabled for that window; no request is sent and the page stays put).
     */
    private async submitCreateForm() {
        for (let attempt = 0; attempt < 3; attempt++) {
            await this.erpLocators.timeOffCreateSubmitButton.click().catch(() => undefined);
            await this.page
                .waitForURL((url) => !/create/.test(url.toString()), { timeout: 20000 })
                .catch(() => undefined);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);

            if (!/create/.test(this.page.url())) {
                return;
            }
        }

        await expect(this.page).not.toHaveURL(/create/);
    }

    private async expectNotificationContains(pattern: RegExp) {
        const body = this.erpLocators.timeOffNotificationBody.filter({ hasText: pattern });
        const title = this.erpLocators.timeOffNotificationTitle.filter({ hasText: pattern });
        await expect(body.or(title).first()).toBeVisible({ timeout: 10000 });
    }

    private escapeRegExp(value: string): string {
        return value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    }

    private async expectSuccessToast() {
        await expect(this.erpLocators.timeOffSuccessToast).toBeVisible();
    }

    private async expectSuccessToastSoft() {
        try {
            await expect(this.erpLocators.timeOffSuccessToast).toBeVisible({ timeout: 2_500 });
        } catch {
            // Ignore — a redirect can tear the toast down before it is observed.
        }
    }

    async expectValidationErrors() {
        await expect(this.erpLocators.timeOffValidationMessage.first()).toBeVisible();
    }
}
