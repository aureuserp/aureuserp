import fs from "node:fs";
import os from "node:os";
import path from "node:path";

/**
 * Provisioning that mutates global application state — installing a plugin, switching a
 * settings toggle on — is identical work for every describe that needs it, yet the suite
 * repeats it in each of its ~33 beforeAll hooks, costing minutes per run. Remember what this
 * worker has already provisioned and skip it the second time.
 *
 * A helper that *undoes* one of these (uninstalling the plugins, turning dropshipping off)
 * must call `invalidateSetup` with its key, or the next describe would trust a stale entry
 * and run against a setting that is no longer on. Tests whose subject IS the provisioning
 * (`01_inventorySettings.spec.ts`) pass `force` to bypass the cache.
 *
 * This cache is per worker *process* — with 4 workers, up to 4 processes can each decide
 * "I haven't provisioned this yet" for the same key and race to submit the same shared
 * settings form concurrently, one clobbering the other's write. `withSettingsLock` below
 * serializes the underlying mutation across processes via a lock directory (`fs.mkdirSync`
 * is atomic — it fails with EEXIST if another process holds it), so each worker still runs
 * its own provisioning once, but never at the same time as another worker's.
 */
const completed = new Set<string>();

const LOCK_ROOT = path.join(os.tmpdir(), "pw-e2e-setup-locks");
const LOCK_POLL_MS = 200;
const LOCK_TIMEOUT_MS = 5 * 60 * 1000;

function lockPathFor(key: string): string {
    return path.join(LOCK_ROOT, `${key.replace(/[^a-z0-9_-]/gi, "_")}.lock`);
}

/**
 * Run `fn` while holding a cross-process lock keyed by `key`, so two workers never mutate
 * the same global settings form at the same time.
 */
export async function withSettingsLock<T>(key: string, fn: () => Promise<T>): Promise<T> {
    fs.mkdirSync(LOCK_ROOT, { recursive: true });

    const lockPath = lockPathFor(key);
    const deadline = Date.now() + LOCK_TIMEOUT_MS;

    while (true) {
        try {
            fs.mkdirSync(lockPath);
            break;
        } catch (error: any) {
            if (error?.code !== "EEXIST") {
                throw error;
            }

            if (Date.now() > deadline) {
                throw new Error(`Timed out waiting for setup lock: ${key}`);
            }

            await new Promise((resolve) => setTimeout(resolve, LOCK_POLL_MS));
        }
    }

    try {
        return await fn();
    } finally {
        fs.rmSync(lockPath, { recursive: true, force: true });
    }
}

/**
 * CI installs every plugin via `php artisan X:install` before Playwright even starts (see
 * playwright_tests.yml), so the per-spec `ensureXPluginInstalled()` calls are guaranteed
 * no-ops there — yet each still pays for a real page navigation and UI check on its first hit
 * per worker. The workflow sets PLUGINS_PREINSTALLED=true so those calls can skip entirely;
 * local runs without that CLI step leave it unset and keep doing the real check.
 */
export function pluginsPreinstalled(): boolean {
    return process.env.PLUGINS_PREINSTALLED === "true";
}

export const SETUP_KEYS = {
    pluginSales: "plugin:sales",
    pluginPurchases: "plugin:purchases",
    pluginInventories: "plugin:inventories",
    pluginWebsite: "plugin:website",
    pluginBlogs: "plugin:blogs",
    settingsWarehouses: "settings:warehouses",
    settingsProducts: "settings:products",
    settingsOperations: "settings:operations",
    settingsTraceability: "settings:traceability",
    settingsLogistics: "settings:logistics",
    settingsPurchaseAgreements: "settings:purchaseAgreements",
} as const;

export const PLUGIN_SETUP_KEYS = [
    SETUP_KEYS.pluginSales,
    SETUP_KEYS.pluginPurchases,
    SETUP_KEYS.pluginInventories,
    SETUP_KEYS.pluginWebsite,
    SETUP_KEYS.pluginBlogs,
];

export async function runOnce(key: string, provision: () => Promise<void>, force = false): Promise<void> {
    if (!force && completed.has(key)) {
        return;
    }

    await withSettingsLock(key, provision);
    completed.add(key);
}

export function invalidateSetup(...keys: string[]): void {
    for (const key of keys) {
        completed.delete(key);
    }
}
