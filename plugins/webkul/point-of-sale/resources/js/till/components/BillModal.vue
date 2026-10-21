<script setup>
import { inject } from 'vue'
import TillModal from './TillModal.vue'
import ReceiptBody from './ReceiptBody.vue'

const till = inject('till')

const state = till.state

function print() {
    window.pointOfSaleReceipt.print('.pos-receipt')
}
</script>

<template>
    <TillModal
        :heading="till.t('bill.heading')"
        :open="state.billModalOpen"
        width="max-w-md"
        @close="till.closeBill()"
    >
        <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
            <ReceiptBody :order="till.activeOrder" bill />
        </div>

        <template #footer>
            <button
                type="button"
                class="flex min-h-11 items-center justify-center rounded-lg bg-primary-600 px-6 text-sm font-semibold text-white hover:bg-primary-700"
                @click="print()"
            >
                {{ till.t('common.print') }}
            </button>
        </template>
    </TillModal>
</template>
