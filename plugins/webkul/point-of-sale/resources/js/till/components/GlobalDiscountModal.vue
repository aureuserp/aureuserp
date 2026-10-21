<script setup>
import { inject } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state
</script>

<template>
    <TillModal
        :heading="till.t('global-discount.heading')"
        :description="till.t('global-discount.hint')"
        :open="state.globalDiscountModalOpen"
        width="max-w-sm"
        content-class="pb-0!"
        @close="till.closeGlobalDiscount()"
    >
        <div class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 focus-within:border-primary-500 dark:border-gray-700 dark:bg-gray-900">
            <input
                v-model="state.globalDiscountDraft"
                type="number"
                min="0"
                max="100"
                step="any"
                inputmode="decimal"
                class="block min-w-0 flex-1 border-0 bg-transparent py-3 text-end font-mono text-2xl font-semibold tabular-nums text-gray-950 focus:outline-none focus:ring-0 dark:text-white"
                @keydown.enter.prevent="till.confirmGlobalDiscount()"
            >

            <span class="flex-none text-lg text-gray-500 dark:text-gray-400">%</span>
        </div>

        <template #footer>
            <button
                type="button"
                class="flex min-h-11 items-center justify-center rounded-lg bg-primary-600 px-6 text-sm font-semibold text-white hover:bg-primary-700"
                @click="till.confirmGlobalDiscount()"
            >
                {{ till.t('global-discount.apply') }}
            </button>
        </template>
    </TillModal>
</template>
