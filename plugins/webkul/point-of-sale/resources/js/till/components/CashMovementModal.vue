<script setup>
import { inject } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state

const toggle = 'flex min-h-13 items-center justify-center rounded-lg border border-gray-200 bg-white text-[0.9375rem] font-medium text-gray-600 transition-colors aria-pressed:border-primary-600 aria-pressed:bg-primary-50 aria-pressed:font-semibold aria-pressed:text-primary-700 hover:bg-gray-50 dark:hover:bg-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:aria-pressed:border-primary-500 dark:aria-pressed:bg-primary-500/20 dark:aria-pressed:text-primary-300'

const field = 'h-11 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-950 placeholder:text-gray-400 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white'
</script>

<template>
    <TillModal
        :open="state.cashOpen"
        width="max-w-md"
        :heading="till.t('cash-movement.heading')"
        @close="state.cashOpen = false"
    >
        <div class="flex flex-col gap-3">
            <div class="grid grid-cols-2 gap-2">
                <button type="button" :class="toggle" :aria-pressed="state.cashType === 'in'" @click="state.cashType = 'in'">
                    {{ till.t('cash-movement.in') }}
                </button>

                <button type="button" :class="toggle" :aria-pressed="state.cashType === 'out'" @click="state.cashType = 'out'">
                    {{ till.t('cash-movement.out') }}
                </button>
            </div>

            <input v-model="state.cashAmount" type="number" step="0.01" :placeholder="till.t('cash-movement.amount')" :class="field">

            <input v-model="state.cashReason" type="text" :placeholder="till.t('cash-movement.reason')" :class="field">

            <p v-if="state.cashError" class="text-sm text-danger-600 dark:text-danger-400">{{ state.cashError }}</p>
        </div>

        <template #footer>
            <button
                type="button"
                class="flex min-h-11 items-center justify-center rounded-lg bg-primary-600 px-6 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-60"
                :disabled="state.cashBusy"
                @click="till.submitCash()"
            >
                {{ till.t('cash-movement.confirm') }}
            </button>
        </template>
    </TillModal>
</template>
