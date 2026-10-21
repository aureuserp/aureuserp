<script setup>
import { inject, computed } from 'vue'

const till = inject('till')

const state = till.state

const canPay = computed(() => Boolean(till.activeOrder && till.sellableLines(till.activeOrder).length))

const showsKitchen = computed(() => till.isRestaurant && till.sendsToKitchen)

const kitchen = computed(() => till.preparationSummary())

const hasActiveLine = computed(() => Boolean(till.activeLine))

const partnerName = computed(() => {
    const order = till.activeOrder

    return order?.partner_id ? (till.master.partners.get(order.partner_id)?.name ?? null) : null
})

const keyBase = 'flex min-h-[clamp(2.1rem,4.6vh,3.25rem)] items-center justify-center rounded-lg border text-lg font-medium transition-colors'

const key = `${keyBase} border-gray-200 bg-white text-gray-950 hover:bg-gray-50 active:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800`

const modes = computed(() => [
    { value: 'qty', label: till.t('numpad.qty'), disabled: false },
    { value: 'discount', label: '%', disabled: !till.config.enable_line_discount },
    { value: 'price', label: till.t('numpad.price'), disabled: till.config.can_edit_price === false },
])

const lastRow = [
    { k: '-', label: '+/-', tone: 'border-warning-200 bg-warning-100 text-warning-900 hover:bg-warning-200 dark:border-warning-500/30 dark:bg-warning-500/20 dark:text-warning-200 dark:hover:bg-warning-500/30' },
    { k: '0', label: '0', tone: '' },
    { k: '.', label: '.', tone: 'border-info-200 bg-info-100 text-info-900 hover:bg-info-200 dark:border-info-500/30 dark:bg-info-500/20 dark:text-info-200 dark:hover:bg-info-500/30' },
]

function modeClass(mode, index) {
    const base = 'flex flex-1 items-center justify-center text-sm font-medium transition-colors disabled:cursor-not-allowed disabled:text-gray-300 dark:disabled:text-gray-600'

    const active = state.numpadMode === mode.value
        ? 'bg-primary-100 font-semibold text-primary-900 hover:bg-primary-200 dark:bg-primary-500/25 dark:text-primary-100'
        : 'bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800'

    const divider = index < 2 ? 'border-b border-gray-200 dark:border-gray-700' : ''

    return `${base} ${active} ${divider}`
}
</script>

<template>
    <div class="flex flex-col gap-[clamp(0.25rem,0.8vh,0.5rem)]">
        <div class="grid grid-cols-3 gap-[clamp(0.25rem,0.8vh,0.5rem)]">
            <button
                type="button"
                class="flex min-h-[clamp(2.5rem,5.5vh,3.5rem)] min-w-0 items-center justify-center gap-2 rounded-lg border px-2 text-base font-medium transition-colors disabled:cursor-not-allowed disabled:opacity-60"
                :class="partnerName
                    ? 'border-primary-500 bg-primary-50 text-primary-800 hover:bg-primary-100 dark:border-primary-400 dark:bg-primary-500/15 dark:text-primary-200'
                    : 'border-gray-200 bg-white text-gray-600 hover:bg-gray-50 active:bg-gray-100 dark:active:bg-gray-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800'"
                :disabled="till.isCustomerLocked"
                :title="partnerName ?? till.t('actions.customer')"
                @click="till.openCustomers()"
            >
                <svg class="size-5 flex-none" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.96 9.96 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z" />
                </svg>

                <span class="truncate">{{ partnerName ?? till.t('actions.customer') }}</span>
            </button>

            <button
                type="button"
                class="flex min-h-[clamp(2.5rem,5.5vh,3.5rem)] items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white text-base font-medium text-gray-600 transition-colors hover:bg-gray-50 active:bg-gray-100 dark:active:bg-gray-700 disabled:text-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:disabled:text-gray-600"
                :disabled="!hasActiveLine"
                :title="till.noteLabel"
                @click="till.openNotes()"
            >
                <svg class="size-5 flex-none" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M3.75 3A1.75 1.75 0 0 0 2 4.75v10.5c0 .966.784 1.75 1.75 1.75h8.5A1.75 1.75 0 0 0 14 15.25V9.664a2.25 2.25 0 0 0-.659-1.591l-4.414-4.414A2.25 2.25 0 0 0 7.336 3H3.75Zm1.5 6.5a.75.75 0 0 1 .75-.75h4a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1-.75-.75Zm.75 2.75a.75.75 0 0 0 0 1.5h4a.75.75 0 0 0 0-1.5H6Z" />
                </svg>

                {{ till.t('actions.note') }}
            </button>

            <button
                type="button"
                class="flex min-h-[clamp(2.5rem,5.5vh,3.5rem)] items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white text-base font-medium text-gray-600 transition-colors hover:bg-gray-50 active:bg-gray-100 dark:active:bg-gray-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
                :title="till.t('actions.label')"
                @click="till.openActions()"
            >
                <svg class="size-5 flex-none" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 3a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM10 8.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM11.5 15.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z" />
                </svg>

                {{ till.t('actions.label') }}
            </button>
        </div>

        <div v-if="hasActiveLine" class="grid grid-cols-4 gap-[clamp(0.25rem,0.8vh,0.5rem)]">
            <button v-for="digit in ['1', '2', '3']" :key="digit" type="button" :class="key" @click="till.pressNumpad(digit)">
                {{ digit }}
            </button>

            <div class="col-start-4 row-span-3 row-start-1 flex flex-col overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                <button
                    v-for="(mode, index) in modes"
                    :key="mode.value"
                    type="button"
                    :class="modeClass(mode, index)"
                    :disabled="mode.disabled"
                    @click="till.setNumpadMode(mode.value)"
                >
                    {{ mode.label }}
                </button>
            </div>

            <button v-for="digit in ['4', '5', '6']" :key="digit" type="button" :class="key" @click="till.pressNumpad(digit)">
                {{ digit }}
            </button>

            <button v-for="digit in ['7', '8', '9']" :key="digit" type="button" :class="key" @click="till.pressNumpad(digit)">
                {{ digit }}
            </button>

            <button
                v-for="entry in lastRow"
                :key="entry.k"
                type="button"
                :class="entry.tone ? `${keyBase} ${entry.tone}` : key"
                @click="till.pressNumpad(entry.k)"
            >
                {{ entry.label }}
            </button>

            <button
                type="button"
                :class="`${keyBase} border-danger-200 bg-danger-100 text-danger-800 hover:bg-danger-200 dark:border-danger-500/30 dark:bg-danger-500/20 dark:text-danger-200 dark:hover:bg-danger-500/30`"
                :aria-label="till.t('numpad.backspace')"
                @click="till.pressNumpad('Backspace')"
            >
                &#9003;
            </button>
        </div>

        <div class="flex gap-2">
            <button
                v-if="showsKitchen"
                type="button"
                class="flex min-h-[clamp(2.6rem,5.5vh,3.5rem)] flex-1 flex-col items-center justify-center rounded-lg px-2 text-base font-semibold transition-colors disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:bg-gray-800"
                :class="kitchen.count
                    ? 'bg-warning-500 text-white hover:bg-warning-600'
                    : 'border border-gray-200 bg-white text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400'"
                :disabled="!kitchen.count"
                @click="till.sendToKitchen()"
            >
                <span>{{ till.t('kitchen.order') }}</span>

                <span v-if="kitchen.categories.length" class="flex max-w-full gap-2 truncate text-[0.625rem] font-medium leading-tight">
                    <span v-for="category in kitchen.categories.slice(0, 3)" :key="category.name">{{ category.name }} {{ category.qty }}</span>
                </span>
            </button>

            <button
                type="button"
                class="flex min-h-[clamp(2.6rem,5.5vh,3.5rem)] flex-1 items-center justify-center rounded-lg bg-primary-600 text-base font-semibold text-white transition-colors hover:bg-primary-700 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:bg-gray-800"
                :disabled="!canPay"
                @click="till.goToPayment()"
            >
                {{ till.t('actions.payment') }}
            </button>
        </div>
    </div>
</template>
