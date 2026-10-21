<script setup>
import { inject, computed } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const alert = computed(() => till.rejectionAlert)

const label = computed(() => (alert.value?.table
    ? till.t('rejected.table', { table: alert.value.table })
    : till.t('rejected.order', { order: alert.value?.order ?? '' })))
</script>

<template>
    <TillModal
        :heading="alert?.settledElsewhere ? till.t('rejected.settled-heading') : till.t('rejected.failed-heading')"
        :open="Boolean(alert)"
        width="max-w-md"
        content-class="pt-0!"
        @close="till.acknowledgeRejection(alert.uuid)"
    >
        <div v-if="alert" class="flex flex-col gap-3 text-sm text-gray-700 dark:text-gray-200">
            <p v-if="alert.settledElsewhere">
                {{ till.t('rejected.settled-body', { order: label }) }}
            </p>

            <p v-else>
                {{ till.t('rejected.failed-body', { order: label }) }}
            </p>

            <div
                v-if="alert.settledElsewhere"
                class="rounded-lg border border-danger-200 bg-danger-50 px-4 py-3 text-danger-800 dark:border-danger-500/40 dark:bg-danger-500/10 dark:text-danger-200"
            >
                <p class="text-base font-semibold">
                    {{ till.t('rejected.give-back', { amount: alert.amount }) }}
                </p>

                <p v-if="alert.methods" class="text-sm">
                    {{ alert.methods }}
                </p>
            </div>

            <p v-else class="rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-600 dark:bg-white/5 dark:text-gray-300">
                {{ alert.message }}
            </p>
        </div>

        <template #footer>
            <div v-if="alert" class="flex w-full flex-wrap justify-end gap-2">
                <button
                    v-if="!alert.settledElsewhere"
                    type="button"
                    class="flex min-h-11 items-center justify-center rounded-lg border border-gray-200 bg-white px-5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
                    @click="till.acknowledgeRejection(alert.uuid)"
                >
                    {{ till.t('common.close') }}
                </button>

                <button
                    v-if="!alert.settledElsewhere"
                    type="button"
                    class="flex min-h-11 items-center justify-center rounded-lg bg-primary-600 px-6 text-sm font-semibold text-white hover:bg-primary-700"
                    @click="till.retryRejection(alert.uuid)"
                >
                    {{ till.t('offline.retry') }}
                </button>

                <button
                    v-else
                    type="button"
                    class="flex min-h-11 items-center justify-center rounded-lg bg-danger-600 px-6 text-sm font-semibold text-white hover:bg-danger-700"
                    @click="till.dismissRejection(alert.uuid)"
                >
                    {{ till.t('rejected.returned') }}
                </button>
            </div>
        </template>
    </TillModal>
</template>
