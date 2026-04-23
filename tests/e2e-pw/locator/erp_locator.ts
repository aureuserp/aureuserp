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

    /**
     * Sales - Customers, Products, Quotations
     */

    readonly salesCustomersTable: Locator;
    readonly salesCustomerCreateButton: Locator;
    readonly salesCustomerNewCreateButton: Locator;
    readonly salesCustomerNameInput: Locator;
    readonly salesCustomerEmailInput: Locator;
    readonly salesCustomerSaveButton: Locator;
    readonly salesCustomerSearchInput: Locator;
    readonly salesCustomerEditButton: Locator;
    readonly salesCustomerDeleteButton: Locator;


    readonly salesProductsTable: Locator;
    readonly salesProductNewCreateButton: Locator;
    readonly salesProductNameInput: Locator;
    readonly salesProductCategorySelect: Locator;
    readonly salesProductPriceInput: Locator;
    readonly salesProductUomSelect: Locator;
    readonly salesProductSaveButton: Locator;
    readonly salesProductCreateButton: Locator;
    readonly salesProductEditButton: Locator;
    readonly salesProductDeleteButton: Locator;

    readonly salesQuotationCreateButton: Locator;
    readonly salesQuotationEditButton: Locator;
    readonly salesQuotationCustomerSelect: Locator;
    readonly salesQuotationPaymentTermSelect: Locator;
    readonly salesQuotationAddProductButton: Locator;
    readonly salesQuotationProductSelectInput: Locator;
    readonly salesQuotationQuantityInput: Locator;
    readonly salesQuotationSaveButton: Locator;
    readonly salesQuotationDeleteButton: Locator;
    readonly salesQuotationConfirmButton: Locator;
    readonly salesQuotationSendButton: Locator;
    readonly salesQuotationSendSubmitButton: Locator;
    readonly salesQuotationSentRadio: Locator;
    readonly salesQuotationCreateInvoiceButton: Locator;
    readonly salesQuotationInvoiceSubmitButton: Locator;
    readonly salesQuotationDeliveriesTable: Locator;
    readonly salesQuotationDeliveryEditButton: Locator;
    readonly salesDeliveryValidateButton: Locator;
    readonly salesDeliveryNoBackorderButton: Locator;
    readonly salesInvoicesTable: Locator;

    readonly salesSearchInput: Locator;
    readonly salesRowActionsButton: Locator;
    readonly salesEditAction: Locator;
    readonly salesDeleteAction: Locator;
    readonly salesConfirmDeleteButton: Locator;

    readonly salesSelectSearchInput: Locator;
    readonly salesSelectOption: Locator;
    readonly salesSuccessToast: Locator;
    readonly salesValidationMessage: Locator;

    /**
     * Employees - Employees, Departments
     */

    readonly employeesMenuLink: Locator;
    readonly employeesTable: Locator;
    readonly employeeCreateButton: Locator;
    readonly employeeNameInput: Locator;
    readonly employeeJobTitleInput: Locator;
    readonly employeeWorkEmailInput: Locator;
    readonly employeeWorkPhoneInput: Locator;
    readonly employeeMobilePhoneInput: Locator;
    readonly employeeDepartmentSelect: Locator;
    readonly employeeJobPositionSelect: Locator;
    readonly employeeManagerSelect: Locator;
    readonly employeeCoachSelect: Locator;
    readonly employeeSaveButton: Locator;
    readonly employeeEditSaveButton: Locator;
    readonly employeeSearchInput: Locator;
    readonly employeeViewButton: Locator;
    readonly employeeEditButton: Locator;
    readonly employeeDeleteButton: Locator;
    readonly employeeRowActionsButton: Locator;
    readonly employeeConfirmDeleteButton: Locator;
    readonly selectAllEmployeesButton: Locator;
    readonly employeeBulkActionsButton: Locator;
    readonly employeeBulkDeleteButton: Locator;
    readonly employeeSuccessToast: Locator;
    readonly employeeErrorToast: Locator;
    readonly employeeValidationMessage: Locator;

    readonly departmentsTable: Locator;
    readonly departmentCreateButton: Locator;
    readonly departmentNameInput: Locator;
    readonly departmentManagerSelect: Locator;
    readonly departmentParentSelect: Locator;
    readonly departmentCompanySelect: Locator;
    readonly departmentSaveButton: Locator;
    readonly departmentEditSaveButton: Locator;
    readonly departmentSearchInput: Locator;
    readonly departmentRowActionsButton: Locator;
    readonly departmentViewButton: Locator;
    readonly departmentEditButton: Locator;
    readonly departmentDeleteButton: Locator;
    readonly departmentConfirmDeleteButton: Locator;
    readonly selectAllDepartmentsButton: Locator;
    readonly departmentBulkActionsButton: Locator;
    readonly departmentBulkDeleteButton: Locator;

    readonly employeeSelectSearchInput: Locator;
    readonly employeeSelectOption: Locator;

    /**
     * Employees - Configurations (Employment Types, Employee Categories,
     * Work Locations, Departure Reasons, Skill Types, Job Positions, Activity Plans)
     */

    readonly configurationTable: Locator;
    readonly configurationCreateButton: Locator;
    readonly configurationNameInput: Locator;
    readonly configurationCodeInput: Locator;
    readonly configurationLocationNumberInput: Locator;
    readonly configurationCountrySelect: Locator;
    readonly configurationCompanySelect: Locator;
    readonly configurationDepartmentSelect: Locator;
    readonly configurationEmploymentTypeSelect: Locator;
    readonly configurationColorSelect: Locator;
    readonly configurationLocationTypeOption: Locator;
    readonly configurationModalCreateButton: Locator;
    readonly configurationModalSaveButton: Locator;
    readonly configurationCreatePageSaveButton: Locator;
    readonly configurationEditPageSaveButton: Locator;
    readonly configurationSearchInput: Locator;
    readonly configurationViewButton: Locator;
    readonly configurationEditButton: Locator;
    readonly configurationDeleteButton: Locator;
    readonly configurationRowActionsButton: Locator;
    readonly configurationConfirmDeleteButton: Locator;
    readonly configurationSuccessToast: Locator;
    readonly configurationErrorToast: Locator;
    readonly configurationValidationMessage: Locator;

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

        /**
         * Sales - Customers, Products, Quotations
         */

        this.salesCustomersTable = page.locator("div.fi-ta-content-grid, div.fi-ta-empty-state, table");
        this.salesCustomerNewCreateButton = page.locator("a,button").filter({ hasText: /new customer|create customer|add customer|create/i }).first();
        this.salesCustomerNameInput = page.locator('input[id="form.name"]').first();
        this.salesCustomerEmailInput = page.locator('input[id="form.email"]').first();
        this.salesCustomerCreateButton = page.locator('button[id="key-bindings-1"]').first();
        this.salesCustomerSaveButton = page.locator('button[id="key-bindings-2"]').first();
        this.salesCustomerDeleteButton = page.locator('button[id="key-bindings-1"]').first();
        this.salesCustomerSearchInput = page.locator('.fi-input.fi-input-has-inline-prefix').nth(1);
        this.salesCustomerEditButton = page.getByRole('link', { name: 'Edit' }).first();

        this.salesProductsTable = page.locator("table, div.fi-ta-empty-state");
        this.salesProductNewCreateButton = page.locator("a,button").filter({ hasText: /new product|create product|add product|create/i }).first();
        this.salesProductNameInput = page.locator('input[id="form.name"]').first();
        this.salesProductCategorySelect = page.locator('input[id="form.category_id"], [role="combobox"][aria-label*="Category"], [role="combobox"][aria-labelledby*="Category"]').first();
        this.salesProductPriceInput = page.locator('input[id="form.price"]').first();
        this.salesProductUomSelect = page.locator('input[id="form.uom_id"], [role="combobox"][aria-label*="UOM"], [role="combobox"][aria-labelledby*="UOM"]').first();
        this.salesProductCreateButton = page.locator('button[id="key-bindings-1"]').first();
        this.salesProductEditButton = page.getByRole('link', { name: 'Edit' });
        this.salesProductSaveButton = page.locator('button[id="key-bindings-2"]').first();
        this.salesProductDeleteButton = page.getByRole('button', { name: 'Delete' });

        this.salesQuotationCreateButton = page.locator("a,button").filter({ hasText: /new quotation|create quotation|add quotation|create/i }).first();
        this.salesQuotationEditButton = page.getByRole('link', { name: 'Edit' }).first();
        this.salesQuotationCustomerSelect = page.locator('[wire\\:key$="form.partner_id"] button.fi-select-input-btn').first();
        this.salesQuotationPaymentTermSelect = page.locator('[wire\\:key$="form.payment_term_id"] button.fi-select-input-btn').first();
        this.salesQuotationAddProductButton = page.getByRole("button", { name: /Add Product/i }).first();
        this.salesQuotationProductSelectInput = page.locator('[wire\\:key*=".form.products."][wire\\:key*=".product_id."] button.fi-select-input-btn');
        this.salesQuotationQuantityInput = page.locator('input[id^="form.products."][id$=".product_qty"]');
        this.salesQuotationDeleteButton = page.getByRole('button', { name: 'Delete' }).first();
        this.salesQuotationSaveButton = page.getByRole('button', { name: /^(Create|Save changes|Submit)$/i }).first();
        this.salesQuotationConfirmButton = page.getByRole("button", { name: /Confirm/i }).first();
        this.salesQuotationSendButton = page.getByRole("button", { name: /Send by Email|Send/i }).first();
        this.salesQuotationSendSubmitButton = page.getByRole("dialog").getByRole("button", { name: /Send|Submit/i }).first(); 
        this.salesQuotationSentRadio = page.getByRole("radio", { name: /Quotation Sent/i });
        this.salesQuotationCreateInvoiceButton = page.getByRole("button", { name: /Create Invoice/i }).first();
        this.salesQuotationInvoiceSubmitButton = page.getByRole("dialog").getByRole("button", { name: /Create Invoice/i }).first();
        this.salesQuotationDeliveriesTable = page.locator("table, div.fi-ta-empty-state");
        this.salesQuotationDeliveryEditButton = page.getByRole('table').getByRole('link', { name: 'Edit' });
        this.salesDeliveryValidateButton = page.getByRole("button", { name: /Validate/i }).first();
        this.salesDeliveryNoBackorderButton = page.getByRole("button", { name: /No Backorder/i }).first();
        this.salesInvoicesTable = page.locator("table, div.fi-ta-empty-state");

        this.salesSearchInput = page.locator(".fi-input.fi-input-has-inline-prefix").nth(1);
        this.salesRowActionsButton = page.getByRole('button', { name: 'Actions' });
        this.salesEditAction = page.getByRole("menuitem", { name: /Edit/i }).first();
        this.salesDeleteAction = page.getByRole("menuitem", { name: /Delete/i }).first();
        this.salesConfirmDeleteButton = page.getByRole("dialog").getByRole("button", { name: /Delete/i }).first();

        this.salesSelectSearchInput = page.locator('.fi-dropdown-panel[role="listbox"]:visible input.fi-input[aria-label="Search"]').last();
        this.salesSelectOption = page.locator('.fi-dropdown-panel[role="listbox"]:visible [role="option"]');
        this.salesSuccessToast = page.locator("h3.fi-no-notification-title, .fi-toast-message-success").first();
        this.salesValidationMessage = page.locator(".fi-fo-field-wrp-error-message, .text-danger, .invalid-feedback");

        /**
         * Employees - Employees, Departments
         */

        this.employeesMenuLink = page.getByRole("link", { name: /employees/i });
        this.employeesTable = page.locator("div.fi-ta-content-grid, div.fi-ta-empty-state, table");
        this.employeeCreateButton = page.locator("a,button").filter({ hasText: /new employee|create employee|add employee|create/i }).first();
        this.employeeNameInput = page.locator('input[id="form.name"]').first();
        this.employeeJobTitleInput = page.locator('input[id="form.job_title"]').first();
        this.employeeWorkEmailInput = page.locator('input[id="form.work_email"]').first();
        this.employeeWorkPhoneInput = page.locator('input[id="form.work_phone"]').first();
        this.employeeMobilePhoneInput = page.locator('input[id="form.mobile_phone"]').first();
        this.employeeDepartmentSelect = page.locator('[wire\\:key$="form.department_id"] button.fi-select-input-btn, [wire\\:key$=".form.department_id"] button.fi-select-input-btn').first();
        this.employeeJobPositionSelect = page.locator('[wire\\:key$="form.job_id"] button.fi-select-input-btn, [wire\\:key$=".form.job_id"] button.fi-select-input-btn').first();
        this.employeeManagerSelect = page.locator('[wire\\:key$="form.parent_id"] button.fi-select-input-btn, [wire\\:key$=".form.parent_id"] button.fi-select-input-btn').first();
        this.employeeCoachSelect = page.locator('[wire\\:key$="form.coach_id"] button.fi-select-input-btn, [wire\\:key$=".form.coach_id"] button.fi-select-input-btn').first();
        this.employeeSaveButton = page.getByRole("button", { name: /^(Create|Save changes|Submit)$/i }).first();
        this.employeeEditSaveButton = page.getByRole("button", { name: /^(Save changes|Save|Submit)$/i }).first();
        this.employeeSearchInput = page.locator('.fi-input.fi-input-has-inline-prefix').nth(1);
        this.employeeViewButton = page.getByRole("link", { name: /^View$/i }).first();
        this.employeeEditButton = page.getByRole("link", { name: /^Edit$/i }).first();
        this.employeeDeleteButton = page.getByRole("button", { name: /^Delete$/i }).first();
        this.employeeRowActionsButton = page.getByRole("button", { name: /Actions/i }).first();
        this.employeeConfirmDeleteButton = page.getByRole("dialog").getByRole("button", { name: /Delete/i }).first();
        this.selectAllEmployeesButton = page.locator('input[aria-label="Select/deselect all items for bulk actions."]');
        this.employeeBulkActionsButton = page.locator('button.fi-ac-btn-group').nth(1);
        this.employeeBulkDeleteButton = page.getByRole("menuitem", { name: /^Delete$/i }).first();

        this.employeeSuccessToast = page.locator("h3.fi-no-notification-title, .fi-toast-message-success").first();
        this.employeeErrorToast = page.locator(".fi-toast-message-error, .fi-input-wrp-error").first();
        this.employeeValidationMessage = page.locator(".fi-fo-field-wrp-error-message, .text-danger, .invalid-feedback");

        this.departmentsTable = page.locator("div.fi-ta-content-grid, div.fi-ta-empty-state, table");
        this.departmentCreateButton = page.locator("a,button").filter({ hasText: /new department|create department|add department|create/i }).first();
        this.departmentNameInput = page.locator('input[id="form.name"]').first();
        this.departmentManagerSelect = page.locator('[wire\\:key$="form.manager_id"] button.fi-select-input-btn, [wire\\:key$=".form.manager_id"] button.fi-select-input-btn').first();
        this.departmentParentSelect = page.locator('[wire\\:key$="form.parent_id"] button.fi-select-input-btn, [wire\\:key$=".form.parent_id"] button.fi-select-input-btn').first();
        this.departmentCompanySelect = page.locator('[wire\\:key$="form.company_id"] button.fi-select-input-btn, [wire\\:key$=".form.company_id"] button.fi-select-input-btn').first();
        this.departmentSaveButton = page.getByRole("button", { name: /^(Create|Save changes|Submit)$/i }).first();
        this.departmentEditSaveButton = page.getByRole("button", { name: /^(Save changes|Save|Submit)$/i }).first();
        this.departmentSearchInput = page.locator('.fi-input.fi-input-has-inline-prefix').nth(1);
        this.departmentRowActionsButton = page.getByRole("button", { name: /Actions/i }).first();
        this.departmentViewButton = page.getByRole("link", { name: /^View$/i }).first();
        this.departmentEditButton = page.getByRole("link", { name: /^Edit$/i }).first();
        this.departmentDeleteButton = page.getByRole("button", { name: /^Delete$/i }).first();
        this.departmentConfirmDeleteButton = page.getByRole("dialog").getByRole("button", { name: /Delete/i }).first();
        this.selectAllDepartmentsButton = page.locator('input[aria-label="Select/deselect all items for bulk actions."]');
        this.departmentBulkActionsButton = page.locator('button.fi-ac-btn-group').nth(1);
        this.departmentBulkDeleteButton = page.getByRole("menuitem", { name: /^Delete$/i }).first();

        this.employeeSelectSearchInput = page.locator('.fi-dropdown-panel[role="listbox"]:visible input.fi-input[aria-label="Search"]').last();
        this.employeeSelectOption = page.locator('.fi-dropdown-panel[role="listbox"]:visible [role="option"]');

        /**
         * Employees - Configurations
         */

        this.configurationTable = page.locator("table, div.fi-ta-empty-state, div.fi-ta-content-grid");
        this.configurationCreateButton = page.locator("a,button").filter({ hasText: /new|create|add/i }).first();
        this.configurationNameInput = page.locator('input[id$="form.name"], input[id$=".name"]').first();
        this.configurationCodeInput = page.locator('input[id$="form.code"], input[id$=".code"]').first();
        this.configurationLocationNumberInput = page.locator('input[id$="form.location_number"], input[id$=".location_number"]').first();
        this.configurationCountrySelect = page.locator('[wire\\:key$="form.country_id"] button.fi-select-input-btn, [wire\\:key$=".form.country_id"] button.fi-select-input-btn').first();
        this.configurationCompanySelect = page.locator('[wire\\:key$="form.company_id"] button.fi-select-input-btn, [wire\\:key$=".form.company_id"] button.fi-select-input-btn').first();
        this.configurationDepartmentSelect = page.locator('[wire\\:key$="form.department_id"] button.fi-select-input-btn, [wire\\:key$=".form.department_id"] button.fi-select-input-btn').first();
        this.configurationEmploymentTypeSelect = page.locator('[wire\\:key$="form.employment_type_id"] button.fi-select-input-btn, [wire\\:key$=".form.employment_type_id"] button.fi-select-input-btn').first();
        this.configurationColorSelect = page.locator('[wire\\:key$="form.color"] button.fi-select-input-btn, [wire\\:key$=".form.color"] button.fi-select-input-btn').first();
        this.configurationLocationTypeOption = page.locator('label.fi-fo-toggle-buttons-option, button[role="radio"]').first();
        this.configurationModalCreateButton = page.getByRole("dialog").getByRole("button", { name: /^Create$/i }).first();
        this.configurationModalSaveButton = page.getByRole("dialog").getByRole("button", { name: /^(Save|Save changes|Submit)$/i }).first();
        this.configurationCreatePageSaveButton = page.getByRole("button", { name: /^(Create|Submit)$/i }).first();
        this.configurationEditPageSaveButton = page.getByRole("button", { name: /^(Save changes|Save|Submit)$/i }).first();
        this.configurationSearchInput = page.locator('.fi-input.fi-input-has-inline-prefix').nth(1);
        this.configurationViewButton = page.getByRole("link", { name: /^View$/i }).first();
        this.configurationEditButton = page.getByRole("link", { name: /^Edit$/i }).first().or(page.getByRole("button", { name: /^Edit$/i }).first());
        this.configurationDeleteButton = page.getByRole("button", { name: /^Delete$/i }).first();
        this.configurationRowActionsButton = page.getByRole("button", { name: /Actions/i }).first();
        this.configurationConfirmDeleteButton = page.getByRole("dialog").getByRole("button", { name: /Delete/i }).first();
        this.configurationSuccessToast = page.locator("h3.fi-no-notification-title, .fi-toast-message-success").first();
        this.configurationErrorToast = page.locator(".fi-toast-message-error, .fi-input-wrp-error").first();
        this.configurationValidationMessage = page.locator(".fi-fo-field-wrp-error-message, .text-danger, .invalid-feedback");
    }
}
