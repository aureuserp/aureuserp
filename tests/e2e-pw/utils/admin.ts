export async function loginAsAdmin(page:any) {
    /**
     * Admin credentials.
     */
    const adminCredentials = {
        email: "admin@example.com",
        password: "admin123",
    };

    /**
     * Authenticate the admin user.
     */
    await page.goto("/admin/login");
    await page.fill('input[type="email"]', adminCredentials.email);
    await page.fill('input[type="password"]', adminCredentials.password);
    await page.press('input[type="password"]', "Enter");
    await page.waitForNavigation();
    return adminCredentials;
}
