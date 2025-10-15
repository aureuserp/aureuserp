export async function loginAsAdmin(page) {
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
    await page.goto("admin/login");
    await page.fill('input[type="email"]', adminCredentials.email);
    await page.fill('input[type="password"]', adminCredentials.password);
    await page.press('input[type="password"]', "Enter");

    /**
     * Wait for the dashboard to load.
     */
    console.log("dashboard loading...")
    await page.waitForNavigation();
    return adminCredentials;
}