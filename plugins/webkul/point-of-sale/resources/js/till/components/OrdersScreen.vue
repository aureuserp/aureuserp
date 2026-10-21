<script setup>
import { inject, computed } from 'vue'
import ReceiptBody from './ReceiptBody.vue'
import InvoiceCustomerModal from './InvoiceCustomerModal.vue'

const till = inject('till')

const state = till.state

const receiptOrder = computed(() => till.receiptOrderFor(state.historySelected))

const statuses = ['active', 'draft', 'paid', 'done', 'invoiced', 'canceled']

const keyBase = 'flex min-h-[clamp(2.1rem,4.6vh,3.25rem)] items-center justify-center rounded-lg border text-lg font-medium transition-colors'

const key = `${keyBase} border-gray-200 bg-white text-gray-950 hover:bg-gray-50 active:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800`

const warningKey = `${keyBase} border-warning-200 bg-warning-100 text-warning-900 hover:bg-warning-200 dark:border-warning-500/30 dark:bg-warning-500/20 dark:text-warning-200`

const infoKey = `${keyBase} border-info-200 bg-info-100 text-info-900 hover:bg-info-200 dark:border-info-500/30 dark:bg-info-500/20 dark:text-info-200`

const dangerKey = `${keyBase} border-danger-200 bg-danger-100 text-danger-800 hover:bg-danger-200 dark:border-danger-500/30 dark:bg-danger-500/20 dark:text-danger-200`

const isRestaurant = computed(() => till.isRestaurant)

const columnCount = computed(() => (isRestaurant.value ? 9 : 8))

function search() {
    state.historyPage = 1

    till.refreshHistory()
}

function statusLabel(value) {
    const label = till.t('history.status.' + value)

    return label === 'history.status.' + value ? value : label
}

function formatDate(value) {
    if (!value) {
        return ''
    }

    const date = new Date(value)

    return Number.isNaN(date.getTime()) ? value : date.toLocaleString()
}

function tableLabel(order) {
    if (!order.table) {
        return '—'
    }

    return [order.table.floor, order.table.table_number].filter(Boolean).join('/')
}

function back() {
    state.historyOpen = false

    if (till.isRestaurant) {
        till.goToFloor()
    } else {
        till.goToProducts()
    }
}
</script>

<template>
    <div class="grid min-h-0 grid-cols-[1fr] grid-rows-[minmax(0,1fr)] gap-4 p-4 lg:grid-cols-[1fr_minmax(22rem,32%)]">
        <section class="flex min-h-0 min-w-0 flex-col gap-3">
            <div class="flex flex-none items-center gap-2">
                <button
                    type="button"
                    class="inline-flex flex-none items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
                    @click="back()"
                >
                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L9.06 10l3.71 3.71a.75.75 0 1 1-1.06 1.06l-4.24-4.24a.75.75 0 0 1 0-1.06l4.24-4.24a.75.75 0 0 1 1.06-.02Z" clip-rule="evenodd" />
                    </svg>

                    {{ till.t('history.back') }}
                </button>

                <div class="relative min-w-0 flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-gray-400">
                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 2.95 10.14l3.2 3.21a.75.75 0 1 0 1.06-1.06l-3.21-3.2A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                        </svg>
                    </span>

                    <input
                        v-model="state.historySearch"
                        type="text"
                        :placeholder="till.t('history.search')"
                        class="w-full rounded-lg border border-gray-200 bg-white py-2 pl-11 pr-3 text-sm text-gray-950 placeholder:text-gray-400 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                        @keyup.enter="search()"
                    >
                </div>

                <select
                    v-model="state.historyStatus"
                    class="flex-none rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-950 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    @change="search()"
                >
                    <option v-for="status in statuses" :key="status" :value="status">{{ statusLabel(status) }}</option>
                </select>

                <span class="flex-none font-mono text-sm tabular-nums text-gray-600 dark:text-gray-300">
                    {{ state.historyPage }}/{{ Math.max(state.historyLastPage, 1) }}
                </span>

                <button
                    type="button"
                    class="flex size-9 flex-none items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-40 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                    :disabled="state.historyPage <= 1"
                    @click="till.goToHistoryPage(state.historyPage - 1)"
                >
                    <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L9.06 10l3.71 3.71a.75.75 0 1 1-1.06 1.06l-4.24-4.24a.75.75 0 0 1 0-1.06l4.24-4.24a.75.75 0 0 1 1.06-.02Z" clip-rule="evenodd" /></svg>
                </button>

                <button
                    type="button"
                    class="flex size-9 flex-none items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-40 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                    :disabled="state.historyPage >= state.historyLastPage"
                    @click="till.goToHistoryPage(state.historyPage + 1)"
                >
                    <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.24 4.24a.75.75 0 0 1 0 1.06l-4.24 4.24a.75.75 0 0 1-1.06.02Z" clip-rule="evenodd" /></svg>
                </button>
            </div>

            <div class="min-h-0 flex-auto overflow-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 z-[1] bg-gray-700 text-xs uppercase tracking-wide text-white dark:bg-gray-800">
                        <tr>
                            <th class="px-3 py-2 text-start font-medium">{{ till.t('history.columns.date') }}</th>
                            <th class="px-3 py-2 text-start font-medium">{{ till.t('history.columns.receipt') }}</th>
                            <th class="px-3 py-2 text-start font-medium">{{ till.t('history.columns.order') }}</th>
                            <th class="px-3 py-2 text-start font-medium">{{ till.t('history.columns.customer') }}</th>
                            <th class="px-3 py-2 text-start font-medium">{{ till.t('history.columns.cashier') }}</th>
                            <th class="px-3 py-2 text-start font-medium">{{ till.t('history.columns.tracking') }}</th>

                            <th v-if="isRestaurant" class="px-3 py-2 text-start font-medium">{{ till.t('history.columns.table') }}</th>

                            <th class="px-3 py-2 text-center font-medium">{{ till.t('history.columns.total') }}</th>
                            <th class="px-3 py-2 text-center font-medium">{{ till.t('history.columns.status') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="order in state.history"
                            :key="order.id"
                            class="cursor-pointer border-b border-gray-100 transition-colors dark:border-gray-800"
                            :class="state.historySelected?.id === order.id
                                ? 'bg-primary-600 text-white'
                                : 'odd:bg-gray-50 hover:bg-gray-100 dark:odd:bg-gray-800/40 dark:hover:bg-gray-800'"
                            @click="till.selectHistoryOrder(order)"
                        >
                            <td class="whitespace-nowrap px-3 py-2 font-mono text-xs tabular-nums">{{ formatDate(order.ordered_at) }}</td>

                            <td class="whitespace-nowrap px-3 py-2 font-mono text-xs">{{ order.reference }}</td>

                            <td class="whitespace-nowrap px-3 py-2">{{ order.name }}</td>

                            <td class="max-w-40 truncate px-3 py-2">{{ order.partner?.name ?? till.t('history.walk-in') }}</td>

                            <td class="max-w-32 truncate px-3 py-2">{{ order.cashier }}</td>

                            <td class="whitespace-nowrap px-3 py-2 font-mono text-xs tabular-nums">{{ order.tracking_number ?? '—' }}</td>

                            <td v-if="isRestaurant" class="whitespace-nowrap px-3 py-2">{{ tableLabel(order) }}</td>

                            <td class="whitespace-nowrap px-3 py-2 text-center font-mono font-semibold tabular-nums">{{ till.money(order.amount_total) }}</td>

                            <td class="px-3 py-2 text-center">{{ statusLabel(order.state) }}</td>
                        </tr>

                        <tr v-if="!state.history.length">
                            <td :colspan="columnCount" class="px-3 py-10 text-center text-gray-500 dark:text-gray-400">
                                {{ state.historyLoading ? '' : till.t('history.empty') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="flex min-h-0 min-w-0 flex-col">
            <div v-if="state.historySelected" class="flex min-h-0 flex-auto flex-col gap-2">
                <p
                    v-if="till.canRefundSelected()"
                    class="flex-none rounded-lg border border-gray-200 bg-white px-3 py-2 text-center text-sm text-danger-600 dark:border-gray-700 dark:bg-gray-900 dark:text-danger-400"
                >
                    {{ till.t('history.refund-prompt') }}
                </p>

                <div class="flex min-h-0 flex-auto flex-col overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                    <div class="min-h-0 flex-auto overflow-y-auto">
                        <button
                            v-for="line in state.historySelected.lines ?? []"
                            :key="line.id"
                            type="button"
                            class="flex w-full items-start gap-2 border-b border-gray-100 px-3 py-2.5 text-start transition-colors disabled:cursor-default dark:border-gray-800"
                            :class="state.refundActiveLine === line.id
                                ? 'bg-primary-50 dark:bg-primary-500/10'
                                : (state.refundLines[line.id] ? 'hover:bg-gray-50 dark:hover:bg-gray-800' : 'opacity-50')"
                            :disabled="!state.refundLines[line.id]"
                            @click="till.selectRefundLine(line.id)"
                        >
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-medium text-gray-950 dark:text-white">{{ line.full_product_name }}</span>

                                <span class="block font-mono text-xs tabular-nums text-gray-500 dark:text-gray-400">
                                    {{ line.qty + 0 }} &times; {{ till.money(line.price_unit) }}
                                </span>

                                <span
                                    v-if="state.refundLines[line.id] && state.refundLines[line.id].qty > 0"
                                    class="block font-mono text-xs tabular-nums text-primary-600 dark:text-primary-400"
                                >
                                    {{ till.t('history.to-refund') }} {{ state.refundLines[line.id].qty }}
                                </span>
                            </span>

                            <span class="flex-none font-mono text-sm font-semibold tabular-nums text-gray-950 dark:text-white">{{ till.money(line.price_subtotal_incl) }}</span>
                        </button>
                    </div>

                    <div class="flex-none border-t border-gray-100 p-3 dark:border-gray-800">
                        <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                            <span>{{ till.t('history.taxes') }}</span>

                            <span class="font-mono tabular-nums">{{ till.money(state.historySelected.amount_tax) }}</span>
                        </div>

                        <div class="mt-1 flex items-center justify-between text-lg font-semibold text-gray-950 dark:text-white">
                            <span>{{ till.t('history.total') }}</span>

                            <span class="font-mono tabular-nums">{{ till.money(state.historySelected.amount_total) }}</span>
                        </div>
                    </div>
                </div>

                <div class="grid flex-none grid-cols-3 gap-2">
                    <button
                        v-if="!state.historySelected.is_invoiced"
                        type="button"
                        class="flex min-h-11 items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
                        :disabled="state.historyBusyId === state.historySelected.id"
                        @click="till.invoiceHistoryOrder(state.historySelected)"
                    >
                        {{ till.t('history.invoice') }}
                    </button>

                    <a
                        v-else
                        :href="till.orderEndpoint('order_invoice_pdf_endpoint', state.historySelected.id)"
                        target="_blank"
                        rel="noopener"
                        class="flex min-h-11 items-center justify-center gap-2 rounded-lg border border-success-200 bg-success-50 text-sm font-medium text-success-700 hover:bg-success-100 dark:border-success-500/30 dark:bg-success-500/10 dark:text-success-300 dark:hover:bg-success-500/20"
                    >
                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5 2.75C5 1.784 5.784 1 6.75 1h6.5c.966 0 1.75.784 1.75 1.75v3.552c.377.046.752.097 1.126.153A2.212 2.212 0 0 1 18 8.653v4.097A2.25 2.25 0 0 1 15.75 15h-.241l.305 1.984A1.75 1.75 0 0 1 14.084 19H5.915a1.75 1.75 0 0 1-1.73-2.016L4.492 15H4.25A2.25 2.25 0 0 1 2 12.75V8.653c0-1.082.775-2.034 1.874-2.198.374-.056.75-.107 1.127-.153V2.75Zm8.5 3.397a41.533 41.533 0 0 0-7 0V2.75a.25.25 0 0 1 .25-.25h6.5a.25.25 0 0 1 .25.25v3.397ZM6.608 12.5a.25.25 0 0 0-.247.212l-.692 4.5a.25.25 0 0 0 .247.288h8.168a.25.25 0 0 0 .247-.288l-.692-4.5a.25.25 0 0 0-.247-.212H6.608Z" clip-rule="evenodd" /></svg>

                        {{ till.t('history.print-invoice') }}
                    </a>

                    <button
                        v-if="state.historySelected.state !== 'cancel'"
                        type="button"
                        class="flex min-h-11 items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
                        @click="till.printHistoryReceipt()"
                    >
                        {{ till.t('history.print') }}
                    </button>

                    <a
                        :href="till.boot.config.order_details_endpoint.replace('__order__', state.historySelected.id)"
                        target="_blank"
                        rel="noopener"
                        class="flex min-h-11 items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        {{ till.t('history.details') }}
                    </a>

                    <button
                        v-if="state.historySelected.state === 'draft'"
                        type="button"
                        class="flex min-h-11 items-center justify-center gap-2 rounded-lg border border-danger-200 bg-danger-50 text-sm font-medium text-danger-700 hover:bg-danger-100 disabled:opacity-50 dark:border-danger-500/30 dark:bg-danger-500/10 dark:text-danger-300"
                        :disabled="state.historyBusyId === state.historySelected.id"
                        @click="till.cancelHistoryOrder(state.historySelected)"
                    >
                        {{ till.t('history.cancel') }}
                    </button>
                </div>

                <div v-if="till.canRefundSelected()" class="grid flex-none grid-cols-4 gap-[clamp(0.25rem,0.8vh,0.5rem)]">
                    <button v-for="digit in ['1', '2', '3']" :key="digit" type="button" :class="key" @click="till.pressRefund(digit)">{{ digit }}</button>

                    <div class="col-start-4 row-span-3 row-start-1 flex flex-col overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                        <button type="button" class="flex flex-1 items-center justify-center border-b border-gray-200 bg-primary-100 text-sm font-semibold text-primary-900 dark:border-gray-700 dark:bg-primary-500/25 dark:text-primary-100">{{ till.t('numpad.qty') }}</button>

                        <button type="button" disabled class="flex flex-1 items-center justify-center border-b border-gray-200 bg-white text-sm font-medium text-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-600">%</button>

                        <button type="button" disabled class="flex flex-1 items-center justify-center bg-white text-sm font-medium text-gray-300 dark:bg-gray-900 dark:text-gray-600">{{ till.t('numpad.price') }}</button>
                    </div>

                    <button v-for="digit in ['4', '5', '6']" :key="digit" type="button" :class="key" @click="till.pressRefund(digit)">{{ digit }}</button>

                    <button v-for="digit in ['7', '8', '9']" :key="digit" type="button" :class="key" @click="till.pressRefund(digit)">{{ digit }}</button>

                    <button type="button" :class="warningKey" @click="till.pressRefund('clear')">{{ till.t('common.clear') }}</button>

                    <button type="button" :class="key" @click="till.pressRefund('0')">0</button>

                    <button type="button" :class="infoKey" @click="till.pressRefund('.')">.</button>

                    <button type="button" :class="dangerKey" :aria-label="till.t('numpad.backspace')" @click="till.pressRefund('backspace')">&#9003;</button>
                </div>

                <button
                    v-if="till.canRefundSelected()"
                    type="button"
                    class="flex min-h-11 flex-none items-center justify-center gap-2 rounded-lg bg-primary-600 text-sm font-semibold text-white hover:bg-primary-700 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:bg-gray-800"
                    :disabled="till.refundTotalQty() <= 0 || state.historyBusyId === state.historySelected.id"
                    @click="till.submitRefund()"
                >
                    {{ till.t('history.refund') }}
                    <span v-if="till.refundTotalQty() > 0" class="font-mono tabular-nums">· {{ till.t('history.to-refund') }} {{ till.refundTotalQty() }}</span>
                </button>

                <p v-if="state.refundError" class="flex-none text-sm text-danger-600 dark:text-danger-400">{{ state.refundError }}</p>

                <p v-if="state.historyError" class="flex-none text-sm text-danger-600 dark:text-danger-400">{{ state.historyError }}</p>

                <div class="hidden" aria-hidden="true">
                    <ReceiptBody :order="receiptOrder" />
                </div>
            </div>

            <div
                v-else
                class="flex min-h-0 flex-auto items-center justify-center rounded-xl border border-dashed border-gray-200 p-6 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400"
            >
                {{ till.t('history.select-order') }}
            </div>
        </aside>

        <InvoiceCustomerModal />
    </div>
</template>
