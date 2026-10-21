<script setup>
import { inject, computed } from 'vue'

const till = inject('till')

const state = till.state

const order = computed(() => till.activeOrder)

const hasSelection = computed(() => Object.values(state.splitQuantities).some((quantity) => quantity > 0))

function productName(productId) {
    return till.master.products.get(productId)?.name ?? ''
}

function moving(line) {
    return state.splitQuantities[line.uuid] ?? 0
}
</script>

<template>
    <div class="flex min-h-0 flex-col gap-3">
        <div class="flex flex-none items-center gap-3">
            <button
                type="button"
                class="flex size-11 flex-none items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
                :aria-label="till.t('common.back')"
                @click="till.closeSplit()"
            >
                <svg class="size-5 rtl:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M11.78 5.22a.75.75 0 0 1 0 1.06L8.06 10l3.72 3.72a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                </svg>
            </button>

            <h2 class="flex-1 text-lg font-semibold text-gray-950 dark:text-white">{{ till.t('split.heading') }}</h2>
        </div>

        <div class="grid min-h-0 flex-auto grid-rows-[minmax(0,1fr)_auto] gap-3 lg:grid-cols-2 lg:grid-rows-[minmax(0,1fr)]">
            <div class="min-h-0 overflow-y-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                <p class="border-b border-gray-100 px-4 py-2 text-xs text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    {{ till.t('split.hint') }}
                </p>

                <button
                    v-for="line in order?.lines ?? []"
                    :key="line.uuid"
                    type="button"
                    class="flex w-full items-center gap-3 border-b border-gray-100 px-4 py-3 text-start transition-colors dark:border-gray-800"
                    :class="moving(line) > 0
                        ? 'bg-primary-50 dark:bg-primary-500/15'
                        : 'hover:bg-gray-50 dark:hover:bg-gray-800'"
                    @click="till.tapSplitLine(line)"
                >
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-medium text-gray-950 dark:text-white">{{ productName(line.product_id) }}</span>

                        <span class="block font-mono text-xs tabular-nums text-gray-500 dark:text-gray-400">
                            <template v-if="moving(line) > 0">{{ moving(line) }} / {{ line.qty }}</template>
                            <template v-else>{{ line.qty }}</template>
                        </span>
                    </span>

                    <span class="flex-none font-mono text-sm font-semibold tabular-nums text-gray-950 dark:text-white">
                        {{ till.money(till.displayLineTotal(line)) }}
                    </span>
                </button>
            </div>

            <div class="flex flex-col gap-3">
                <div class="rounded-xl border border-gray-200 bg-white py-6 text-center dark:border-gray-700 dark:bg-gray-900">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ till.t('split.new-bill') }}</p>

                    <p class="font-mono text-3xl font-bold tabular-nums text-success-600 dark:text-success-400">
                        {{ till.money(till.splitTotal) }}
                    </p>
                </div>

                <button
                    type="button"
                    class="flex min-h-14 items-center justify-center rounded-lg bg-primary-600 text-base font-semibold text-white transition-colors hover:bg-primary-700 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:bg-gray-800"
                    :disabled="!hasSelection"
                    @click="till.confirmSplit()"
                >
                    {{ till.t('split.confirm') }}
                </button>
            </div>
        </div>
    </div>
</template>
