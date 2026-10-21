<script setup>
import { watch, onBeforeUnmount, useSlots, inject, ref, nextTick } from 'vue'

const props = defineProps({
    open: { type: Boolean, default: false },
    width: { type: String, default: 'max-w-xl' },
    heading: { type: String, default: null },
    description: { type: String, default: null },
    contentClass: { type: String, default: null },
    headerClass: { type: String, default: null },
})

const emit = defineEmits(['close'])

const slots = useSlots()

const till = inject('till')

const content = ref(null)

async function focusFirstField() {
    await nextTick()

    const field = content.value?.querySelector('input:not([type=hidden]):not([disabled]), textarea:not([disabled]), select:not([disabled])')

    field?.focus()

    if (field && typeof field.select === 'function' && field.type !== 'file') {
        field.select()
    }
}

function onKeydown(event) {
    if (event.key === 'Escape') {
        emit('close')
    }
}

watch(() => props.open, (open) => {
    if (open) {
        window.addEventListener('keydown', onKeydown)

        focusFirstField()
    } else {
        window.removeEventListener('keydown', onKeydown)
    }
})

onBeforeUnmount(() => window.removeEventListener('keydown', onKeydown))
</script>

<template>
    <Teleport to="body">
        <div v-if="open" class="fi-modal fi-absolute-positioning-context fi-modal-open">
            <Transition
                appear
                enter-active-class="transition-opacity duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-opacity duration-300"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="open" aria-hidden="true" class="fi-modal-close-overlay"></div>
            </Transition>

            <div class="fi-modal-window-ctn fi-clickable" @click.self="emit('close')">
                <Transition
                    appear
                    enter-active-class="fi-transition-enter"
                    enter-from-class="fi-transition-enter-start"
                    enter-to-class="fi-transition-enter-end"
                    leave-active-class="fi-transition-leave"
                    leave-from-class="fi-transition-leave-start"
                    leave-to-class="fi-transition-leave-end"
                >
                    <div
                        v-if="open"
                        class="fi-modal-window fi-modal-window-has-close-btn max-h-[80vh]"
                        :class="[width, slots.footer ? 'fi-modal-window-has-footer' : '']"
                        role="dialog"
                        aria-modal="true"
                    >
                        <button
                            type="button"
                            class="fi-icon-btn fi-modal-close-btn"
                            :aria-label="till.t('common.close')"
                            @click="emit('close')"
                        >
                            <svg
                                class="fi-icon fi-size-lg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                aria-hidden="true"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <div v-if="heading || slots.header" class="fi-modal-header pt-3 px-6 pb-0" :class="headerClass">
                            <slot name="header">
                                <div>
                                    <h2 class="fi-modal-heading pt-2">{{ heading }}</h2>

                                    <p v-if="description" class="fi-modal-description">{{ description }}</p>
                                </div>
                            </slot>
                        </div>

                        <div ref="content" class="fi-modal-content min-h-0 !pt-4 gap-2" :class="contentClass">
                            <slot />
                        </div>

                        <div v-if="slots.footer" class="fi-modal-footer">
                            <div class="flex w-full items-center gap-3">
                                <slot name="footer" />
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </Teleport>
</template>
