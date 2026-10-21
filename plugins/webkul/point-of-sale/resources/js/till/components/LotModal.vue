<script setup>
import { inject, computed } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state

const line = computed(() => till.lotLine)

const product = computed(() => till.lotProduct)

const singleItem = computed(() => line.value && till.allowsOnlyOneLot(line.value.product_id))

const options = computed(() => (line.value ? till.existingLotsFor(line.value.product_id) : []))
</script>

<template>
    <TillModal :heading="till.t('lots.heading')" :description="product?.name" :open="state.lotLineUuid !== null" width="max-w-lg" @close="till.closeLots()">

        <div class="flex min-h-0 flex-auto flex-col gap-2">
            <p
                v-if="state.lotError"
                class="rounded-lg bg-danger-50 px-3 py-2 text-sm text-danger-700 dark:bg-danger-500/10 dark:text-danger-300"
            >
                {{ state.lotError }}
            </p>

            <datalist id="pos-lot-options">
                <option v-for="lot in options" :key="lot.id" :value="lot.name" />
            </datalist>

            <div v-for="(row, index) in state.lotRows" :key="index" class="flex items-center gap-2">
                <input
                    :value="row"
                    type="text"
                    list="pos-lot-options"
                    class="min-h-11 min-w-0 flex-1 rounded-lg border border-gray-200 bg-white px-3 font-mono text-sm text-gray-950 placeholder:font-sans placeholder:text-gray-400 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    :placeholder="till.t('lots.placeholder')"
                    @input="till.setLotRow(index, $event.target.value)"
                >

                <button
                    v-if="!singleItem"
                    type="button"
                    class="flex size-11 flex-none items-center justify-center rounded-lg text-gray-400 hover:bg-danger-50 hover:text-danger-600 dark:hover:bg-danger-500/10 dark:hover:text-danger-400"
                    :aria-label="till.t('lots.remove')"
                    @click="till.removeLotRow(index)"
                >
                    <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                    </svg>
                </button>
            </div>

            <button
                v-if="!singleItem"
                type="button"
                class="flex min-h-11 items-center justify-center gap-1 self-start rounded-lg border border-dashed border-gray-300 px-3 text-sm text-gray-600 hover:bg-gray-50 active:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800 dark:active:bg-gray-700"
                @click="till.addLotRow()"
            >
                + {{ till.t('lots.add') }}
            </button>
        </div>

        <template #footer>
            <button
                type="button"
                class="flex min-h-11 flex-1 items-center justify-center rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 active:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:active:bg-gray-700"
                @click="till.closeLots()"
            >
                {{ till.t('common.discard') }}
            </button>

            <button
                type="button"
                class="flex min-h-11 flex-2 items-center justify-center rounded-lg bg-primary-600 text-sm font-semibold text-white hover:bg-primary-700"
                @click="till.confirmLots()"
            >
                {{ till.t('lots.warning.proceed') }}
            </button>
        </template>
    </TillModal>
</template>
