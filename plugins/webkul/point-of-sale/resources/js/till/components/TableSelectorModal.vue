<script setup>
import { inject } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state

const keys = ['1', '2', '3', '4', '5', '6', '7', '8', '9', 'clear', '0', 'Backspace']

const key = 'flex min-h-12 items-center justify-center rounded-lg border border-gray-200 bg-white text-lg font-medium text-gray-950 transition-colors hover:bg-gray-50 active:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800'

function press(value) {
    const draft = String(state.tableSelectorDraft ?? '')

    if (value === 'clear') {
        state.tableSelectorDraft = ''

        return
    }

    if (value === 'Backspace') {
        state.tableSelectorDraft = draft.slice(0, -1)

        return
    }

    state.tableSelectorDraft = `${draft}${value}`.slice(0, 64)
}
</script>

<template>
    <TillModal
        :heading="till.t('table-selector.heading')"
        :description="till.t('table-selector.hint')"
        :open="state.tableSelectorOpen"
        width="max-w-sm"
        content-class=" pb-0!"
        @close="till.closeTableSelector()"
    >
        <div class="flex flex-col gap-3">
            <input
                v-model="state.tableSelectorDraft"
                type="text"
                maxlength="64"
                :placeholder="till.t('table-selector.placeholder')"
                class="block w-full rounded-lg border border-gray-200 bg-white px-3 py-3 text-center font-mono text-2xl font-semibold text-gray-950 placeholder:text-base placeholder:font-normal placeholder:text-gray-400 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                @keydown.enter.prevent="till.confirmTableSelector()"
            >

            <div class="grid grid-cols-3 gap-2">
                <button
                    v-for="entry in keys"
                    :key="entry"
                    type="button"
                    :class="key"
                    :aria-label="entry === 'Backspace' ? till.t('numpad.backspace') : (entry === 'clear' ? till.t('common.clear') : entry)"
                    @click="press(entry)"
                >
                    <template v-if="entry === 'Backspace'">&#9003;</template>
                    <template v-else-if="entry === 'clear'">{{ till.t('numpad.clear') }}</template>
                    <template v-else>{{ entry }}</template>
                </button>
            </div>
        </div>

        <template #footer>
            <button
                type="button"
                class="flex min-h-11 items-center justify-center rounded-lg bg-primary-600 px-6 text-sm font-semibold text-white hover:bg-primary-700 disabled:bg-gray-200 disabled:text-gray-400 dark:disabled:bg-gray-800"
                :disabled="!String(state.tableSelectorDraft ?? '').trim()"
                @click="till.confirmTableSelector()"
            >
                {{ till.t('table-selector.jump') }}
            </button>
        </template>
    </TillModal>
</template>
