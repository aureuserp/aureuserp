<script setup>
import { inject, computed } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state

const columns = computed(() => {
    const bills = till.moneyBills

    const half = Math.ceil(bills.length / 2)

    return [bills.slice(0, half), bills.slice(half)]
})

const stepper = 'flex size-9 flex-none items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 transition-colors hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800'
</script>

<template>
    <TillModal
        :open="state.moneyDetailsOpen"
        width="max-w-xl"
        :heading="till.t('money-details.label')"
        @close="state.moneyDetailsOpen = false"
    >
        <div class="grid gap-2 sm:grid-cols-2">
            <div v-for="(column, index) in columns" :key="index" class="flex flex-col gap-2">
                <div v-for="bill in column" :key="bill.id" class="flex items-center gap-2">
                    <button type="button" :class="stepper" :aria-label="till.t('money-details.decrease')" @click="till.stepMoneyDetail(bill.id, -1)">
                        <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H4.75A.75.75 0 0 1 4 10Z" clip-rule="evenodd" /></svg>
                    </button>

                    <input
                        type="number"
                        min="0"
                        :value="state.moneyDetails[bill.id] ?? 0"
                        class="h-9 w-20 rounded-lg border border-gray-200 bg-white px-2 text-sm text-gray-950 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                        @input="till.setMoneyDetail(bill.id, $event.target.value)"
                    >

                    <button type="button" :class="stepper" :aria-label="till.t('money-details.increase')" @click="till.stepMoneyDetail(bill.id, 1)">
                        <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" /></svg>
                    </button>

                    <span class="font-mono text-sm tabular-nums text-gray-950 dark:text-white">{{ till.money(Number(bill.value)) }}</span>
                </div>
            </div>
        </div>

        <template #footer>
            <button
                type="button"
                class="flex min-h-11 items-center justify-center rounded-lg bg-primary-600 px-6 text-sm font-semibold text-white hover:bg-primary-700"
                @click="till.confirmMoneyDetails()"
            >
                {{ till.t('money-details.confirm') }}
            </button>

            <p class="ml-auto font-mono text-lg font-semibold tabular-nums text-gray-950 dark:text-white">
                {{ till.t('money-details.total', { total: till.money(till.moneyDetailsTotal()) }) }}
            </p>
        </template>
    </TillModal>
</template>
