<script setup>
import { inject, computed } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state

const perGuest = computed(() => {
    const count = Math.trunc(Number(state.guestsDraft) || 0)

    if (count < 1 || !till.activeOrder) {
        return null
    }

    return till.money(till.orderTotals(till.activeOrder).total / count)
})

const stepper = 'flex size-14 flex-none items-center justify-center rounded-lg border border-gray-200 bg-white text-2xl font-semibold text-gray-700 transition-colors hover:bg-gray-50 active:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800'
</script>

<template>
    <TillModal
        :heading="till.t('guests.heading')"
        :open="state.guestsModalOpen"
        width="max-w-sm"
        content-class="pb-0!"
        header-class="pb-0!"
        @close="till.closeGuests()"
    >
        <div class="flex items-center gap-3">
            <button type="button" :class="stepper" :aria-label="till.t('guests.decrease')" @click="till.adjustGuests(-1)">
                &minus;
            </button>

            <input
                v-model.number="state.guestsDraft"
                type="number"
                min="0"
                step="1"
                inputmode="numeric"
                class="block min-w-0 flex-1 rounded-lg border border-gray-200 bg-white px-3 py-3 text-center font-mono text-2xl font-semibold tabular-nums text-gray-950 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                @keydown.enter.prevent="till.confirmGuests()"
            >

            <button type="button" :class="stepper" :aria-label="till.t('guests.increase')" @click="till.adjustGuests(1)">
                +
            </button>
        </div>

        <p v-if="perGuest" class="mt-3 text-center text-sm text-gray-500 dark:text-gray-400">
            {{ till.t('guests.per-guest', { amount: perGuest }) }}
        </p>

        <template #footer>
            <button
                type="button"
                class="flex min-h-11 items-center justify-center rounded-lg bg-primary-600 px-6 text-sm font-semibold text-white hover:bg-primary-700"
                @click="till.confirmGuests()"
            >
                {{ till.t('common.confirm') }}
            </button>
        </template>
    </TillModal>
</template>
