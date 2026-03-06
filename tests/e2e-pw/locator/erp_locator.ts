import { Locator, Page } from "@playwright/test";

export class ErpLocators {

    readonly page: Page;

    /**
     *  Plugin Install/Uninstall  
     */

    readonly pluginSyncButton: Locator;
    readonly pluginthreeDot: Locator;
    readonly pluginName : Locator;
    readonly pluginInstallButton: Locator;
    readonly pluginUninstallButton: Locator
    readonly pluginConfirmButton : Locator;
    readonly pluginSearchInput : Locator;
    readonly pluginSuccessMessage : Locator;
    readonly pluginErrorMessage : Locator;

    /**
     * Companies
     */

    readonly allCompaniesCount: Locator;
    readonly companiesMenuLink: Locator;
    readonly companiesTable: Locator;
    readonly companiesCreateButton: Locator;
    readonly companiesNameInput: Locator;
    readonly companiesEmailInput: Locator;
    readonly companiesPhoneInput: Locator;
    readonly companiesStatusToggleOn: Locator;
    readonly companiesStatusToggleOff: Locator;
    readonly companiesSaveButton: Locator;
    readonly companiesSearchInput: Locator;
    readonly companiesRowActionsButton: Locator;
    readonly companiesEditButton: Locator;
    readonly companiesDeleteButton: Locator;
    readonly selectAllCompaniesButton: Locator;
    readonly bulkActionsButton: Locator;
    readonly forceDeleteButton: Locator;
    readonly companiesConfirmDeleteButton: Locator;
    readonly companiesStatusToggle: Locator;
    readonly companiesSuccessToast: Locator;
    readonly companiesErrorToast: Locator;
    readonly companiesFeildValidationMessage: Locator;
    readonly companiesValidationMessage: Locator;

    /**
     * Users
     */

    readonly usersMenuLink: Locator;
    readonly allUsersCount: Locator;
    readonly usersTable: Locator;
    readonly usersCreateButton: Locator;
    readonly usersInviteButton: Locator;
    readonly usersNameInput: Locator;
    readonly usersEmailInput: Locator;
    readonly usersPasswordInput: Locator;
    readonly usersPasswordConfirmationInput: Locator;
    readonly usersRoleSelect: Locator;
    readonly usersCompanySelect: Locator;
    readonly usersCompanySearchInput: Locator;
    readonly usersRoleOption: Locator;
    readonly usersCompanyOption: Locator;
    readonly usersSaveButton: Locator;
    readonly usersSearchInput: Locator;
    readonly usersRowActionsButton: Locator;
    readonly usersEditButton: Locator;
    readonly usersDeleteButton: Locator;
    readonly usersConfirmDeleteButton: Locator;
    readonly selectAllUsersButton: Locator;
    readonly usersBulkActionsButton: Locator;
    readonly usersForceDeleteButton: Locator;
    readonly usersStatusToggle: Locator;
    readonly usersCreateStatusToggle: Locator;
    readonly usersResetPasswordButton: Locator;
    readonly usersChangePasswordInput: Locator;
    readonly usersChangePasswordConfirmationInput: Locator;
    readonly usersChangePasswordSaveButton: Locator;
    readonly userMenuButton: Locator;
    readonly logoutButton: Locator;
    readonly usersSuccessToast: Locator;
    readonly usersErrorToast: Locator;
    readonly userFeildValidationMessage: Locator;
    readonly usersValidationMessage: Locator;
    readonly manageUsersEnableResetCard: Locator;
    readonly manageUsersEnableResetToggle: Locator;
    readonly manageUsersEnableInvitationToggle: Locator;
    readonly settingsSaveButton: Locator;

    constructor(page: Page) {
        this.page = page;

        /**
         *  Plugin Install/Uninstall  
         */

        this.pluginSyncButton = page.locator('text=Sync Available Plugins');
        this.pluginthreeDot = page.locator('button[title="Actions"]');
        this.pluginName = page.locator('.fi-size-lg.fi-font-semibold.fi-ta-text-item.fi-ta-text.fi-inline');
        this.pluginInstallButton = page.locator('button.fi-color.fi-color-success.fi-text-color-700');
        this.pluginUninstallButton = page.locator('button.fi-color.fi-color-danger.fi-dropdown-list-item');
        this.pluginConfirmButton = page.locator('span[x-show="! isProcessing"]');
        this.pluginSearchInput = page.locator('.fi-input.fi-input-has-inline-prefix').nth(1);
        this.pluginSuccessMessage = page.locator('h3.fi-no-notification-title');
        this.pluginErrorMessage = page.locator('.fi-toast-message-error');

        /**
         * Companies
         */

        this.allCompaniesCount = page.locator('span.fi-badge-label-ctn').nth(0);
        this.companiesMenuLink = page.getByRole("link", { name: /companies/i });
        this.companiesTable = page.locator("table");
        this.companiesCreateButton = page.locator("a,button").filter({ hasText: /new company|create company|add company|create/i }).first();
        this.companiesNameInput = page.locator('input[id="form.name"]').first();
        this.companiesEmailInput = page.locator('input[id="form.email"]').first();
        this.companiesPhoneInput = page.locator('input[id="form.phone"]').first();
        this.companiesStatusToggleOn = page.locator('button[aria-checked="true"]');
        this.companiesStatusToggleOff = page.locator('button[aria-checked="false"]');
        this.companiesSaveButton = page.locator('button[type="submit"]').nth(1);
        this.companiesSearchInput = page.locator('.fi-input.fi-input-has-inline-prefix').nth(1);
        this.companiesRowActionsButton = page.locator('div.fi-ta-text-item').nth(0);
        this.companiesEditButton = page.locator("a.fi-ac-btn-action");
        this.companiesDeleteButton = page.locator("button.fi-ac-btn-action");
        this.selectAllCompaniesButton = page.locator('input[aria-label="Select/deselect all items for bulk actions."]');
        this.bulkActionsButton = page.locator('button.fi-ac-btn-group').nth(1);
        this.forceDeleteButton = page.locator('span.fi-dropdown-list-item-label').nth(4);
        this.companiesConfirmDeleteButton = page.locator("button[x-data='filamentFormButton']");
        this.companiesStatusToggle = page.locator('button[role="switch"], input[type="checkbox"]').first();
        this.companiesSuccessToast = page.locator("h3.fi-no-notification-title, .fi-toast-message-success").first();
        this.companiesErrorToast = page.locator(".fi-toast-message-error, .fi-input-wrp-error").first();
        this.companiesFeildValidationMessage = page.locator(".fi-fo-field-wrp-error-message", { hasText: /Company name already exists. Please use a unique name./ });
        this.companiesValidationMessage = page.locator(".fi-fo-field-wrp-error-message, .text-danger, .invalid-feedback");

        /**
         * Users
         */

        this.usersMenuLink = page.getByRole("link", { name: /users/i });
        this.allUsersCount = page.locator('span.fi-badge-label-ctn').nth(0);
        this.usersTable = page.locator("table");
        this.usersCreateButton = page.locator("a,button").filter({ hasText: /new user|create user|add user|create/i }).first();
        this.usersInviteButton = page.locator("a,button").filter({ hasText: /invite user|user invitation|invite/i }).first();
        this.usersNameInput = page.locator('input[id="form.name"]');
        this.usersEmailInput = page.locator('input[id="form.email"]');
        this.usersPasswordInput = page.locator('input[id="form.password"]');
        this.usersPasswordConfirmationInput = page.locator('input[id="form.password_confirmation"]');
        this.usersRoleSelect = page.locator('div.fi-select-input-value-ctn').nth(0);
        this.usersCompanySelect = page.locator('div.fi-select-input-value-ctn').nth(5);
        this.usersCompanySearchInput = page.locator('input[type="search"], input[placeholder*="Search"], input[placeholder*="search"]');
        this.usersRoleOption = page.locator('[role="option"], .fi-select-option, li').filter({ hasText: /./ });
        this.usersCompanyOption = page.locator('[role="option"], .fi-select-option, li').filter({ hasText: /./ });
        this.usersSaveButton = page.locator('button[x-data="filamentFormButton"]');
        this.usersSearchInput = page.locator('.fi-input.fi-input-has-inline-prefix').nth(1);
        this.usersRowActionsButton = page.locator('div.fi-ta-text-item').nth(0);
        this.usersEditButton = page.locator("a.fi-ac-btn-action").nth(0);
        this.usersDeleteButton = page.locator("button.fi-ac-btn-action");
        this.usersConfirmDeleteButton = page.getByRole('dialog').getByRole('button', { name: 'Delete' });
        this.selectAllUsersButton = page.locator('input[aria-label="Select/deselect all items for bulk actions."]');
        this.usersBulkActionsButton = page.locator('button.fi-ac-btn-group').nth(1);
        this.usersForceDeleteButton = page.locator('span.fi-dropdown-list-item-label').nth(3);
        this.usersStatusToggle = page.locator('button[role="switch"], input[type="checkbox"]').first();
        this.usersCreateStatusToggle = page.locator('button.fi-fo-toggle');
        this.usersResetPasswordButton = page.locator("button,a").filter({ hasText: /Change Password/i }).first();
        this.usersChangePasswordInput = page.getByRole('textbox', { name: 'New Password*' });
        this.usersChangePasswordConfirmationInput = page.getByRole('textbox', { name: 'Confirm New Password' });
        this.usersChangePasswordSaveButton = page.getByRole('button', { name: 'Submit' });
        this.userMenuButton = page.locator('button[aria-label="User menu"]');
        this.logoutButton = page.getByRole('textbox', { name: 'Confirm New Password' });
        this.usersSuccessToast = page.locator("h3.fi-no-notification-title, .fi-toast-message-success").first();
        this.usersErrorToast = page.locator(".fi-toast-message-error, .fi-input-wrp-error").first();
        this.userFeildValidationMessage = page.locator(".fi-fo-field-wrp-error-message", { hasText: /The email has already been taken./ });
        this.usersValidationMessage = page.locator(".fi-fo-field-wrp-error-message, .text-danger, .invalid-feedback");
        this.manageUsersEnableResetCard = page
            .locator("div,section,li,fieldset")
            .filter({ hasText: /Enable Reset Password|Allow users to reset their password/i })
            .first();
        this.manageUsersEnableResetToggle = page.getByRole("switch", { name: /Enable Reset Password/i });
        this.manageUsersEnableInvitationToggle = page.getByRole("switch", { name: /Enable User Invitation/i });
        this.settingsSaveButton = page.getByRole("button", { name: /Save changes|save|update|submit/i }).first();
    }
}
