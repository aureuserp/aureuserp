<script setup>
import { inject, computed, ref, onMounted } from 'vue'

const till = inject('till')

const queue = till.queue.state

const slot = ref(null)

const pending = computed(() => queue.pending.filter((entry) => !entry.rejected).length)

const failures = computed(() => queue.pending.filter((entry) => entry.rejected))

const showFailures = ref(false)

async function retryFailures() {
    showFailures.value = false

    await till.queue.retryRejected()
}

const badge = 'flex size-9 flex-none items-center justify-center rounded-lg'

onMounted(() => {
    slot.value = document.getElementById('pos-status-slot')
})
</script>

<template>
    <Teleport v-if="slot" :to="slot">
        <span
            v-if="queue.offline"
            :class="`${badge} text-danger-600 dark:text-danger-400`"
            :title="till.t('offline.banner')"
            :aria-label="till.t('offline.banner')"
        >
            <svg
                class="size-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                aria-hidden="true"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 8.735 8.735m0 0a.374.374 0 1 1 .53.53m-.53-.53.53.53m0 0L21 21M14.652 9.348a3.75 3.75 0 0 1 0 5.304m2.121-7.425a6.75 6.75 0 0 1 0 9.546m2.121-11.667c3.808 3.807 3.808 9.98 0 13.788m-9.546-4.242a3.733 3.733 0 0 1-1.06-2.122m-1.061 4.243a6.75 6.75 0 0 1-1.625-6.929m-.496 9.05c-3.068-3.067-3.664-7.67-1.79-11.334M12 12h.008v.008H12V12Z" />
            </svg>
        </span>

        <span
            v-if="pending > 0"
            :class="`${badge} relative text-info-600 dark:text-info-400`"
            :title="till.t('offline.waiting', { count: pending })"
            :aria-label="till.t('offline.waiting', { count: pending })"
        >
            <svg
                class="size-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                aria-hidden="true"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>

            <span class="absolute -end-0.5 -top-0.5 flex min-w-4 justify-center rounded-full bg-info-600 px-1 text-[0.625rem] font-semibold leading-4 text-white">
                {{ pending }}
            </span>
        </span>

        <div v-if="failures.length" class="relative">
            <button
                type="button"
                :class="`${badge} relative text-danger-600 hover:bg-danger-50 dark:text-danger-400 dark:hover:bg-danger-500/10`"
                :title="till.t('offline.rejected', { count: failures.length })"
                :aria-label="till.t('offline.rejected', { count: failures.length })"
                :aria-expanded="showFailures"
                @click="showFailures = !showFailures"
            >
                <svg
                    class="size-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    aria-hidden="true"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>

                <span class="absolute -end-0.5 -top-0.5 flex min-w-4 justify-center rounded-full bg-danger-600 px-1 text-[0.625rem] font-semibold leading-4 text-white">
                    {{ failures.length }}
                </span>
            </button>

            <div
                v-if="showFailures"
                class="absolute end-0 top-full z-50 mt-2 flex w-80 max-w-[calc(100vw-2rem)] flex-col gap-2 rounded-xl border border-gray-200 bg-white p-3 shadow-lg dark:border-gray-700 dark:bg-gray-900"
            >
                <p class="text-sm font-semibold text-danger-600 dark:text-danger-400">
                    {{ till.t('offline.rejected', { count: failures.length }) }}
                </p>

                <ul class="flex max-h-64 flex-col gap-2 overflow-y-auto">
                    <li
                        v-for="failure in failures"
                        :key="failure.uuid"
                        class="rounded-lg bg-gray-50 px-2.5 py-2 text-xs dark:bg-white/5"
                    >
                        <span class="block font-mono font-semibold text-gray-950 dark:text-white">
                            {{ failure.payload?.reference ?? failure.uuid }}
                        </span>

                        <span class="block text-gray-600 dark:text-gray-300">
                            {{ failure.lastError }}
                        </span>

                        <button
                            v-if="till.queue.isSettledElsewhere(failure)"
                            type="button"
                            class="mt-1.5 font-semibold text-danger-600 hover:underline dark:text-danger-400"
                            @click="till.dismissRejection(failure.uuid)"
                        >
                            {{ till.t('rejected.dismiss') }}
                        </button>
                    </li>
                </ul>

                <button
                    v-if="failures.some((failure) => !till.queue.isSettledElsewhere(failure))"
                    type="button"
                    class="flex min-h-9 items-center justify-center rounded-lg bg-primary-600 px-3 text-sm font-semibold text-white hover:bg-primary-700"
                    @click="retryFailures"
                >
                    {{ till.t('offline.retry') }}
                </button>
            </div>
        </div>

        <span
            v-if="till.state.databaseUnavailable"
            :class="`${badge} text-danger-600 dark:text-danger-400`"
            :title="till.t('offline.storage-unavailable')"
            :aria-label="till.t('offline.storage-unavailable')"
        >
            <svg
                class="size-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                aria-hidden="true"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75" />
            </svg>
        </span>
    </Teleport>
</template>
