import {
    test as base,
    expect,
    type Browser,
    type BrowserContext,
    type Page,
} from "@playwright/test";
import fs from "fs";
import { ADMIN_AUTH_STATE_PATH, STATE_DIR_PATH } from "./playwright.config";
import { loginAsAdmin } from "./utils/admin";

type Fixtures = {
    adminPage: Page;
};

type AdminSession = {
    context: BrowserContext;
    page: Page;
};

function ensureAdminAuthStateDirectoryExists(): void {
    fs.mkdirSync(STATE_DIR_PATH, { recursive: true });
}

async function createAdminSession(browser: Browser): Promise<AdminSession> {
    const authExists = fs.existsSync(ADMIN_AUTH_STATE_PATH);
    const context = await browser.newContext(
        authExists ? { storageState: ADMIN_AUTH_STATE_PATH } : {}
    );
    const page = await context.newPage();

    if (!authExists) {
        await loginAsAdmin(page);
        ensureAdminAuthStateDirectoryExists();
        await context.storageState({ path: ADMIN_AUTH_STATE_PATH });

        return { context, page };
    }

    await page.goto("/admin", { waitUntil: "networkidle" }).catch(() => undefined);
    await page.waitForLoadState("networkidle").catch(() => undefined);

    if (page.url().includes("admin/login")) {
        await loginAsAdmin(page);
        ensureAdminAuthStateDirectoryExists();
        await context.storageState({ path: ADMIN_AUTH_STATE_PATH });
    }

    return { context, page };
}

export async function withAdminPage(
    browser: Browser,
    callback: (page: Page) => Promise<void>
): Promise<void> {
    const { context, page } = await createAdminSession(browser);

    try {
        await callback(page);
    } finally {
        await context.close();
    }
}

export const test = base.extend<Fixtures>({
    adminPage: async ({ browser }, use) => {
        await withAdminPage(browser, use);
    },
});

export { expect };
