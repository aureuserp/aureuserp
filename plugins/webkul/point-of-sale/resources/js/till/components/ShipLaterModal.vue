<script setup>
import { inject } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state
</script>

<template>
    <TillModal
        :heading="till.t('payment.ship-later-heading')"
        :open="state.shipLaterModalOpen"
        width="max-w-md"
        content-class="pb-0!"
        header-class="pb-0!"
        @close="till.closeShipLater()"
    >
        <input
            v-model="state.shipLaterDraft"
            type="date"
            :min="till.today"
            class="block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-base text-gray-950 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
        >

        <template #footer>
            <button
                type="button"
                class="flex min-h-11 items-center justify-center rounded-lg bg-primary-600 px-6 text-sm font-semibold text-white hover:bg-primary-700"
                @click="till.confirmShipLater()"
            >
                {{ till.t('common.confirm') }}
            </button>
        </template>
    </TillModal>
</template>
