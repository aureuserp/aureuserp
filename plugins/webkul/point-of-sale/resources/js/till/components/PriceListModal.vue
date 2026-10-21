<script setup>
import { inject } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state
</script>

<template>
    <TillModal :heading="till.t('price-lists.heading')" :open="state.priceListModalOpen" width="max-w-xl" @close="till.closePriceLists()">

        <div class="flex min-h-0 flex-auto flex-col gap-2">
            <button
                v-for="priceList in till.priceLists"
                :key="priceList.id"
                type="button"
                class="flex items-center justify-between gap-3 rounded-lg border px-4 py-3 text-start text-sm transition-colors"
                :class="state.priceListId === priceList.id
                    ? 'border-primary-500 bg-primary-50 font-semibold text-primary-800 dark:border-primary-400 dark:bg-primary-500/15 dark:text-primary-200'
                    : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50 active:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800 dark:active:bg-gray-700'"
                @click="till.selectPriceList(priceList.id)"
            >
                <span class="truncate">{{ priceList.name }}</span>

                <svg
                    v-if="state.priceListId === priceList.id"
                    class="size-5 flex-none"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </TillModal>
</template>
