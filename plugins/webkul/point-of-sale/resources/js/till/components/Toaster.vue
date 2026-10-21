<script setup>
import { inject, computed } from 'vue'

const till = inject('till')

const toasts = computed(() => till.state.toasts)

const tones = {
    danger: 'border-danger-200 bg-danger-50 text-danger-800 dark:border-danger-500/30 dark:bg-danger-500/10 dark:text-danger-200',
    warning: 'border-warning-200 bg-warning-50 text-warning-800 dark:border-warning-500/30 dark:bg-warning-500/10 dark:text-warning-200',
    success: 'border-success-200 bg-success-50 text-success-800 dark:border-success-500/30 dark:bg-success-500/10 dark:text-success-200',
    info: 'border-info-200 bg-info-50 text-info-800 dark:border-info-500/30 dark:bg-info-500/10 dark:text-info-200',
}
</script>

<template>
    <div class="pointer-events-none fixed right-4 top-4 z-50 flex flex-col items-end gap-2">
        <TransitionGroup
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-x-2 opacity-0"
            enter-to-class="translate-x-0 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="translate-x-0 opacity-100"
            leave-to-class="translate-x-2 opacity-0"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                role="alert"
                class="pointer-events-auto flex w-full max-w-sm items-start gap-3 rounded-xl border p-3 shadow-lg"
                :class="tones[toast.status] ?? tones.danger"
                @click="till.dismissToast(toast.id)"
            >
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold">{{ toast.title }}</p>

                    <p v-if="toast.body" class="mt-0.5 text-sm opacity-90">{{ toast.body }}</p>
                </div>

                <button
                    type="button"
                    class="-m-1 flex-none rounded-lg p-1 opacity-70 transition-opacity hover:opacity-100"
                    :aria-label="till.t('common.close')"
                    @click.stop="till.dismissToast(toast.id)"
                >
                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                    </svg>
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>
