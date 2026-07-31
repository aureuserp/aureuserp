import { type Locator, Page, expect } from "@playwright/test";
import fs from "fs";
import { ErpLocators } from "../locator/erp_locator";
import { ADMIN_AUTH_STATE_PATH } from "../playwright.config";
import { clickRowAction, filterListBySearch, rowByText } from "../utils/list";

export type UserData = {
    name: string;
    email: string;
    password: string;
    role: string;
    company: string;
    Status?: "Active" | "Inactive";
};

export class UserManagementPage {
    /**
     * Page and shared locators
     */
    readonly page: Page;
    readonly erpLocators: ErpLocators;
    userCount: number = 0;

    constructor(page: Page) {
        this.page = page;
        this.erpLocators = new ErpLocators(page);
    }

    /**
     * Navigate to users listing
     */
    async gotoUsersPage() {
        await this.page.goto("/admin/users");
        await expect(this.page).toHaveURL(/.*users/);
        await expect(this.erpLocators.usersTable.first()).toBeVisible();

        await this.refreshUserCount();
    }

    /**
     * Read and cache user count from UI
     */
    async refreshUserCount(): Promise<number> {
        const countText = await this.erpLocators.allUsersCount.textContent();
        this.userCount = countText ? parseInt(countText.trim()) : 0;

        return this.userCount;
    }

    /**
     * Open create user form
     */
    async openCreateUserForm() {
        await this.erpLocators.usersCreateButton.click();
        await expect(this.page).toHaveURL(/.*(users|create)/);
    }

    /**
     * Create user with all required fields
     */
    async createUser(userData: UserData) {
        await this.openCreateUserForm();

        await this.erpLocators.usersNameInput.fill(userData.name);
        await this.erpLocators.usersEmailInput.fill(userData.email);
        await this.erpLocators.usersPasswordInput.fill(userData.password);
        await this.erpLocators.usersPasswordConfirmationInput.fill(userData.password);

        await this.selectRole(userData.role);
        await this.selectCompany(userData.company);
        await this.setCreateFormStatus(userData.Status);

        await this.ensureSelected(this.erpLocators.usersRoleSelect, userData.role, () => this.selectRole(userData.role));
        await this.ensureSelected(this.erpLocators.usersCompanySelect, userData.company, () => this.selectCompany(userData.company));

        await this.refillIfEmptied(this.erpLocators.usersNameInput, userData.name);
        await this.refillIfEmptied(this.erpLocators.usersEmailInput, userData.email);
        await this.refillIfEmptied(this.erpLocators.usersPasswordInput, userData.password);
        await this.refillIfEmptied(this.erpLocators.usersPasswordConfirmationInput, userData.password);

        await this.submitUserForm();

        // Saving redirects off the create form and the redirect takes the toast with it. A
        // toast is not proof anyway: with several workers, the one on screen may be another
        // test's.
        await expect(this.page).not.toHaveURL(/users\/create/);
    }


    /**
     * Submit the user form once the field it recomputed has settled. 
     */
    private async ensureSelected(select: Locator, value: string, pick: () => Promise<void>) {
        for (let attempt = 0; attempt < 2; attempt++) {
            const shown = await select.first().innerText().catch(() => "");

            if (shown.includes(value)) {
                return;
            }

            await pick();
        }

        await expect(select.first()).toContainText(value);
    }

    /**
     * Type a value back into a field that a re-render has emptied.
     */
    private async refillIfEmptied(input: Locator, value: string) {
        for (let attempt = 0; attempt < 3; attempt++) {
            if ((await input.inputValue().catch(() => "")) === value) {
                return;
            }

            await input.fill(value);
            await this.page.waitForTimeout(300);
        }

        await expect(input).toHaveValue(value);
    }

    private async submitUserForm(expectLeavingCreate = true) {
        const button = this.erpLocators.usersSaveButton;

        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await button.waitFor({ state: "visible", timeout: 15000 });
        await expect(button).toBeEnabled({ timeout: 60000 });

        if (!expectLeavingCreate) {
            const refused = this.erpLocators.usersValidationMessage.first();

            for (let attempt = 0; attempt < 3; attempt++) {
                await button.click({ force: attempt > 0 }).catch(() => undefined);
                await this.page.waitForLoadState("networkidle").catch(() => undefined);

                const settled = await Promise.race([
                    refused.waitFor({ state: "visible", timeout: 30000 }).then(() => true).catch(() => false),
                    this.page
                        .waitForURL((url) => !/users\/create/.test(url.toString()), { timeout: 30000 })
                        .then(() => true)
                        .catch(() => false),
                ]);

                if (settled) {
                    return;
                }

                await expect(button).toBeEnabled({ timeout: 60000 });
            }

            return;
        }

        for (let attempt = 0; attempt < 2; attempt++) {
            await button.click({ force: attempt > 0 }).catch(() => undefined);

            const left = await this.page
                .waitForURL((url) => !/users\/create/.test(url.toString()), { timeout: 150000 })
                .then(() => true)
                .catch(() => false);

            if (left) {
                await this.page.waitForLoadState("networkidle").catch(() => undefined);

                return;
            }

            if (await this.erpLocators.usersValidationMessage.first().isVisible().catch(() => false)) {
                return;
            }

            await expect(button).toBeEnabled({ timeout: 60000 });
        }

        await expect(this.page).not.toHaveURL(/users\/create/);
    }


    /**
     * Type the plain fields back in if a re-render has emptied them. 
     */
    private async ensurePlainFields(name: string, email: string, password: string) {
        await this.refillIfEmptied(this.erpLocators.usersNameInput, name);
        await this.refillIfEmptied(this.erpLocators.usersEmailInput, email);
        await this.refillIfEmptied(this.erpLocators.usersPasswordInput, password);
        await this.refillIfEmptied(this.erpLocators.usersPasswordConfirmationInput, password);
    }

    /**
     * Validate duplicate email handling
     */
    async createUserWithDuplicateEmail(userData: UserData) {
        await this.createUser(userData);
        await this.gotoUsersPage();
        await this.openCreateUserForm();
        await this.erpLocators.usersNameInput.fill(userData.name);
        await this.erpLocators.usersEmailInput.fill(userData.email);
        await this.erpLocators.usersPasswordInput.fill(userData.password);
        await this.erpLocators.usersPasswordConfirmationInput.fill(userData.password);
        await this.selectRole(userData.role);
        await this.selectCompany(userData.company);
        await this.ensurePlainFields(userData.name, userData.email, userData.password);
        await this.submitUserForm(false);

        await expect(this.page).toHaveURL(/users\/create/);
        await expect(this.erpLocators.userFeildValidationMessage.or(this.erpLocators.usersValidationMessage.first())).toBeVisible();
    }

    /**
     * Validate role required by omitting role
     */
    async createUserWithoutRole(name: string, email: string, password: string, company: string) {
        await this.openCreateUserForm();
        await this.erpLocators.usersNameInput.fill(name);
        await this.erpLocators.usersEmailInput.fill(email);
        await this.erpLocators.usersPasswordInput.fill(password);
        await this.erpLocators.usersPasswordConfirmationInput.fill(password);
        await this.selectCompany(company);
        await this.ensurePlainFields(name, email, password);
        await this.submitUserForm(false);

        await expect(this.page).toHaveURL(/users\/create/);
        await expect(this.erpLocators.usersValidationMessage.first()).toBeVisible();
    }

    /**
     * Validate company required by omitting company
     */
    async createUserWithoutCompany(name: string, email: string, password: string, role: string) {
        await this.openCreateUserForm();
        await this.erpLocators.usersNameInput.fill(name);
        await this.erpLocators.usersEmailInput.fill(email);
        await this.erpLocators.usersPasswordInput.fill(password);
        await this.erpLocators.usersPasswordConfirmationInput.fill(password);
        await this.selectRole(role);
        await this.ensurePlainFields(name, email, password);
        await this.submitUserForm(false);

        await expect(this.page).toHaveURL(/users\/create/);
        await expect(this.erpLocators.usersValidationMessage.first()).toBeVisible();
    }

    /**
     * Validate invalid company selection
     */
    async createUserWithInvalidCompany(name: string, email: string, password: string, role: string, company: string) {
        await this.openCreateUserForm();
        await this.erpLocators.usersNameInput.fill(name);
        await this.erpLocators.usersEmailInput.fill(email);
        await this.erpLocators.usersPasswordInput.fill(password);
        await this.erpLocators.usersPasswordConfirmationInput.fill(password);
        await this.selectRole(role);
        await this.selectCompany(company, true);
        await this.ensurePlainFields(name, email, password);
        await this.submitUserForm(false);

        await expect(this.erpLocators.usersErrorToast.or(this.erpLocators.usersValidationMessage.first())).toBeVisible();
    }

    /**
     * Search users in listing table
     */
    async searchUser(keyword: string) {
        await filterListBySearch(this.page, this.erpLocators.usersSearchInput, keyword);
    }

    /**
     * Assert user row is visible
     */
    async assertUserVisible(identifier: string) {
        await this.searchUser(identifier);
        await expect(this.page.getByText(identifier).first()).toBeVisible();
    }

    /**
     * Edit user name by opening first matched row action
     */
    async editUserName(searchKey: string, newName: string) {
        await this.searchUser(searchKey);

        await clickRowAction(rowByText(this.page, searchKey), "Edit");
        await this.erpLocators.usersNameInput.fill(newName);

        await this.submitEditForm();
        await this.page.waitForTimeout(1500);
    }

    /**
     * Reset password from user action
     */
    async resetUserPassword(searchKey: string, newPassword: string) {
        await this.searchUser(searchKey);
        await clickRowAction(rowByText(this.page, searchKey), "Edit");
        await this.erpLocators.usersResetPasswordButton.click();

        const save = this.erpLocators.usersChangePasswordSaveButton;
        await expect(this.erpLocators.usersChangePasswordInput).toBeVisible({ timeout: 15000 });

        for (let attempt = 0; attempt < 3; attempt++) {
            await this.erpLocators.usersChangePasswordInput.fill(newPassword);
            await this.erpLocators.usersChangePasswordConfirmationInput.fill(newPassword);

            await expect(save).toBeEnabled({ timeout: 30000 });
            await save.click().catch(() => undefined);

            const closed = await save
                .waitFor({ state: "hidden", timeout: 60000 })
                .then(() => true)
                .catch(() => false);

            if (closed) {
                await this.page.waitForLoadState("networkidle").catch(() => undefined);

                return;
            }
        }

        await expect(save).toBeHidden();
    }

    /**
     * Delete user from listing
     */
    async deleteUser(searchKey: string) {
        await this.searchUser(searchKey);
        await clickRowAction(rowByText(this.page, searchKey), "Delete");
        await this.erpLocators.usersConfirmDeleteButton.click();

        await this.expectUserAbsent(searchKey);
    }

    async expectUserListed(name: string) {
        await this.searchUser(name);
        await expect(rowByText(this.page, name)).toBeVisible();
    }

    async expectUserAbsent(searchKey: string) {
        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await this.gotoUsersPage();
        await this.searchUser(searchKey);
        await expect(rowByText(this.page, searchKey)).toHaveCount(0);
    }

    /**
     * Bulk delete users from listing
     */
    async bulkDeleteUsers(searchKey: string, userNames: string[] = []) {
        await this.searchUser(searchKey);

        if (userNames.length > 0) {
            for (const name of userNames) {
                await rowByText(this.page, name).locator('input[type="checkbox"]').first().check();
            }
        } else {
            await this.erpLocators.selectAllUsersButton.click();
        }

        await this.erpLocators.usersBulkActionsButton.click();
        await this.erpLocators.usersForceDeleteButton.click();
        await this.erpLocators.usersConfirmDeleteButton.click();

        for (const name of userNames) {
            await this.expectUserAbsent(name);
        }
    }

    /**
     * Navigate to Manage Users settings page
     */
    async gotoManageUsersSettingsPage() {
        await this.page.goto("/admin/settings/manage-users");
        await expect(this.page).toHaveURL(/.*\/admin\/settings\/manage-users/);
        await expect(this.erpLocators.manageUsersEnableResetCard).toBeVisible();
    }

    /**
     * Enable or disable password reset config for users
     */
    async setEnableResetConfiguration(enabled: boolean) {
        const toggle = this.erpLocators.manageUsersEnableResetToggle;
        await expect(toggle).toBeVisible();

        const tag = await toggle.evaluate((el) => el.tagName.toLowerCase());
        const isEnabled = tag === "input"
            ? await toggle.isChecked()
            : (await toggle.getAttribute("aria-checked")) !== "false";

        if (isEnabled !== enabled) {
            await toggle.click();
        }

        if (await this.erpLocators.settingsSaveButton.isVisible()) {
            await this.erpLocators.settingsSaveButton.click();
            await this.page.waitForLoadState("networkidle");
        }
    }

    /**
     * Enable or disable user invitation config
     */
    async setEnableUserInvitationConfiguration(enabled: boolean) {
        const toggle = this.erpLocators.manageUsersEnableInvitationToggle;
        await expect(toggle).toBeVisible();

        const tag = await toggle.evaluate((el) => el.tagName.toLowerCase());
        const isEnabled = tag === "input"
            ? await toggle.isChecked()
            : (await toggle.getAttribute("aria-checked")) !== "false";

        if (isEnabled !== enabled) {
            await toggle.click();
        }

        if (await this.erpLocators.settingsSaveButton.isVisible()) {
            await this.erpLocators.settingsSaveButton.click();
            await this.page.waitForLoadState("networkidle");
        }
    }

    /**
     * Assert reset password action is not available in row actions
     */
    async assertResetPasswordActionDisabled(searchKey: string) {
        await this.searchUser(searchKey);
        await this.erpLocators.usersRowActionsButton.first().click();
        await this.erpLocators.usersEditButton.click();
        await expect(this.erpLocators.usersResetPasswordButton).not.toBeVisible();
        const resetAction = this.page.locator("button,a").filter({ hasText: /Change Password|Reset Password/i });
        if (await resetAction.count()) {
            await expect(resetAction.first()).not.toBeVisible();
            return;
        }

        await expect(resetAction).toHaveCount(0);
    }

    /**
     * Assert user invitation action is available on users page
     */
    async assertUserInvitationVisible() {
        await expect(this.erpLocators.usersInviteButton).toBeVisible();
    }

    /**
     * Assert user invitation action is hidden on users page
     */
    async assertUserInvitationHidden() {
        if (await this.erpLocators.usersInviteButton.count()) {
            await expect(this.erpLocators.usersInviteButton.first()).not.toBeVisible();
            return;
        }

        await expect(this.erpLocators.usersInviteButton).toHaveCount(0);
    }

    /**
     * Logout user by opening user menu and clicking logout
     */
    async logout() {
        await this.page.waitForLoadState("networkidle");
        await this.erpLocators.userMenuButton.click();
        await this.erpLocators.logoutButton.click();
        await expect(this.page).toHaveURL(/.*\/admin\/login/);

        if (fs.existsSync(ADMIN_AUTH_STATE_PATH)) {
            fs.unlinkSync(ADMIN_AUTH_STATE_PATH);
            console.log("[logout] Deleted stale auth state file.");
        }
    }

    /**
     * Attempt login with given credentials (used for negative testing of inactive users)
     */
    async attemptLogin(email: string, password: string) {
        await this.page.goto("/admin/login");
        await this.page.fill('input[type="email"]', email);
        await this.page.fill('input[type="password"]', password);
        await this.page.press('input[type="password"]', "Enter");
        await this.page.waitForLoadState("networkidle");
    }

    /**
     * Role selection helper (supports native select and custom dropdown)
     */
    private async selectRole(role: string) {
        const roleSelect = this.erpLocators.usersRoleSelect;

        if (!(await roleSelect.count())) {
            return;
        }

        if (await roleSelect.first().evaluate((el) => el.tagName.toLowerCase() === "select")) {
            await roleSelect.selectOption({ label: role });
            return;
        }

        for (let attempt = 0; attempt < 3; attempt++) {
            await roleSelect.click();

            const option = this.page.getByRole("option", { name: role }).first();
            const appeared = await option
                .waitFor({ state: "visible", timeout: 10000 })
                .then(() => true)
                .catch(() => false);

            if (!appeared) {
                continue;
            }

            await option.click();
            await this.page.waitForLoadState("networkidle").catch(() => undefined);

            if (await roleSelect.first().locator(`button:has-text("${role}")`).count()) {
                return;
            }

            if ((await roleSelect.first().innerText()).includes(role)) {
                return;
            }
        }

        await expect(roleSelect.first()).toContainText(role);
    }

    /**
     * Company selection helper (supports native select and custom dropdown)
     */
    private async selectCompany(company: string, allowMissing = false) {
        const companySelect = this.erpLocators.usersCompanySelect;
        if (await companySelect.count()) {
            if (await companySelect.first().evaluate((el) => el.tagName.toLowerCase() === "select")) {
                await companySelect.selectOption({ label: company });
                return;
            }
            for (let attempt = 0; attempt < 3; attempt++) {
                await companySelect.click();
                const companySearchInput = this.erpLocators.usersCompanySearchInput.last();
                await companySearchInput.waitFor({ state: "visible", timeout: 10000 }).catch(() => undefined);

                if (await companySearchInput.isVisible().catch(() => false)) {
                    await companySearchInput.fill(company);
                    await this.page.waitForLoadState("networkidle").catch(() => undefined);
                    await this.page.waitForTimeout(800);
                }

                const option = this.page.getByRole("option", { name: company }).first();
                const appeared = await option
                    .waitFor({ state: "visible", timeout: 10000 })
                    .then(() => true)
                    .catch(() => false);

                if (!appeared) {
                    if (allowMissing) {
                        await this.page.keyboard.press("Escape");
                        return;
                    }

                    continue;
                }

                await option.click();
                await this.page.waitForLoadState("networkidle").catch(() => undefined);

                if ((await companySelect.first().innerText()).includes(company)) {
                    return;
                }
            }

            if (!allowMissing) {
                await expect(companySelect.first()).toContainText(company);
            }
        }
    }

    /**
     * Create-form status toggle helper only
     */
    private async setCreateFormStatus(status?: "Active" | "Inactive") {
        if (!status) {
            return;
        }

        const statusToggle = this.erpLocators.usersCreateStatusToggle;
        if (!await statusToggle.count()) {
            return;
        }

        const toggleState = await statusToggle.first().getAttribute("aria-checked");
        const isActive = toggleState !== "false";
        const shouldBeActive = status === "Active";

        if (isActive !== shouldBeActive) {
            await statusToggle.first().click();
        }
    }

    /**
     * Reusable assertion for success toast/notification
     */
    private async expectSuccessFeedback() {
        await this.page.waitForLoadState("networkidle");
        await expect(this.erpLocators.usersSuccessToast).toBeVisible();
    }

    /**
     * Save an edit-page form, retrying the click if a Livewire hydration race swallows it.
     * submitUserForm's "settled" check leans on leaving `/users/create` — on an edit page that
     * URL condition is already true before the click even lands, so it can't tell a real save
     * from a swallowed one here. The success toast is the only reliable signal on this page.
     */
    private async submitEditForm(): Promise<void> {
        const button = this.erpLocators.usersSaveButton;

        await this.page.waitForLoadState("networkidle").catch(() => undefined);
        await button.waitFor({ state: "visible", timeout: 15000 });
        await expect(button).toBeEnabled({ timeout: 60000 });

        for (let attempt = 0; attempt < 3; attempt++) {
            await button.click({ force: attempt > 0 }).catch(() => undefined);
            await this.page.waitForLoadState("networkidle").catch(() => undefined);

            const saved = await this.erpLocators.usersSuccessToast
                .waitFor({ state: "visible", timeout: 15000 })
                .then(() => true)
                .catch(() => false);

            if (saved) {
                return;
            }

            await expect(button).toBeEnabled({ timeout: 60000 });
        }

        await this.expectSuccessFeedback();
    }
}
