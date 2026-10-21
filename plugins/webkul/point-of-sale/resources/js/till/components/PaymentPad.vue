<script setup>
import { inject, computed } from 'vue'

const till = inject('till')

const canValidate = computed(() => till.canAttemptValidate())

const order = computed(() => till.activeOrder)

const key = 'flex min-h-[clamp(2.1rem,4.6vh,3.25rem)] items-center justify-center rounded-lg border text-lg font-medium transition-colors disabled:opacity-40'

const plain = `${key} border-gray-200 bg-white text-gray-950 hover:bg-gray-50 active:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800`

const bump = `${key} border-success-200 bg-success-100 text-success-800 hover:bg-success-200 dark:border-success-500/30 dark:bg-success-500/20 dark:text-success-200`

const rows = [
    [{ k: '1' }, { k: '2' }, { k: '3' }, { k: '+10', tone: bump }],
    [{ k: '4' }, { k: '5' }, { k: '6' }, { k: '+20', tone: bump }],
    [{ k: '7' }, { k: '8' }, { k: '9' }, { k: '+50', tone: bump }],
    [
        { k: '-', label: '+/-', tone: `${key} border-warning-200 bg-warning-100 text-warning-900 hover:bg-warning-200 dark:border-warning-500/30 dark:bg-warning-500/20 dark:text-warning-200` },
        { k: '0' },
        { k: '.', tone: `${key} border-info-200 bg-info-100 text-info-900 hover:bg-info-200 dark:border-info-500/30 dark:bg-info-500/20 dark:text-info-200` },
        { k: 'Backspace', label: '⌫', tone: `${key} border-danger-200 bg-danger-100 text-danger-800 hover:bg-danger-200 dark:border-danger-500/30 dark:bg-danger-500/20 dark:text-danger-200` },
    ],
]
</script>

<template>
    <div class="flex flex-col gap-[clamp(0.25rem,0.8vh,0.5rem)]">
        <div class="grid grid-cols-2 gap-[clamp(0.25rem,0.8vh,0.5rem)]">
            <button
                type="button"
                class="flex min-h-[clamp(2.5rem,5.5vh,3.5rem)] min-w-0 items-center justify-center gap-2 rounded-lg border px-2 text-base font-medium transition-colors disabled:cursor-not-allowed disabled:opacity-60"
                :class="order?.partner_id
                    ? 'border-primary-500 bg-primary-50 text-primary-800 dark:border-primary-400 dark:bg-primary-500/15 dark:text-primary-200'
                    : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300'"
                :disabled="till.isCustomerLocked"
                @click="till.openCustomers()"
            >
                <svg class="size-5 flex-none" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.96 9.96 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z" />
                </svg>

                <span class="truncate">
                    {{ (order?.partner_id ? till.master.partners.get(order.partner_id)?.name : null) ?? till.t('actions.customer') }}
                </span>
            </button>

            <button
                type="button"
                class="flex min-h-[clamp(2.5rem,5.5vh,3.5rem)] items-center justify-center gap-2 rounded-lg border px-2 text-base font-medium transition-colors"
                :class="order?.to_invoice
                    ? 'border-primary-500 bg-primary-50 text-primary-800 dark:border-primary-400 dark:bg-primary-500/15 dark:text-primary-200'
                    : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300'"
                @click="till.toggleToInvoice()"
            >
                <svg class="size-5 flex-none" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M3.75 3A1.75 1.75 0 0 0 2 4.75v10.5c0 .966.784 1.75 1.75 1.75h8.5A1.75 1.75 0 0 0 14 15.25V9.664a2.25 2.25 0 0 0-.659-1.591l-4.414-4.414A2.25 2.25 0 0 0 7.336 3H3.75Z" />
                </svg>

                {{ till.t('payment.invoice') }}

                <span
                    class="ms-1 flex size-4 flex-none items-center justify-center rounded border"
                    :class="order?.to_invoice ? 'border-primary-500 bg-primary-600 text-white' : 'border-gray-300 dark:border-gray-600'"
                >
                    <svg v-if="order?.to_invoice" class="size-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                    </svg>
                </span>
            </button>
        </div>

        <button
            v-if="till.canShipLater()"
            type="button"
            class="flex min-h-[clamp(2.5rem,5.5vh,3.5rem)] w-full items-center gap-2 rounded-lg border px-3 text-base font-medium transition-colors"
            :class="order?.shipped_at
                ? 'border-primary-500 bg-primary-50 text-primary-800 dark:border-primary-400 dark:bg-primary-500/15 dark:text-primary-200'
                : 'justify-center border-gray-200 bg-white text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300'"
            @click="till.toggleShipLater()"
        >
            <span class="flex items-center gap-2">
                <svg class="size-5 flex-none" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-13a.75.75 0 0 0-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 0 0 0-1.5h-3.25V5Z" clip-rule="evenodd" />
                </svg>

                {{ till.t('payment.ship-later') }}
            </span>

            <span v-if="order?.shipped_at" class="ms-auto font-mono tabular-nums">{{ order.shipped_at }}</span>
        </button>

        <button
            v-if="till.tipProductId"
            type="button"
            class="flex min-h-[clamp(2.5rem,5.5vh,3.5rem)] w-full items-center gap-2 rounded-lg border px-3 text-base font-medium transition-colors"
            :class="till.tipAmount()
                ? 'border-primary-500 bg-primary-50 text-primary-800 dark:border-primary-400 dark:bg-primary-500/15 dark:text-primary-200'
                : 'justify-center border-gray-200 bg-white text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300'"
            @click="till.openTip()"
        >
            <span class="flex items-center gap-2">
                <svg class="size-5 flex-none" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="m9.653 16.915-.005-.003-.019-.01a20.759 20.759 0 0 1-1.162-.682 22.045 22.045 0 0 1-2.582-1.9C4.045 12.733 2 10.352 2 7.5a4.5 4.5 0 0 1 8-2.828A4.5 4.5 0 0 1 18 7.5c0 2.852-2.044 5.233-3.885 6.82a22.049 22.049 0 0 1-3.744 2.582l-.019.01-.005.003h-.002a.739.739 0 0 1-.69.001l-.002-.001Z" />
                </svg>

                {{ till.t('tip.label') }}
            </span>

            <span v-if="till.tipAmount()" class="ms-auto font-mono tabular-nums">{{ till.money(till.tipAmount()) }}</span>
        </button>

        <div class="grid grid-cols-4 gap-[clamp(0.25rem,0.8vh,0.5rem)]">
            <template v-for="(row, index) in rows" :key="index">
                <button
                    v-for="entry in row"
                    :key="entry.k"
                    type="button"
                    :class="entry.tone ?? plain"
                    :disabled="!till.activePayment && !(order && !order.payments.length && till.startsPaymentLine(entry.k))"
                    @click="till.pressPaymentKey(entry.k)"
                >
                    {{ entry.label ?? entry.k }}
                </button>
            </template>
        </div>

        <div class="flex gap-2">
            <button
                type="button"
                class="flex min-h-[clamp(2.6rem,5.5vh,3.5rem)] flex-1 items-center justify-center rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                @click="till.goToProducts()"
            >
                {{ till.t('common.back') }}
            </button>

            <button
                type="button"
                class="flex min-h-[clamp(2.6rem,5.5vh,3.5rem)] flex-2 items-center justify-center rounded-lg bg-primary-600 text-base font-semibold text-white transition-colors hover:bg-primary-700 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:bg-gray-800"
                :disabled="!canValidate"
                @click="till.validate()"
            >
                {{ till.t('payment.validate') }}
            </button>
        </div>
    </div>
</template>
