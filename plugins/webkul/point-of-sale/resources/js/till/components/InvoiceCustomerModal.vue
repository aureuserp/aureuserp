<script setup>
import { inject, computed } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state

const results = computed(() => till.searchPartners(state.customerSearch))
</script>

<template>
    <TillModal
        :open="state.invoiceCustomerStep === 'confirm'"
        width="max-w-md"
        :heading="till.t('history.invoice-customer.heading')"
        @close="till.cancelInvoiceCustomer()"
    >
        <p class="text-sm text-gray-600 dark:text-gray-300">
            {{ till.t('history.invoice-customer.question') }}
        </p>

        <div class="flex justify-end gap-2">
            <button
                type="button"
                class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                @click="till.cancelInvoiceCustomer()"
            >
                {{ till.t('common.cancel') }}
            </button>

            <button
                type="button"
                class="rounded-lg bg-primary-600 px-5 py-2 text-sm font-semibold text-white hover:bg-primary-700"
                @click="till.confirmInvoiceCustomer()"
            >
                {{ till.t('common.ok') }}
            </button>
        </div>
    </TillModal>

    <TillModal
        :open="state.invoiceCustomerStep === 'select'"
        width="max-w-xl"
        :heading="till.t('history.invoice-customer.select-heading')"
        @close="till.cancelInvoiceCustomer()"
    >
        <div class="flex flex-none items-center gap-2">
            <input
                v-model="state.customerSearch"
                type="search"
                :placeholder="till.t('customers.search')"
                class="min-w-0 flex-1 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-950 placeholder:text-gray-400 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
            >
        </div>

        <div class="min-h-0 flex-auto overflow-y-auto">
            <button
                v-for="partner in results"
                :key="partner.id"
                type="button"
                class="flex w-full items-start justify-between gap-3 border-b border-gray-100 px-4 py-2.5 text-start hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800"
                @click="till.chooseInvoiceCustomer(partner.id)"
            >
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-sm font-semibold text-gray-950 dark:text-white">{{ partner.name }}</span>

                    <span class="block truncate text-xs text-gray-500 dark:text-gray-400">
                        {{ [partner.street1, partner.city, partner.zip].filter(Boolean).join(', ') }}
                    </span>
                </span>

                <span class="shrink-0 text-xs text-gray-500 dark:text-gray-400">
                    {{ partner.email ?? partner.phone }}
                </span>
            </button>

            <p v-if="!results.length" class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                {{ till.t('customers.no-match') }}
            </p>
        </div>
    </TillModal>
</template>
