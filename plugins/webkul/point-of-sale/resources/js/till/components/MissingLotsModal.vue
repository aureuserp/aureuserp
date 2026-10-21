<script setup>
import { inject, computed } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state

const missing = computed(() => till.linesMissingLots())

function proceed() {
    till.dismissLotWarning()

    till.goToPayment({ ignoreLots: true })
}
</script>

<template>
    <TillModal :heading="till.t('lots.warning.heading')" :open="state.lotWarningOpen" width="max-w-lg" @close="till.dismissLotWarning()">

        <div class="flex flex-col gap-3 p-4">
            <p class="whitespace-pre-line text-sm text-gray-700 dark:text-gray-300">
                {{ till.t('lots.warning.body') }}
            </p>

            <ul class="flex flex-col gap-1">
                <li
                    v-for="line in missing"
                    :key="line.uuid"
                    class="truncate rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-700 dark:bg-gray-800 dark:text-gray-200"
                >
                    {{ till.master.products.get(line.product_id)?.name }}
                </li>
            </ul>
        </div>

        <template #footer>
            <button
                type="button"
                class="flex min-h-11 flex-1 items-center justify-center rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 active:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:active:bg-gray-700"
                @click="till.dismissLotWarning()"
            >
                {{ till.t('common.cancel') }}
            </button>

            <button
                type="button"
                class="flex min-h-11 flex-1 items-center justify-center rounded-lg bg-primary-600 text-sm font-semibold text-white hover:bg-primary-700"
                @click="proceed()"
            >
                {{ till.t('lots.warning.proceed') }}
            </button>
        </template>
    </TillModal>
</template>
