<script setup>
import { inject } from 'vue'

defineProps({
    canvas: { type: Object, required: true },
})

const till = inject('till')

const button = 'flex size-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-base font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800'
</script>

<template>
    <div class="flex flex-none items-center gap-1">
        <button type="button" :class="button" :aria-label="till.t('floor-plan.zoom-out')" @click="canvas.zoomOut()">&minus;</button>

        <button
            type="button"
            class="flex h-9 min-w-16 items-center justify-center rounded-lg border border-gray-200 bg-white px-2 font-mono text-xs text-gray-700 tabular-nums transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
            :title="till.t('floor-plan.fit')"
            @click="canvas.fit()"
        >
            {{ Math.round(canvas.scale.value * 100) }}%
        </button>

        <button type="button" :class="button" :aria-label="till.t('floor-plan.zoom-in')" @click="canvas.zoomIn()">+</button>
    </div>
</template>
