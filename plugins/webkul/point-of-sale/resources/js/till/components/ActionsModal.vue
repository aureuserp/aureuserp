<script setup>
import { inject, ref, watch } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state

const confirmingCancel = ref(false)

watch(() => state.actionsModalOpen, (open) => {
    if (!open) {
        confirmingCancel.value = false
    }
})

const tile = 'flex min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-4 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 active:bg-gray-100 disabled:text-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800 dark:active:bg-gray-700 dark:disabled:text-gray-600'

const danger = 'flex min-h-20 flex-col items-center justify-center gap-2 rounded-lg border border-danger-200 bg-danger-50 px-3 py-4 text-sm font-medium text-danger-700 transition-colors hover:bg-danger-100 active:bg-danger-200 dark:border-danger-500/30 dark:bg-danger-500/10 dark:text-danger-300 dark:hover:bg-danger-500/20 dark:active:bg-danger-500/30'
</script>

<template>
    <TillModal :heading="till.t('actions.heading')" :open="state.actionsModalOpen" width="max-w-2xl" @close="till.closeActions()">

        <div class="grid grid-cols-2 gap-3 p-4 sm:grid-cols-3">
            <button
                v-if="till.canSelectPriceList()"
                type="button"
                :class="tile"
                @click="till.openPriceLists()"
            >
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75Zm0 5A.75.75 0 0 1 2.75 9h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 9.75Zm0 5a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
                </svg>

                <span>{{ till.t('price-lists.label') }}</span>

                <span class="truncate text-xs font-normal text-gray-500 dark:text-gray-400">
                    {{ till.activePriceList?.name ?? till.t('price-lists.default') }}
                </span>
            </button>

            <button
                v-if="till.isRestaurant && till.activeOrder"
                type="button"
                :class="tile"
                @click="till.closeActions(); till.openGuests()"
            >
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M7 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM14.5 9a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5ZM1.615 16.428a1.224 1.224 0 0 1-.569-1.175 6.002 6.002 0 0 1 11.908 0c.058.467-.172.92-.57 1.174A9.953 9.953 0 0 1 7 18a9.953 9.953 0 0 1-5.385-1.572ZM14.5 16h-.106c.07-.297.088-.611.048-.933a7.47 7.47 0 0 0-1.588-3.755 4.502 4.502 0 0 1 5.874 2.636.818.818 0 0 1-.36.98A7.465 7.465 0 0 1 14.5 16Z" />
                </svg>

                <span>{{ till.t('guests.label') }}</span>

                <span class="font-mono text-xs font-normal tabular-nums text-gray-500 dark:text-gray-400">
                    {{ till.activeOrder.customer_count ?? 0 }}
                </span>
            </button>

            <button
                v-if="till.globalDiscountProductId"
                type="button"
                :class="tile"
                :disabled="!till.canApplyGlobalDiscount"
                @click="till.openGlobalDiscount()"
            >
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M4.5 2A2.5 2.5 0 0 0 2 4.5v3.879a2.5 2.5 0 0 0 .732 1.767l7.5 7.5a2.5 2.5 0 0 0 3.536 0l3.878-3.878a2.5 2.5 0 0 0 0-3.536l-7.5-7.5A2.5 2.5 0 0 0 8.38 2H4.5ZM5 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                </svg>

                <span>{{ till.t('global-discount.label') }}</span>
            </button>

            <button
                v-if="till.isRestaurant && till.config.enable_print_bill"
                type="button"
                :class="tile"
                :disabled="!till.activeOrder?.lines.length"
                @click="till.openBill()"
            >
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5 2.75C5 1.784 5.784 1 6.75 1h6.5c.966 0 1.75.784 1.75 1.75v3.552c.377.046.752.097 1.126.153A2.212 2.212 0 0 1 18 8.653v4.097A2.25 2.25 0 0 1 15.75 15h-.241l.305 1.984A1.75 1.75 0 0 1 14.084 19H5.915a1.75 1.75 0 0 1-1.73-2.016L4.492 15H4.25A2.25 2.25 0 0 1 2 12.75V8.653c0-1.082.775-2.034 1.874-2.198.374-.056.75-.107 1.127-.153L5 6.25v-3.5Zm8.5 3.397a41.533 41.533 0 0 0-7 0V2.75a.25.25 0 0 1 .25-.25h6.5a.25.25 0 0 1 .25.25v3.397ZM6.608 12.5a.25.25 0 0 0-.247.212l-.693 4.5a.25.25 0 0 0 .247.288h8.17a.25.25 0 0 0 .246-.288l-.692-4.5a.25.25 0 0 0-.247-.212H6.608Z" clip-rule="evenodd" />
                </svg>

                <span>{{ till.t('bill.label') }}</span>
            </button>

            <button
                v-if="till.isRestaurant && till.config.enable_split_bill"
                type="button"
                :class="tile"
                :disabled="!till.canSplit"
                @click="till.openSplit()"
            >
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M7 3.5A1.5 1.5 0 0 1 8.5 2h3.879a1.5 1.5 0 0 1 1.06.44l3.122 3.12A1.5 1.5 0 0 1 17 6.622V12.5a1.5 1.5 0 0 1-1.5 1.5h-1v-3.379a3 3 0 0 0-.879-2.121L10.5 5.379A3 3 0 0 0 8.379 4.5H7v-1Z" />
                    <path d="M4.5 6A1.5 1.5 0 0 0 3 7.5v9A1.5 1.5 0 0 0 4.5 18h7a1.5 1.5 0 0 0 1.5-1.5v-5.879a1.5 1.5 0 0 0-.44-1.06L9.44 6.439A1.5 1.5 0 0 0 8.378 6H4.5Z" />
                </svg>

                <span>{{ till.t('split.label') }}</span>
            </button>

            <button
                v-if="till.canToggleTakeaway"
                type="button"
                :class="tile"
                @click="till.toggleTakeaway()"
            >
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M6 5v1H4.667a1.75 1.75 0 0 0-1.743 1.598l-.826 9.5A1.75 1.75 0 0 0 3.84 19H16.16a1.75 1.75 0 0 0 1.743-1.902l-.826-9.5A1.75 1.75 0 0 0 15.333 6H14V5a4 4 0 0 0-8 0Zm4-2.5A2.5 2.5 0 0 0 7.5 5v1h5V5A2.5 2.5 0 0 0 10 2.5ZM7.5 10a2.5 2.5 0 0 0 5 0V8.75a.75.75 0 0 1 1.5 0V10a4 4 0 0 1-8 0V8.75a.75.75 0 0 1 1.5 0V10Z" clip-rule="evenodd" />
                </svg>

                <span>{{ till.t(till.activeOrder.is_takeaway ? 'takeaway.to-dine-in' : 'takeaway.to-takeaway') }}</span>
            </button>

            <button
                v-if="till.isRestaurant && till.activeOrder"
                type="button"
                :class="tile"
                @click="till.startTransfer()"
            >
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M13.2 2.24a.75.75 0 0 0 .04 1.06l2.1 1.95H6.75a.75.75 0 0 0 0 1.5h8.59l-2.1 1.95a.75.75 0 1 0 1.02 1.1l3.5-3.25a.75.75 0 0 0 0-1.1l-3.5-3.25a.75.75 0 0 0-1.06.04Zm-6.4 8a.75.75 0 0 0-1.06-.04l-3.5 3.25a.75.75 0 0 0 0 1.1l3.5 3.25a.75.75 0 1 0 1.02-1.1l-2.1-1.95h8.59a.75.75 0 0 0 0-1.5H4.66l2.1-1.95a.75.75 0 0 0 .04-1.06Z" clip-rule="evenodd" />
                </svg>

                <span>{{ till.t('transfer.label') }}</span>
            </button>

            <button
                v-if="till.canRenameOrder"
                type="button"
                :class="tile"
                @click="till.openOrderName()"
            >
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z" />
                    <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z" />
                </svg>

                <span>{{ till.t('order-name.label') }}</span>

                <span v-if="till.activeOrder.floating_name" class="truncate text-xs font-normal text-gray-500 dark:text-gray-400">
                    {{ till.activeOrder.floating_name }}
                </span>
            </button>

            <button
                v-if="!confirmingCancel"
                type="button"
                :class="danger"
                @click="confirmingCancel = true"
            >
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443A24 24 0 0 0 3.32 4.5a.75.75 0 0 0 .06 1.499l.46-.02.613 8.58A3 3 0 0 0 7.445 17.5h5.11a3 3 0 0 0 2.992-2.94l.613-8.581.46.02a.75.75 0 1 0 .06-1.5 24 24 0 0 0-2.68-.306V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325A42 42 0 0 1 10 4Z" clip-rule="evenodd" />
                </svg>

                <span>{{ till.t('actions.cancel-order.label') }}</span>
            </button>

            <button
                v-else
                type="button"
                class="flex min-h-20 flex-col items-center justify-center gap-1 rounded-lg bg-danger-600 px-3 py-4 text-sm font-semibold text-white transition-colors hover:bg-danger-700 active:bg-danger-800 disabled:opacity-60"
                :disabled="state.cancellingOrder"
                @click="till.cancelActiveOrder()"
            >
                <span>{{ till.t('actions.cancel-order.confirm') }}</span>

                <span class="text-center text-xs font-normal text-danger-100">
                    {{ till.t('actions.cancel-order.hint') }}
                </span>
            </button>
        </div>

        <p v-if="state.cancelError" class="px-4 pb-4 text-sm text-danger-600 dark:text-danger-400">
            {{ state.cancelError }}
        </p>
    </TillModal>
</template>
