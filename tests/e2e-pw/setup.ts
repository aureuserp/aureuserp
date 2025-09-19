import { test as base, expect, type Page } from "@playwright/test";
import fs from "fs";
import { ADMIN_AUTH_STATE_PATH } from "./playwright.config";
import { loginAsAdmin } from "./utils/admin";

type AdminPage = Page

type AdminFixtures = {
    adminPage: AdminPage;
};

export const test = base.extend<AdminFixtures>({
    adminPage: async ({ browser }, use) => {
        const authExists = fs.existsSync(ADMIN_AUTH_STATE_PATH);

        const context = await browser.newContext(
            authExists ? { storageState: ADMIN_AUTH_STATE_PATH } : {}
        );

        const page = await context.newPage();

        if (!authExists) {
            await loginAsAdmin(page);
            await context.storageState({ path: ADMIN_AUTH_STATE_PATH });
        } else {
            await page.goto("/admin");
            if (page.url().includes("admin/login")) {
                await loginAsAdmin(page);
                await context.storageState({ path: ADMIN_AUTH_STATE_PATH });
            }
        }

        await use(page as AdminPage);
        await context.close();
    },
});

export { expect };