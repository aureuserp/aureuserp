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

    /**
     * Arm the wait before submitting: waitForNavigation() only sees a navigation that
     * starts after it is called, so a login that redirects straight away leaves it waiting
     * for a second navigation that never comes, and the run hangs on the login page.
     */
    await Promise.all([
        page.waitForURL((url: URL) => !url.toString().includes("/admin/login"), { timeout: 60000 }),
        page.press('input[type="password"]', "Enter"),
    ]);
    await page.waitForLoadState("networkidle").catch(() => undefined);

    return adminCredentials;
}
