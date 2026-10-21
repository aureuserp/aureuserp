<script setup>
import { inject } from 'vue'

const till = inject('till')

const state = till.state
</script>

<template>
    <div
        v-if="state.openingOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 p-4"
        role="dialog"
        aria-modal="true"
    >
        <div class="flex w-full max-w-md flex-col gap-4 rounded-xl bg-white p-6 shadow-xl dark:bg-gray-900">
            <h2 class="text-lg font-semibold text-gray-950 dark:text-white">
                {{ till.t('opening-control.heading') }}
            </h2>

            <div v-if="till.hasCashCountMethod" class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-950 dark:text-white" for="opening-cash">
                    {{ till.t('opening-control.cash') }}
                </label>

                <p v-if="till.previousClosingBalance !== null" class="text-sm text-gray-500 dark:text-gray-400">
                    {{ till.t('opening-control.previous-closing-balance', { amount: till.money(till.previousClosingBalance) }) }}
                </p>

                <div class="flex items-center gap-2">
                    <input
                        id="opening-cash"
                        v-model="state.openingCash"
                        type="number"
                        step="0.01"
                        class="h-10 min-w-0 flex-1 rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-950 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    >

                    <button
                        type="button"
                        class="flex size-10 flex-none items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 transition-colors hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
                        :title="till.t('money-details.label')"
                        :aria-label="till.t('money-details.label')"
                        @click="till.openMoneyDetails('opening')"
                    >
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-950 dark:text-white" for="opening-note">
                    {{ till.t('opening-control.note') }}
                </label>

                <textarea
                    id="opening-note"
                    v-model="state.openingNotes"
                    rows="3"
                    :placeholder="till.t('opening-control.placeholder')"
                    class="block w-full rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-950 placeholder:text-gray-400 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500"
                ></textarea>
            </div>

            <p v-if="state.openingError" class="text-sm text-danger-600 dark:text-danger-400">
                {{ state.openingError }}
            </p>

            <div class="flex justify-end gap-2">
                <button
                    type="button"
                    class="flex min-h-11 items-center justify-center rounded-lg border border-gray-200 px-6 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-60 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800"
                    :disabled="state.openingBusy"
                    @click="till.cancelOpening()"
                >
                    {{ till.t('opening-control.cancel') }}
                </button>

                <button
                    type="button"
                    class="flex min-h-11 items-center justify-center rounded-lg bg-primary-600 px-6 text-sm font-semibold text-white hover:bg-primary-700 disabled:opacity-60"
                    :disabled="state.openingBusy"
                    @click="till.confirmOpening()"
                >
                    {{ till.t('opening-control.confirm') }}
                </button>
            </div>
        </div>
    </div>
</template>
