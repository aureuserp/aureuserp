<script setup>
import { inject, computed, nextTick, onMounted } from 'vue'
import ReceiptBody from './ReceiptBody.vue'

const till = inject('till')

const order = computed(() => till.activeOrder)

function print() {
    window.pointOfSaleReceipt.print('.pos-receipt')
}

onMounted(async () => {
    if (!order.value || till.state.autoPrintOrderUuid !== order.value.uuid) {
        return
    }

    till.state.autoPrintOrderUuid = null

    await nextTick()

    print()
})
</script>

<template>
    <div class="flex min-h-0 flex-col gap-3">
        <div class="min-h-0 flex-auto overflow-y-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
            <ReceiptBody :order="order" />
        </div>

        <div class="flex flex-none gap-2">
            <button
                v-if="till.printsReceipts"
                type="button"
                class="flex min-h-12 flex-1 items-center justify-center rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                @click="print()"
            >
                {{ till.t('common.print') }}
            </button>

            <button
                type="button"
                class="flex min-h-12 flex-2 items-center justify-center rounded-lg bg-primary-600 text-sm font-semibold text-white hover:bg-primary-700"
                @click="till.finishOrder()"
            >
                {{ till.t('receipt.new-order') }}
            </button>
        </div>
    </div>
</template>
