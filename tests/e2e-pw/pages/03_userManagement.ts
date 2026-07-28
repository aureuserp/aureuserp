import { Page, expect } from "@playwright/test";
import fs from "fs";
import { ErpLocators } from "../locator/erp_locator";
import { ADMIN_AUTH_STATE_PATH } from "../playwright.config";

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

        await this.erpLocators.usersSaveButton.click();
        await this.expectSuccessFeedback();
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
        await this.erpLocators.usersSaveButton.click();
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
        await this.erpLocators.usersSaveButton.click();
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
        await this.erpLocators.usersSaveButton.click();
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
        await this.erpLocators.usersSaveButton.click();
        await expect(this.erpLocators.usersErrorToast.or(this.erpLocators.usersValidationMessage.first())).toBeVisible();
    }

    /**
     * Search users in listing table
     */
    async searchUser(keyword: string) {
        await this.erpLocators.usersSearchInput.fill(keyword);
        await this.page.waitForLoadState("networkidle");
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
        await this.erpLocators.usersRowActionsButton.first().click();
        await this.erpLocators.usersEditButton.click();
        await this.erpLocators.usersNameInput.fill(newName);
        await this.erpLocators.usersSaveButton.click();
        await this.expectSuccessFeedback();
    }

    /**
     * Reset password from user action
     */
    async resetUserPassword(searchKey: string, newPassword: string) {
        await this.searchUser(searchKey);
        await this.erpLocators.usersRowActionsButton.click();
        await this.erpLocators.usersEditButton.click();
        await this.erpLocators.usersResetPasswordButton.click();
        await this.erpLocators.usersChangePasswordInput.fill(newPassword);
        await this.erpLocators.usersChangePasswordConfirmationInput.fill(newPassword);
        await this.erpLocators.usersChangePasswordSaveButton.click();
        await this.expectSuccessFeedback();
    }

    /**
     * Delete user from listing
     */
    async deleteUser(searchKey: string) {
        await this.searchUser(searchKey);
        await this.erpLocators.usersRowActionsButton.first().click();
        await this.erpLocators.usersDeleteButton.click();
        await this.erpLocators.usersConfirmDeleteButton.click();
        await this.expectSuccessFeedback();
    }

    /**
     * Bulk delete users from listing
     */
    async bulkDeleteUsers(searchKey: string) {
        await this.searchUser(searchKey);
        await this.erpLocators.selectAllUsersButton.click();
        await this.erpLocators.usersBulkActionsButton.click();
        await this.erpLocators.usersForceDeleteButton.click();
        await this.erpLocators.usersConfirmDeleteButton.click();
        await this.expectSuccessFeedback();
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
        if (await roleSelect.count()) {
            if (await roleSelect.first().evaluate((el) => el.tagName.toLowerCase() === "select")) {
                await roleSelect.selectOption({ label: role });
                return;
            }
            await roleSelect.click();
            await this.page.getByRole("option", { name: role }).first().click();
        }
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
            await companySelect.click();
            const companySearchInput = this.erpLocators.usersCompanySearchInput.last();
            if (await companySearchInput.isVisible()) {
                await companySearchInput.fill(company);
            }
            const option = this.page.getByRole("option", { name: company }).first();
            if (allowMissing) {
                if (await option.isVisible()) {
                    await option.click();
                }
                return;
            }
            await option.waitFor({ state: "visible" });
            await option.click();
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
}
