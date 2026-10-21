<script setup>
import { inject, computed, watch } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state

const queue = till.queue.state

const open = computed(() => queue.offline && ! state.offlineAcknowledged)

watch(() => queue.offline, (offline) => {
    if (! offline) {
        state.offlineAcknowledged = false
    }
})

function acknowledge() {
    state.offlineAcknowledged = true
}
</script>

<template>
    <TillModal
        :heading="till.t('offline.lost-heading')"
        :open="open"
        width="max-w-sm"
        @close="acknowledge"
    >
        <div class="flex flex-col items-center gap-4 text-center">
            <span class="flex size-14 items-center justify-center rounded-full bg-danger-100 text-danger-600 dark:bg-danger-500/20 dark:text-danger-400">
                <svg
                    class="size-7"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    aria-hidden="true"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 8.735 8.735m0 0a.374.374 0 1 1 .53.53m-.53-.53.53.53m0 0L21 21M14.652 9.348a3.75 3.75 0 0 1 0 5.304m2.121-7.425a6.75 6.75 0 0 1 0 9.546m2.121-11.667c3.808 3.807 3.808 9.98 0 13.788m-9.546-4.242a3.733 3.733 0 0 1-1.06-2.122m-1.061 4.243a6.75 6.75 0 0 1-1.625-6.929m-.496 9.05c-3.068-3.067-3.664-7.67-1.79-11.334M12 12h.008v.008H12V12Z" />
                </svg>
            </span>

            <p class="text-sm text-gray-600 dark:text-gray-300">
                {{ till.t('offline.lost-body') }}
            </p>
        </div>

        <template #footer>
            <button
                type="button"
                class="flex min-h-11 w-full items-center justify-center rounded-lg bg-primary-600 px-6 text-sm font-semibold text-white hover:bg-primary-700"
                @click="acknowledge"
            >
                {{ till.t('offline.continue') }}
            </button>
        </template>
    </TillModal>
</template>
