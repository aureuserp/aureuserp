import { test as baseTest, expect, Page } from "@playwright/test";
import { loginAsAdmin } from "./utils/admin";


type AdminFixtures = {
    adminPage: Page;
};

// export const test = baseTest.extend<AdminFixtures>({
//     adminPage: async ({ page }, use) => {
//         await loginAsAdmin(page);
//         await use(page);
//     },
// });

export const test = baseTest.extend<AdminFixtures>({
    adminPage: async ({ page }, use) => {
        await loginAsAdmin(page);
        await use(page);
    },
});

export { expect }


