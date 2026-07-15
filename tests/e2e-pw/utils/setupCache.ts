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
 */
const completed = new Set<string>();

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

    await provision();
    completed.add(key);
}

export function invalidateSetup(...keys: string[]): void {
    for (const key of keys) {
        completed.delete(key);
    }
}
