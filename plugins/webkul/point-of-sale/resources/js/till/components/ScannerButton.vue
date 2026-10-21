<script setup>
import { inject, ref, onMounted, onBeforeUnmount } from 'vue'

const till = inject('till')

const state = till.state

const slot = ref(null)

const video = ref(null)

let stream = null

let detector = null

let frame = null

onMounted(() => {
    slot.value = document.getElementById('pos-status-slot')
})

onBeforeUnmount(() => stop())

async function start() {
    state.cameraError = null

    if (!till.cameraScanSupported) {
        state.cameraScanning = true
        state.cameraError = till.t('scanner.unsupported')

        return
    }

    try {
        detector ??= new window.BarcodeDetector()

        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })

        state.cameraScanning = true

        await new Promise((resolve) => requestAnimationFrame(resolve))

        video.value.srcObject = stream

        await video.value.play()

        read()
    } catch (error) {
        state.cameraError = error.message

        stop()
    }
}

function stop() {
    window.cancelAnimationFrame(frame)

    stream?.getTracks().forEach((track) => track.stop())

    stream = null

    state.cameraScanning = false
}

async function read() {
    if (!state.cameraScanning || !video.value) {
        return
    }

    try {
        const [found] = await detector.detect(video.value)

        if (found?.rawValue && till.scan(found.rawValue)) {
            stop()

            return
        }
    } catch (error) {
        state.cameraError = error.message
    }

    frame = window.requestAnimationFrame(() => read())
}

function toggle() {
    state.cameraScanning ? stop() : start()
}
</script>

<template>
    <Teleport v-if="slot" :to="slot">
        <button
            type="button"
            class="flex h-9 flex-none items-center gap-1.5 rounded-lg px-2 text-gray-600 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5"
            :class="state.cameraScanning ? 'text-primary-600 dark:text-primary-400' : ''"
            :title="till.t(state.cameraScanning ? 'scanner.stop' : 'scanner.start')"
            :aria-label="till.t(state.cameraScanning ? 'scanner.stop' : 'scanner.start')"
            @click="toggle()"
        >
            <svg
                class="size-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                aria-hidden="true"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z" />
            </svg>

            <span v-if="state.cameraScanning" class="text-sm font-medium">{{ till.t('scanner.stop') }}</span>
        </button>
    </Teleport>

    <Teleport to="body">
        <div
            v-if="state.cameraScanning"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/70 p-4"
            @click.self="stop()"
        >
            <div class="flex w-full max-w-md flex-col gap-3 rounded-xl bg-white p-4 dark:bg-gray-900">
                <p class="text-base font-semibold text-gray-950 dark:text-white">{{ till.t('scanner.heading') }}</p>

                <video
                    v-if="till.cameraScanSupported"
                    ref="video"
                    class="w-full rounded-lg bg-gray-950"
                    muted
                    playsinline
                ></video>

                <p class="text-sm text-gray-600 dark:text-gray-300">{{ till.t('scanner.hardware-hint') }}</p>

                <p v-if="state.cameraError" class="text-sm text-danger-600 dark:text-danger-400">{{ state.cameraError }}</p>

                <button
                    type="button"
                    class="flex min-h-11 items-center justify-center rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 active:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:active:bg-gray-700"
                    @click="stop()"
                >
                    {{ till.t('scanner.stop') }}
                </button>
            </div>
        </div>
    </Teleport>
</template>
