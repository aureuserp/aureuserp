<script setup>
import { inject } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state
</script>

<template>
    <TillModal
        :heading="till.t('payment.ship-later-error-heading')"
        :open="Boolean(state.shipLaterError)"
        width="max-w-md"
        content-class="pt-0!"
        @close="till.dismissShipLaterError()"
    >
        <p class="text-sm text-gray-700 dark:text-gray-200">
            {{ state.shipLaterError }}
        </p>

        <template #footer>
            <button
                type="button"
                class="flex min-h-11 items-center justify-center rounded-lg bg-primary-600 px-6 text-sm font-semibold text-white hover:bg-primary-700"
                @click="till.dismissShipLaterError()"
            >
                {{ till.t('common.ok') }}
            </button>
        </template>
    </TillModal>
</template>
