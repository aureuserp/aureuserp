<script setup>
import { inject, computed } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state

const heading = computed(() => till.t(till.tipAmount() ? 'tip.change' : 'tip.add'))
</script>

<template>
    <TillModal
        :heading="heading"
        :open="state.tipModalOpen"
        width="max-w-sm"
        content-class="pb-0!"
        header-class="pb-0!"
        @close="till.closeTip()"
    >
        <div class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 focus-within:border-primary-500 dark:border-gray-700 dark:bg-gray-900">
            <span class="flex-none text-lg text-gray-500 dark:text-gray-400">{{ till.currency.symbol }}</span>

            <input
                v-model="state.tipDraft"
                type="number"
                min="0"
                step="any"
                inputmode="decimal"
                class="block min-w-0 flex-1 border-0 bg-transparent py-3 text-end font-mono text-2xl font-semibold tabular-nums text-gray-950 focus:outline-none focus:ring-0 dark:text-white"
                @keydown.enter.prevent="till.confirmTip()"
            >
        </div>

        <template #footer>
            <button
                type="button"
                class="flex min-h-11 items-center justify-center rounded-lg border border-gray-200 bg-white px-4 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
                @click="state.tipDraft = ''; till.confirmTip()"
            >
                {{ till.t('tip.remove') }}
            </button>

            <button
                type="button"
                class="flex min-h-11 items-center justify-center rounded-lg bg-primary-600 px-6 text-sm font-semibold text-white hover:bg-primary-700"
                @click="till.confirmTip()"
            >
                {{ till.t('common.confirm') }}
            </button>
        </template>
    </TillModal>
</template>
