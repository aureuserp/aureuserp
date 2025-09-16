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
    console.log("logging in as admin...");
    await page.goto("/admin/login");
    await page.fill('input[type="email"]', adminCredentials.email);
    await page.fill('input[type="password"]', adminCredentials.password);

    console.log("email and password filled...");

    // Try to submit via the form submit button first, fallback to pressing Enter
    const submitClicked = await page.locator('button[type="submit"]').first().catch(() => null);
    if (submitClicked) {
        await submitClicked.click();
    } else {
        await page.press('input[type="password"]', 'Enter');
    }

    // Wait for one of several signals that login succeeded (avoid long hangs):
    // - navigation
    // - dashboard-specific selector
    // - URL change
    // Use short timeouts so the test fails fast instead of getting stuck.
    const timeout = 8000;

    const navPromise = page.waitForNavigation({ waitUntil: 'networkidle', timeout }).catch(() => null);
    const selectorPromise = page.waitForSelector('text=Dashboard', { timeout }).catch(() => null);
    const urlPromise = page.waitForURL(/admin|dashboard/, { timeout }).catch(() => null);

    await Promise.race([navPromise, selectorPromise, urlPromise]);

    console.log("dashboard loading...");

    return adminCredentials;
}