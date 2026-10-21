<script setup>
import { inject } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state

function reviewOrders() {
    state.closingDrafts = false
    state.closingOpen = false

    till.openHistory('draft')
}
</script>

<template>
    <TillModal
        :open="state.closingDrafts"
        width="max-w-md"
        :heading="till.t('draft-orders.heading')"
        @close="state.closingDrafts = false"
    >
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ till.t('draft-orders.message') }}
        </p>

        <template #footer>
            <button
                type="button"
                class="flex min-h-11 items-center justify-center rounded-lg bg-primary-600 px-5 text-sm font-semibold text-white hover:bg-primary-700"
                @click="reviewOrders()"
            >
                {{ till.t('draft-orders.review-orders') }}
            </button>

            <button
                type="button"
                class="flex min-h-11 items-center justify-center rounded-lg bg-danger-600 px-5 text-sm font-semibold text-white hover:bg-danger-700 disabled:opacity-60"
                :disabled="state.closingBusy"
                @click="till.confirmCancelDraftOrders()"
            >
                {{ till.t('draft-orders.cancel-orders') }}
            </button>
        </template>
    </TillModal>
</template>
