import { test , expect } from "../setup";
import { CompanyManagementPage } from "../pages/03_companyManagement";
import { UserManagementPage, type UserData } from "../pages/04_userManagement";

test.describe("Users Module E2E", () => {
    const defaultRole = "Admin";

    test.beforeEach(async ({ adminPage }) => {
        const userPage = new UserManagementPage(adminPage);
        await userPage.gotoUsersPage();
    });

    test("Access Users Listing - Loads Table", async ({ adminPage }) => {
        const userPage = new UserManagementPage(adminPage);
        await userPage.gotoUsersPage();
    });

    test("Create User - Valid Inputs", async ({ adminPage }) => {
        const companyPage = new CompanyManagementPage(adminPage);
        const userPage = new UserManagementPage(adminPage);
        const key = Date.now();
        const companyName = `E2E User Company ${key}`;

        // Precondition: Users requires an existing company.
        await companyPage.gotoCompaniesPage();
        await companyPage.createCompany({ name: companyName, email: `users-company+${key}@example.com` });

        const userData: UserData = {
            name: `E2E User ${key}`,
            email: `e2e.user+${key}@example.com`,
            password: "Test@12345",
            role: defaultRole,
            company: companyName,
        };

        await userPage.gotoUsersPage();
        const initialCount = await userPage.refreshUserCount();
        await userPage.createUser(userData);
        await userPage.gotoUsersPage();
        const updatedCount = await userPage.refreshUserCount();
        expect(updatedCount).toBe(initialCount + 1);
    });

    test("Create User - Duplicate Email Validation", async ({ adminPage }) => {
        const companyPage = new CompanyManagementPage(adminPage);
        const userPage = new UserManagementPage(adminPage);
        const key = Date.now();
        const companyName = `E2E Duplicate User Company ${key}`;

        await companyPage.gotoCompaniesPage();
        await companyPage.createCompany({ name: companyName, email: `dup-company+${key}@example.com` });

        await userPage.gotoUsersPage();
        await userPage.createUserWithDuplicateEmail({
            name: `E2E Duplicate User ${key}`,
            email: `duplicate.user+${key}@example.com`,
            password: "Test@12345",
            role: defaultRole,
            company: companyName,
        });
    });

    test("Create User - Role Required Validation", async ({ adminPage }) => {
        const companyPage = new CompanyManagementPage(adminPage);
        const userPage = new UserManagementPage(adminPage);
        const key = Date.now();
        const companyName = `E2E Role Required Company ${key}`;

        await companyPage.gotoCompaniesPage();
        await companyPage.createCompany({ name: companyName, email: `role-company+${key}@example.com` });

        await userPage.gotoUsersPage();
        await userPage.createUserWithoutRole(
            `E2E Role Required User ${key}`,
            `role.required+${key}@example.com`,
            "Test@12345",
            companyName
        );
    });

    test("Create User - Company Required Validation", async ({ adminPage }) => {
        const userPage = new UserManagementPage(adminPage);
        const key = Date.now();

        await userPage.createUserWithoutCompany(
            `E2E Company Required User ${key}`,
            `company.required+${key}@example.com`,
            "Test@12345",
            defaultRole
        );
    });

    test("Create User - Inactive/Invalid Company Validation", async ({ adminPage }) => {
        const userPage = new UserManagementPage(adminPage);
        const key = Date.now();

        await userPage.createUserWithInvalidCompany(
            `E2E Invalid Company User ${key}`,
            `invalid.company+${key}@example.com`,
            "Test@12345",
            defaultRole,
            `Missing Company ${key}`
        );
    });

    test("Edit User - Updates Name", async ({ adminPage }) => {
        const companyPage = new CompanyManagementPage(adminPage);
        const userPage = new UserManagementPage(adminPage);
        const key = Date.now();
        const companyName = `E2E Edit User Company ${key}`;
        const originalName = `E2E Editable User ${key}`;
        const updatedName = `E2E Edited User ${key}`;
        const email = `edit.user+${key}@example.com`;

        await companyPage.gotoCompaniesPage();
        await companyPage.createCompany({ name: companyName, email: `edit-user-company+${key}@example.com` });

        await userPage.gotoUsersPage();
        await userPage.createUser({
            name: originalName,
            email,
            password: "Test@12345",
            role: defaultRole,
            company: companyName,
        });

        await userPage.gotoUsersPage();
        await userPage.editUserName(email, updatedName);
        await userPage.gotoUsersPage();
        await userPage.assertUserVisible(updatedName);
    });

    // test("Inactive User - Cannot Login To Admin Panel", async ({ adminPage }) => {
    //     const companyPage = new CompanyManagementPage(adminPage);
    //     const userPage = new UserManagementPage(adminPage);
    //     const key = Date.now();
    //     const companyName = `E2E Inactive User Company ${key}`;
    //     const email = `inactive.user+${key}@example.com`;
    //     const password = "Test@12345";

    //     await companyPage.gotoCompaniesPage();
    //     await companyPage.createCompany({ name: companyName, email: `inactive-user-company+${key}@example.com` });

    //     await userPage.gotoUsersPage();
    //     await userPage.createUser({
    //         name: `E2E Inactive User ${key}`,
    //         email,
    //         password,
    //         role: defaultRole,
    //         company: companyName,
    //         Status: "Inactive",
    //     });

    //     await userPage.logout();
    //     await userPage.attemptLogin(email, password);
    //     await expect(userPage.page).toHaveURL(/.*\/admin\/login/);
    // });

    test("Reset User Password Configuration - Inabled/Disabled setting,", async ({ adminPage }) => {
        const companyPage = new CompanyManagementPage(adminPage);
        const userPage = new UserManagementPage(adminPage);
        const key = Date.now();
        const companyName = `E2E Reset Config Company ${key}`;
        const email = `reset.config.user+${key}@example.com`;

        await companyPage.gotoCompaniesPage();
        await companyPage.createCompany({ name: companyName, email: `reset-config-company+${key}@example.com` });

        await userPage.gotoUsersPage();
        await userPage.createUser({
            name: `E2E Reset Config User ${key}`,
            email,
            password: "Test@12345",
            role: defaultRole,
            company: companyName,
        });

        await userPage.gotoManageUsersSettingsPage();
        await userPage.setEnableResetConfiguration(false);

        try {
            await userPage.gotoUsersPage();
            await userPage.assertResetPasswordActionDisabled(email);
        } finally {
            await userPage.gotoManageUsersSettingsPage();
            await userPage.setEnableResetConfiguration(true);
        }
    });

    test("Reset User Password - Success", async ({ adminPage }) => {
        const companyPage = new CompanyManagementPage(adminPage);
        const userPage = new UserManagementPage(adminPage);
        const key = Date.now();
        const companyName = `E2E Reset User Company ${key}`;
        const email = `reset.user+${key}@example.com`;

        await companyPage.gotoCompaniesPage();
        await companyPage.createCompany({ name: companyName, email: `reset-user-company+${key}@example.com` });

        await userPage.gotoUsersPage();
        await userPage.createUser({
            name: `E2E Reset User ${key}`,
            email,
            password: "Test@12345",
            role: defaultRole,
            company: companyName,
        });

        await userPage.gotoUsersPage();
        await userPage.resetUserPassword(email, "NewTest@12345");
    });

    test("Delete User - Removes Record From Listing", async ({ adminPage }) => {
        const companyPage = new CompanyManagementPage(adminPage);
        const userPage = new UserManagementPage(adminPage);
        const key = Date.now();
        const companyName = `E2E Delete User Company ${key}`;
        const email = `delete.user+${key}@example.com`;

        await companyPage.gotoCompaniesPage();
        await companyPage.createCompany({ name: companyName, email: `delete-user-company+${key}@example.com` });

        await userPage.gotoUsersPage();
        await userPage.createUser({
            name: `E2E Delete User ${key}`,
            email,
            password: "Test@12345",
            role: defaultRole,
            company: companyName,
        });

        await userPage.gotoUsersPage();
        await userPage.deleteUser(email);
    });

    test("Bulk Delete Users - Removes Records", async ({ adminPage }) => {
        const companyPage = new CompanyManagementPage(adminPage);
        const userPage = new UserManagementPage(adminPage);
        const key = Date.now();
        const companyName = `E2E Bulk Delete User Company ${key}`;
        const bulkKey = `bulk.delete.${key}`;
        const users = [
            {
                name: `E2E Bulk Delete User 1 ${key}`,
                email: `${bulkKey}.user1@example.com`,
            },
            {
                name: `E2E Bulk Delete User 2 ${key}`,
                email: `${bulkKey}.user2@example.com`,
            },
        ];

        await companyPage.gotoCompaniesPage();
        await companyPage.createCompany({ name: companyName, email: `bulk-delete-users-company+${key}@example.com` });

        await userPage.gotoUsersPage();
        const initialCount = await userPage.refreshUserCount();

        for (const user of users) {
            await userPage.gotoUsersPage();
            await userPage.createUser({
                name: user.name,
                email: user.email,
                password: "Test@12345",
                role: defaultRole,
                company: companyName,
            });
        }

        await userPage.gotoUsersPage();
        await userPage.bulkDeleteUsers(bulkKey);
        await userPage.gotoUsersPage();
        const finalCount = await userPage.refreshUserCount();
        expect(finalCount).toBe(initialCount);
    });

    test("User Invitation - Inabled/Disabled setting", async ({ adminPage }) => {
        const userPage = new UserManagementPage(adminPage);

        await userPage.gotoManageUsersSettingsPage();
        await userPage.setEnableUserInvitationConfiguration(false);

        try {
            await userPage.gotoUsersPage();
            await userPage.assertUserInvitationHidden();

            await userPage.gotoManageUsersSettingsPage();
            await userPage.setEnableUserInvitationConfiguration(true);
            await userPage.gotoUsersPage();
            await userPage.assertUserInvitationVisible();
        } finally {
            await userPage.gotoManageUsersSettingsPage();
            await userPage.setEnableUserInvitationConfiguration(true);
        }
    });
});
