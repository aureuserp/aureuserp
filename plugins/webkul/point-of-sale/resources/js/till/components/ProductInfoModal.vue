<script setup>
import { inject, computed } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state

const info = computed(() => (state.productInfoId ? till.productInfo() : null))

const reference = computed(() => [info.value?.reference, info.value?.barcode].filter(Boolean).join(' · ') || null)
</script>

<template>
    <TillModal
        :open="Boolean(info)"
        width="max-w-2xl"
        header-class="pb-0!"
        @close="till.closeProductInfo()"
    >
        <template #header>
            <span class="me-4 flex size-14 flex-none items-center justify-center overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800">
                <img v-if="info?.image" :src="info.image" :alt="info.name" class="h-full w-full object-cover">
            </span>

            <div>
                <h2 class="fi-modal-heading">{{ info?.name }}</h2>

                <p v-if="reference" class="fi-modal-description">{{ reference }}</p>
            </div>
        </template>

        <div v-if="info" class="flex min-h-0 flex-auto flex-col gap-5">
            <div v-if="info.is_storable" class="flex flex-col gap-1">
                <p class="text-sm font-semibold text-gray-950 dark:text-white">{{ till.t('product-info.inventory') }}</p>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <span
                        class="font-mono font-semibold tabular-nums"
                        :class="info.available <= 0 ? 'text-danger-600 dark:text-danger-400' : 'text-gray-950 dark:text-white'"
                    >{{ info.available.toFixed(2) }}</span>
                    {{ till.t('product-info.available') }}
                </p>

                <p v-if="info.available <= 0" class="text-xs text-gray-500 dark:text-gray-400">
                    {{ till.t('product-info.negative-warning') }}
                </p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="flex flex-col gap-2">
                    <p class="text-sm font-semibold text-gray-950 dark:text-white">{{ till.t('product-info.financials') }}</p>

                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">{{ till.t('product-info.price') }}</span>
                        <span class="font-mono tabular-nums text-gray-950 dark:text-white">{{ till.money(info.price) }}</span>
                    </div>

                    <template v-if="till.config.show_margins_and_costs">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">{{ till.t('product-info.cost') }}</span>
                            <span class="font-mono tabular-nums text-gray-950 dark:text-white">{{ till.money(info.cost) }}</span>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">{{ till.t('product-info.margin') }}</span>
                            <span class="font-mono tabular-nums text-gray-950 dark:text-white">
                                {{ till.money(info.margin) }} ({{ info.margin_ratio.toFixed(2) }}%)
                            </span>
                        </div>
                    </template>
                </div>

                <div class="flex flex-col gap-2">
                    <p class="text-sm font-semibold text-gray-950 dark:text-white">{{ till.t('product-info.order') }}</p>

                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">{{ till.t('product-info.quantity') }}</span>
                        <span class="font-mono tabular-nums text-gray-950 dark:text-white">{{ +info.quantity.toFixed(3) }}</span>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">{{ till.t('product-info.total-price') }}</span>
                        <span class="font-mono tabular-nums text-gray-950 dark:text-white">{{ till.money(info.total_price) }}</span>
                    </div>

                    <div v-if="till.config.show_margins_and_costs" class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">{{ till.t('product-info.total-margin') }}</span>
                        <span class="font-mono tabular-nums text-gray-950 dark:text-white">{{ till.money(info.total_margin) }}</span>
                    </div>
                </div>
            </div>

            <button
                type="button"
                class="flex min-h-12 items-center justify-center rounded-lg bg-primary-600 text-sm font-semibold text-white hover:bg-primary-700"
                @click="till.pickProduct(info.id); till.closeProductInfo()"
            >
                {{ till.t('product-info.add') }}
            </button>
        </div>
    </TillModal>
</template>
