<script setup>
import { inject, computed } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state

const report = computed(() => state.closingReport)

const cash = computed(() => report.value?.default_cash_details ?? null)

const movesTotal = computed(() => (cash.value?.moves ?? []).reduce((total, move) => total + Number(move.amount), 0))

const field = 'h-10 w-full rounded-lg border border-gray-200 bg-white pe-10 ps-3 text-sm text-gray-950 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white'

const iconBtn = 'flex size-10 flex-none items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 transition-colors hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800'

const footerBtn = 'flex min-h-11 items-center justify-center gap-1.5 rounded-lg px-5 text-sm font-semibold transition-colors'

function rowClass(amount) {
    return amount !== 0
        ? 'flex items-center justify-between gap-4 text-sm font-semibold text-danger-600'
        : 'flex items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400'
}
</script>

<template>
    <TillModal
        :open="state.closingOpen"
        width="max-w-2xl"
        :heading="till.t('closing.heading')"
        @close="state.closingOpen = false"
    >
        <p v-if="state.closingBusy && !report" class="py-10 text-center text-sm text-gray-500 dark:text-gray-400">
            {{ till.t('money-details.label') }}…
        </p>

        <div v-else-if="report" class="flex flex-col gap-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ till.t('closing.orders', { quantity: report.orders_details.quantity }) }}:
                <span class="font-mono tabular-nums">{{ till.money(report.orders_details.amount) }}</span>
            </p>

            <div v-if="cash" class="flex flex-col gap-1 border-b border-gray-100 pb-3 dark:border-gray-800">
                <div class="flex items-center justify-between gap-4 text-base font-semibold text-gray-950 dark:text-gray-50">
                    <span>{{ cash.name }}</span>
                    <span class="font-mono tabular-nums">{{ till.money(cash.amount) }}</span>
                </div>

                <div class="flex items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ till.t('closing.opening') }}</span>
                    <span class="font-mono tabular-nums">{{ till.money(cash.opening) }}</span>
                </div>

                <div class="flex items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ till.t('closing.payments') }}</span>
                    <span class="font-mono tabular-nums">{{ till.money(cash.payment_amount) }}</span>
                </div>

                <button type="button" class="flex w-full items-center justify-between gap-4 text-start text-sm text-gray-500 dark:text-gray-400" @click="till.toggleCashMoves()">
                    <span class="flex items-center gap-1">
                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path v-if="state.showCashMoves" fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            <path v-else fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                        </svg>

                        {{ till.t('closing.moves') }}
                    </span>

                    <span class="font-mono tabular-nums">{{ till.money(movesTotal) }}</span>
                </button>

                <template v-if="state.showCashMoves">
                    <div v-for="(move, index) in cash.moves" :key="index" class="flex items-center justify-between gap-4 ps-5 text-sm text-gray-500 dark:text-gray-400">
                        <span>{{ move.name }}</span>
                        <span class="font-mono tabular-nums">{{ till.money(move.amount) }}</span>
                    </div>
                </template>

                <div class="flex items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ till.t('closing.counted') }}</span>
                    <span class="font-mono tabular-nums">{{ till.money(Number(state.closingCashReal) || 0) }}</span>
                </div>

                <div :class="rowClass(till.cashDifference(cash))">
                    <span>{{ till.t('closing.difference') }}</span>
                    <span class="font-mono tabular-nums">{{ till.money(till.cashDifference(cash)) }}</span>
                </div>
            </div>

            <div v-for="method in report.non_cash_payment_methods" :key="method.id" class="flex flex-col gap-1 border-b border-gray-100 pb-3 dark:border-gray-800">
                <div class="flex items-center justify-between gap-4 text-base font-semibold text-gray-950 dark:text-gray-50">
                    <span>{{ method.name }}</span>
                    <span class="font-mono tabular-nums">{{ till.money(method.amount) }}</span>
                </div>

                <div class="flex items-center justify-between gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ till.t('closing.counted') }}</span>

                    <div class="flex items-center gap-1">
                        <div class="relative w-40">
                            <input v-model="state.closingCounted[method.id]" type="number" step="0.01" :class="field">

                            <button
                                type="button"
                                class="absolute inset-y-0 end-3 my-auto flex size-6 items-center justify-center rounded text-gray-400 transition-colors hover:text-gray-600 dark:hover:text-gray-200"
                                :aria-label="till.t('closing.clear')"
                                @click="state.closingCounted[method.id] = ''"
                            >
                                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" /></svg>
                            </button>
                        </div>

                        <button type="button" :class="iconBtn.replace('size-10', 'size-9')" :aria-label="till.t('closing.copy')" @click="till.copyExpectedPayment(method.id, method.amount)">
                            <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7 3.5A1.5 1.5 0 0 1 8.5 2h3.879a1.5 1.5 0 0 1 1.06.44l3.122 3.12A1.5 1.5 0 0 1 17 6.622V12.5a1.5 1.5 0 0 1-1.5 1.5h-1v-3.379a3 3 0 0 0-.879-2.121L10.5 5.379A3 3 0 0 0 8.379 4.5H7v-1Z" /><path d="M4.5 6A1.5 1.5 0 0 0 3 7.5v9A1.5 1.5 0 0 0 4.5 18h7a1.5 1.5 0 0 0 1.5-1.5v-5.879a1.5 1.5 0 0 0-.44-1.06L9.44 6.439A1.5 1.5 0 0 0 8.378 6H4.5Z" /></svg>
                        </button>
                    </div>
                </div>

                <div :class="rowClass(till.paymentDifference(method))">
                    <span>{{ till.t('closing.difference') }}</span>
                    <span class="font-mono tabular-nums">{{ till.money(till.paymentDifference(method)) }}</span>
                </div>
            </div>

            <div class="flex flex-col gap-2">
                <p class="text-sm font-semibold text-gray-950 dark:text-white">{{ till.t('closing.count') }}</p>

                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <input v-model="state.closingCashReal" type="number" step="0.01" :class="field">

                        <button
                            type="button"
                            class="absolute inset-y-0 end-3 my-auto flex size-6 items-center justify-center rounded text-gray-400 transition-colors hover:text-gray-600 dark:hover:text-gray-200"
                            :aria-label="till.t('closing.clear')"
                            @click="state.closingCashReal = ''"
                        >
                            <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" /></svg>
                        </button>
                    </div>

                    <button type="button" :class="iconBtn" :aria-label="till.t('money-details.label')" @click="till.openMoneyDetails('closing')">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" /></svg>
                    </button>

                    <button type="button" :class="iconBtn" :aria-label="till.t('closing.copy')" @click="till.copyExpectedCash()">
                        <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7 3.5A1.5 1.5 0 0 1 8.5 2h3.879a1.5 1.5 0 0 1 1.06.44l3.122 3.12A1.5 1.5 0 0 1 17 6.622V12.5a1.5 1.5 0 0 1-1.5 1.5h-1v-3.379a3 3 0 0 0-.879-2.121L10.5 5.379A3 3 0 0 0 8.379 4.5H7v-1Z" /><path d="M4.5 6A1.5 1.5 0 0 0 3 7.5v9A1.5 1.5 0 0 0 4.5 18h7a1.5 1.5 0 0 0 1.5-1.5v-5.879a1.5 1.5 0 0 0-.44-1.06L9.44 6.439A1.5 1.5 0 0 0 8.378 6H4.5Z" /></svg>
                    </button>
                </div>

                <template v-if="report.amount_authorized_diff !== null">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ till.t('closing.authorized-diff', { amount: till.money(report.amount_authorized_diff) }) }}
                    </p>

                    <p v-if="till.exceedsAuthorizedDifference(report)" class="text-xs font-semibold text-danger-600 dark:text-danger-400">
                        {{ till.t('closing.authorized-diff-exceeded') }}
                    </p>
                </template>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="flex flex-col gap-1">
                    <p class="text-sm font-semibold text-gray-950 dark:text-white">{{ till.t('closing.opening-note') }}</p>

                    <p class="min-h-20 whitespace-pre-line rounded-lg border border-gray-200 px-3 py-2 text-[0.8125rem] text-gray-500 dark:border-gray-700 dark:text-gray-400">{{ report.opening_notes || '—' }}</p>
                </div>

                <div class="flex flex-col gap-1">
                    <p class="text-sm font-semibold text-gray-950 dark:text-white">{{ till.t('closing.note') }}</p>

                    <textarea
                        v-model="state.closingNotes"
                        rows="3"
                        class="block min-h-20 w-full rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-950 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    ></textarea>
                </div>
            </div>

            <p v-if="state.closingUnbalanced !== null" class="rounded-lg border border-danger-300 bg-danger-50 p-3 text-sm text-danger-700 dark:border-danger-500/40 dark:bg-danger-500/10 dark:text-danger-300">
                {{ till.t('closing.unbalanced', { amount: till.money(state.closingUnbalanced) }) }}
            </p>

            <p v-else-if="state.closingError" class="text-sm text-danger-600 dark:text-danger-400">{{ state.closingError }}</p>
        </div>

        <template #footer>
            <button type="button" :class="`${footerBtn} bg-danger-600 text-white hover:bg-danger-700 disabled:opacity-60`" :disabled="state.closingBusy" @click="till.submitClose()">
                {{ till.t('closing.confirm') }}
            </button>

            <button type="button" :class="`${footerBtn} border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800`" @click="state.closingOpen = false">
                {{ till.t('closing.discard') }}
            </button>

            <button type="button" :class="`${footerBtn} border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800`" @click="till.openCash('in')">
                {{ till.t('menu.cash-in-out') }}
            </button>

            <button type="button" :class="`${footerBtn} border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800`" @click="till.downloadSalesDetails()">
                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 2.75a.75.75 0 0 0-1.5 0v8.614L6.295 8.235a.75.75 0 1 0-1.09 1.03l4.25 4.5a.75.75 0 0 0 1.09 0l4.25-4.5a.75.75 0 0 0-1.09-1.03l-2.955 3.129V2.75Z" /><path d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z" /></svg>

                {{ till.t('closing.daily-sale') }}
            </button>
        </template>
    </TillModal>
</template>
