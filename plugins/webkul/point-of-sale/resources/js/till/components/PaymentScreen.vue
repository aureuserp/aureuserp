<script setup>
import { inject, computed } from 'vue'

const till = inject('till')

const state = till.state

const order = computed(() => till.activeOrder)

const totals = computed(() => till.orderTotals())

const hasRemaining = computed(() => totals.value.change <= 0)

const isSettled = computed(() => totals.value.covered)

function methodName(id) {
    return till.master.payment_methods.get(id)?.name ?? ''
}
</script>

<template>
    <div class="flex min-h-0 flex-auto flex-col gap-3">
        <div class="flex-none rounded-xl border border-gray-200 bg-white py-6 text-center dark:border-gray-700 dark:bg-gray-900">
            <p class="font-mono text-4xl font-bold tabular-nums text-success-600 dark:text-success-400">
                {{ till.money(totals.total) }}
            </p>
        </div>

        <div class="flex min-h-0 flex-auto flex-col justify-center gap-2 overflow-y-auto">
            <p v-if="!order?.payments.length" class="rounded-xl border border-gray-200 bg-white p-6 text-center text-base text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400">
                {{ till.t('payment.select-method') }}
            </p>

            <template v-else>
                <div
                    class="flex flex-none items-center justify-between rounded-xl border px-4 py-3 text-lg font-semibold"
                    :class="isSettled
                        ? 'border-success-300 bg-success-50 text-success-700 dark:border-success-500/40 dark:bg-success-500/10 dark:text-success-300'
                        : 'border-danger-300 bg-danger-50 text-danger-700 dark:border-danger-500/40 dark:bg-danger-500/10 dark:text-danger-300'"
                >
                    <span>{{ hasRemaining ? 'Remaining' : 'Change' }}</span>

                    <span class="font-mono tabular-nums">
                        {{ till.money(hasRemaining ? (isSettled ? 0 : Math.abs(totals.due)) : totals.change) }}
                    </span>
                </div>

                <div
                    v-for="payment in order.payments"
                    :key="payment.uuid"
                    class="flex flex-none items-center gap-3 rounded-xl border px-4 py-3 transition-colors"
                    :class="state.activePaymentUuid === payment.uuid
                        ? 'border-primary-500 bg-primary-50 dark:border-primary-400 dark:bg-primary-500/10'
                        : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900'"
                >
                    <button
                        type="button"
                        class="flex min-w-0 flex-1 items-center justify-between gap-3 text-start"
                        @click="till.selectPayment(payment.uuid)"
                    >
                        <span class="truncate text-base font-medium text-gray-950 dark:text-white">
                            {{ methodName(payment.payment_method_id) }}
                            <span v-if="payment.is_change" class="text-xs font-normal text-gray-400">{{ till.t('payment.change') }}</span>
                        </span>

                        <span class="flex-none font-mono text-lg font-semibold tabular-nums text-gray-950 dark:text-white">
                            {{ till.money(payment.amount) }}
                        </span>
                    </button>

                    <button
                        type="button"
                        class="flex size-9 flex-none items-center justify-center rounded-lg text-danger-500 hover:bg-danger-50 dark:hover:bg-danger-500/10"
                        :aria-label="till.t('payment.remove', { method: methodName(payment.payment_method_id) })"
                        @click="till.removePayment(payment.uuid)"
                    >
                        <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                        </svg>
                    </button>
                </div>
            </template>
        </div>
    </div>
</template>
