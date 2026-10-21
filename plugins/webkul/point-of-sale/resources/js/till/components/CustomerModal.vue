<script setup>
import { inject, computed, ref } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state

const results = computed(() => till.searchPartners(state.customerSearch))

const order = computed(() => till.activeOrder)

const creating = ref(false)

const draft = ref({ name: '', email: '', phone: '' })

function choose(partnerId) {
    till.selectCustomer(partnerId)

    till.closeCustomers()
}

function startCreating() {
    draft.value = { name: state.customerSearch.trim(), email: '', phone: '' }

    creating.value = true
}

function saveCustomer() {
    const partner = till.createCustomer(draft.value)

    if (!partner) {
        return
    }

    creating.value = false

    till.closeCustomers()
}
</script>

<template>
    <TillModal
        :open="state.customerModalOpen"
        width="max-w-xl"
        :heading="till.t('customers.heading')"
        @close="till.closeCustomers()"
    >
            <div class="flex flex-none items-center gap-2">
                <input
                    v-model="state.customerSearch"
                    type="search"
                    :placeholder="till.t('customers.search')"
                    class="min-w-0 flex-1 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-950 placeholder:text-gray-400 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                >

                <button
                    type="button"
                    class="flex-none rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white hover:bg-primary-700"
                    @click="startCreating()"
                >
                    {{ till.t('common.new') }}
                </button>
            </div>

            <div v-if="creating" class="flex flex-none flex-col gap-2 border-b border-gray-100 dark:border-gray-800">
                <input
                    v-model="draft.name"
                    type="text"
                    :placeholder="till.t('customers.create.fields.name')"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-950 placeholder:text-gray-400 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                >

                <input
                    v-model="draft.email"
                    type="email"
                    :placeholder="till.t('customers.create.fields.email')"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-950 placeholder:text-gray-400 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                >

                <input
                    v-model="draft.phone"
                    type="tel"
                    :placeholder="till.t('customers.create.fields.phone')"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-950 placeholder:text-gray-400 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                >

                <div class="flex gap-2">
                    <button
                        type="button"
                        class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 dark:border-gray-700 dark:text-gray-300"
                        @click="creating = false"
                    >
                        {{ till.t('common.cancel') }}
                    </button>

                    <button
                        type="button"
                        class="flex-2 rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white hover:bg-primary-700 disabled:bg-gray-200 dark:disabled:bg-gray-800 disabled:text-gray-400"
                        :disabled="!draft.name.trim()"
                        @click="saveCustomer()"
                    >
                        {{ till.t('common.save') }}
                    </button>
                </div>
            </div>

            <div class="min-h-0 flex-auto overflow-y-auto">
                <button
                    v-if="order?.partner_id && !till.isCustomerLocked"
                    type="button"
                    class="flex w-full items-center gap-2 border-b border-gray-100 px-4 py-2.5 text-start text-sm font-medium text-danger-600 hover:bg-danger-50 dark:border-gray-800 dark:text-danger-400 dark:hover:bg-danger-500/10"
                    @click="choose(null)"
                >
                    {{ till.t('customers.clear') }}
                </button>

                <button
                    v-for="partner in results"
                    :key="partner.id"
                    type="button"
                    class="flex w-full items-start justify-between gap-3 border-b border-gray-100 px-4 py-2.5 text-start hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800"
                    @click="choose(partner.id)"
                >
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-sm font-semibold text-gray-950 dark:text-white">
                            {{ partner.name }}
                            <span v-if="partner.is_local" class="ms-1 rounded bg-warning-100 px-1 py-0.5 text-[0.625rem] font-medium uppercase text-warning-700 dark:bg-warning-500/20 dark:text-warning-300">{{ till.t('customers.badge-new') }}</span>
                        </span>
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
