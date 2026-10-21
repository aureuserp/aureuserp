<script setup>
import { inject } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state
</script>

<template>
    <TillModal
        :heading="till.t('order-name.heading')"
        :open="state.orderNameModalOpen"
        width="max-w-md"
        content-class=" pb-0!"
        header-class="pb-0!"
        @close="till.closeOrderName()"
    >
        <input
            v-model="state.orderNameDraft"
            type="text"
            maxlength="64"
            :placeholder="till.t('order-name.placeholder')"
            class="block w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-base text-gray-950 placeholder:text-gray-400 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
            @keydown.enter.prevent="till.confirmOrderName()"
        >

        <template #footer>
            <button
                type="button"
                class="flex min-h-11 items-center justify-center rounded-lg bg-primary-600 px-6 text-sm font-semibold text-white hover:bg-primary-700"
                @click="till.confirmOrderName()"
            >
                {{ till.t('common.save') }}
            </button>
        </template>
    </TillModal>
</template>
