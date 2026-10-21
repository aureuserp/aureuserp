<script setup>
import { inject, computed, ref } from 'vue'
import FloorZoom from './FloorZoom.vue'
import { useFloorCanvas } from './floor-canvas.js'

const till = inject('till')

const state = till.state

const draft = computed(() => state.planDraft)

const selected = computed(() => till.planSelectedTable)

const palette = [
    { value: '#ef4444', name: 'red' },
    { value: '#f97316', name: 'orange' },
    { value: '#eab308', name: 'yellow' },
    { value: '#22c55e', name: 'green' },
    { value: '#14b8a6', name: 'teal' },
    { value: '#3b82f6', name: 'blue' },
    { value: '#8b5cf6', name: 'violet' },
    { value: '#ec4899', name: 'pink' },
    { value: '#78716c', name: 'stone' },
]

const backgrounds = [
    { value: '#ffffff', name: 'white' },
    { value: '#f5f5f4', name: 'light-grey' },
    { value: '#fef3c7', name: 'cream' },
    { value: '#dcfce7', name: 'mint' },
    { value: '#dbeafe', name: 'sky' },
    { value: '#ede9fe', name: 'lavender' },
    { value: '#fce7f3', name: 'blush' },
    { value: '#e7e5e4', name: 'warm-grey' },
]

const scroller = ref(null)

const picker = ref(null)

const tables = computed(() => till.planTables)

const canvas = useFloorCanvas(scroller, draft, tables, { margin: 200 })

const backdrop = computed(() => (draft.value?.background_color ? { '--floor-bg': draft.value.background_color } : {}))

const backdropClass = computed(() => (draft.value?.background_color
    ? 'bg-(--floor-bg) dark:bg-[oklch(from_var(--floor-bg)_0.32_calc(c*2)_h)]'
    : 'bg-gray-50 dark:bg-gray-950'))

function pickImage(event) {
    const [file] = event.target.files ?? []

    event.target.value = ''

    if (file) {
        till.uploadFloorImage(file)
    }
}

const button = 'flex min-h-10 flex-none items-center justify-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800'

let gesture = null

function placement(table) {
    const style = {
        left: `${table.position_h}px`,
        top: `${table.position_v}px`,
        width: `${table.width}px`,
        height: `${table.height}px`,
    }

    if (table.color) {
        style.borderColor = table.color
    }

    return style
}

function disarmDelete(event) {
    if (!event.target.closest('[data-delete-floor]')) {
        state.planConfirmDelete = false
    }
}

function startMove(event, table) {
    if (event.button !== 0 || till.planLocked) {
        return
    }

    till.state.planSelectedKey = table.key

    canvas.hold()

    gesture = {
        mode: 'move',
        key: table.key,
        x: event.clientX,
        y: event.clientY,
        left: table.position_h,
        top: table.position_v,
        width: table.width,
        height: table.height,
    }

    event.currentTarget.setPointerCapture(event.pointerId)
}

function startResize(event, table) {
    event.stopPropagation()

    if (till.planLocked) {
        return
    }

    canvas.hold()

    gesture = {
        mode: 'resize',
        key: table.key,
        x: event.clientX,
        y: event.clientY,
        width: table.width,
        height: table.height,
    }

    event.currentTarget.setPointerCapture(event.pointerId)
}

function track(event) {
    if (!gesture) {
        return
    }

    const dx = (event.clientX - gesture.x) / canvas.scale.value
    const dy = (event.clientY - gesture.y) / canvas.scale.value

    if (gesture.mode === 'move') {
        till.movePlanTable(gesture.key, gesture.left + dx, gesture.top + dy)
    } else {
        till.resizePlanTable(gesture.key, gesture.width + dx, gesture.height + dy)
    }
}

function release() {
    gesture = null

    canvas.release()
}
</script>

<template>
    <div class="flex min-h-0 flex-col gap-3" @pointerdown.capture="disarmDelete">
        <div class="flex flex-none flex-wrap items-center gap-2 rounded-xl border border-gray-200 bg-white p-2 dark:border-gray-700 dark:bg-gray-900">
            <input
                v-model="draft.name"
                :disabled="state.planSaving"
                type="text"
                maxlength="255"
                :aria-label="till.t('floor-plan.floor-name')"
                :placeholder="till.t('floor-plan.floor-name')"
                class="min-h-10 w-44 rounded-lg border border-gray-200 bg-white px-3 text-sm text-gray-950 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
            >

            <div class="flex items-center gap-1" :title="till.t('floor-plan.background')">
                <button
                    v-for="colour in backgrounds"
                    :key="colour.value"
                    type="button"
                    class="size-7 rounded-md border-2 transition-transform hover:scale-110"
                    :class="draft.background_color === colour.value ? 'border-primary-600' : 'border-gray-200 dark:border-gray-700'"
                    :style="{ backgroundColor: colour.value }"
                    :title="till.t(`floor-plan.colours.${colour.name}`)"
                    :aria-label="till.t(`floor-plan.colours.${colour.name}`)"
                    :aria-pressed="draft.background_color === colour.value"
                    :disabled="state.planSaving"
                    @click="draft.background_color = colour.value"
                />

                <button
                    type="button"
                    class="flex size-7 items-center justify-center rounded-md border-2 border-dashed border-gray-300 text-xs text-gray-500 dark:border-gray-600 dark:text-gray-400"
                    :aria-label="till.t('floor-plan.no-colour')"
                    :disabled="state.planSaving"
                    @click="draft.background_color = ''"
                >
                    &times;
                </button>
            </div>

            <input ref="picker" type="file" accept="image/*" class="sr-only" tabindex="-1" @change="pickImage">

            <button type="button" :class="button" :disabled="state.planSaving" @click="picker?.click()">
                {{ draft.background_image ? till.t('floor-plan.change-image') : till.t('floor-plan.add-image') }}
            </button>

            <button
                v-if="draft.background_image"
                type="button"
                :class="button"
                :disabled="state.planSaving"
                @click="till.removeFloorImage()"
            >
                {{ till.t('floor-plan.remove-image') }}
            </button>

            <span class="mx-1 h-6 w-px bg-gray-200 dark:bg-gray-700"></span>

            <button type="button" :class="button" @click="till.addPlanTable()">
                {{ till.t('floor-plan.add-table') }}
            </button>

            <button
                v-if="till.config.floor_plan?.can_create"
                type="button"
                :class="button"
                :disabled="state.planSaving"
                @click="till.addFloor()"
            >
                {{ till.t('floor-plan.add-floor') }}
            </button>

            <button
                v-if="till.config.floor_plan?.can_delete"
                data-delete-floor
                type="button"
                class="flex min-h-10 flex-none items-center justify-center rounded-lg border px-3 text-sm font-medium transition-colors disabled:opacity-50"
                :class="state.planConfirmDelete
                    ? 'border-danger-600 bg-danger-600 text-white hover:bg-danger-700'
                    : 'border-danger-200 bg-white text-danger-700 hover:bg-danger-50 dark:border-danger-500/40 dark:bg-gray-900 dark:text-danger-300 dark:hover:bg-danger-500/10'"
                :disabled="state.planSaving"
                @click="till.deleteFloor()"
            >
                {{ state.planConfirmDelete ? till.t('floor-plan.confirm-delete-floor') : till.t('floor-plan.delete-floor') }}
            </button>

            <span class="flex-1"></span>

            <FloorZoom :canvas="canvas" />

            <button type="button" :class="button" :disabled="state.planSaving" @click="till.cancelPlanEdit()">
                {{ till.t('common.cancel') }}
            </button>

            <button
                type="button"
                class="flex min-h-10 flex-none items-center justify-center rounded-lg bg-primary-600 px-5 text-sm font-semibold text-white transition-colors hover:bg-primary-700 disabled:opacity-60"
                :disabled="state.planSaving"
                @click="till.savePlan()"
            >
                {{ state.planSaving ? till.t('common.saving') : till.t('common.save') }}
            </button>

            <p v-if="state.planError" class="w-full text-sm text-danger-600 dark:text-danger-400">
                {{ state.planError }}
            </p>
        </div>

        <div v-if="selected" class="flex flex-none flex-wrap items-center gap-2 rounded-xl border border-primary-200 bg-primary-50 p-2 dark:border-primary-500/30 dark:bg-primary-500/10">
            <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                {{ till.t('floor-plan.table-number') }}

                <input
                    :value="selected.table_number"
                    type="text"
                    maxlength="16"
                    class="min-h-10 w-20 rounded-lg border border-gray-200 bg-white px-2 text-center text-sm font-semibold text-gray-950 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    @input="till.updatePlanTable({ table_number: $event.target.value })"
                >
            </label>

            <div class="flex items-center gap-1">
                <button type="button" :class="button" :aria-label="till.t('floor-plan.fewer-seats')" @click="till.updatePlanTable({ seats: selected.seats - 1 })">&minus;</button>

                <span class="min-w-16 text-center text-sm text-gray-700 dark:text-gray-200">{{ till.choice('floor.seats', selected.seats) }}</span>

                <button type="button" :class="button" :aria-label="till.t('floor-plan.more-seats')" @click="till.updatePlanTable({ seats: selected.seats + 1 })">+</button>
            </div>

            <button type="button" :class="button" @click="till.updatePlanTable({ shape: selected.shape === 'round' ? 'square' : 'round' })">
                {{ selected.shape === 'round' ? till.t('floor-plan.make-square') : till.t('floor-plan.make-round') }}
            </button>

            <div class="flex items-center gap-1">
                <button
                    v-for="colour in palette"
                    :key="colour.value"
                    type="button"
                    class="size-7 rounded-full border-2 transition-transform hover:scale-110"
                    :class="selected.color === colour.value ? 'border-gray-950 dark:border-white' : 'border-transparent'"
                    :style="{ backgroundColor: colour.value }"
                    :title="till.t(`floor-plan.colours.${colour.name}`)"
                    :aria-label="till.t(`floor-plan.colours.${colour.name}`)"
                    :aria-pressed="selected.color === colour.value"
                    @click="till.updatePlanTable({ color: colour.value })"
                />

                <button
                    type="button"
                    class="flex size-7 items-center justify-center rounded-full border-2 border-dashed border-gray-300 text-xs text-gray-500 dark:border-gray-600 dark:text-gray-400"
                    :aria-label="till.t('floor-plan.no-colour')"
                    @click="till.updatePlanTable({ color: null })"
                >
                    &times;
                </button>
            </div>

            <span class="flex-1"></span>

            <button type="button" :class="button" @click="till.duplicatePlanTable()">
                {{ till.t('floor-plan.duplicate') }}
            </button>

            <button
                type="button"
                class="flex min-h-10 flex-none items-center justify-center rounded-lg border border-danger-200 bg-white px-3 text-sm font-medium text-danger-700 transition-colors hover:bg-danger-50 dark:border-danger-500/40 dark:bg-gray-900 dark:text-danger-300 dark:hover:bg-danger-500/10"
                @click="till.removePlanTable()"
            >
                {{ till.t('floor-plan.delete-table') }}
            </button>
        </div>

        <p v-else class="flex-none text-xs text-gray-500 dark:text-gray-400">
            {{ till.t('floor-plan.hint') }}
        </p>

        <div
            ref="scroller"
            class="min-h-0 flex-auto overflow-auto rounded-xl border-2 border-dashed border-primary-300 dark:border-primary-500/40"
            :class="backdropClass"
            :style="backdrop"
        >
            <div class="relative" :style="canvas.frameStyle.value">
                <div
                    class="absolute left-0 top-0 origin-top-left bg-[radial-gradient(circle,rgb(0_0_0/0.08)_1px,transparent_1px)] bg-size-[20px_20px] dark:bg-[radial-gradient(circle,rgb(255_255_255/0.08)_1px,transparent_1px)]"
                    :style="canvas.planStyle.value"
                    @pointerdown.self="state.planSelectedKey = null"
                >
                    <div
                        v-for="table in till.planTables"
                        :key="table.key"
                        class="absolute flex touch-none cursor-move select-none flex-col items-center justify-center border-4 bg-white/80 text-gray-900 shadow-sm dark:bg-gray-900/80 dark:text-gray-100"
                        :class="[
                            table.shape === 'round' ? 'rounded-full' : 'rounded-xl',
                            table.color ? '' : 'border-gray-300 dark:border-gray-600',
                            state.planSelectedKey === table.key ? 'ring-4 ring-primary-500/50' : '',
                        ]"
                        :style="placement(table)"
                        @pointerdown="startMove($event, table)"
                        @pointermove="track"
                        @pointerup="release"
                        @pointercancel="release"
                    >
                        <span class="text-base font-bold leading-none">{{ table.table_number }}</span>

                        <span class="text-[0.625rem] leading-none opacity-70">{{ till.choice('floor.seats', table.seats) }}</span>

                        <span
                            v-if="state.planSelectedKey === table.key"
                            class="absolute -bottom-2 -right-2 size-5 cursor-se-resize rounded-full border-2 border-white bg-primary-600 dark:border-gray-900"
                            @pointerdown="startResize($event, table)"
                            @pointermove="track"
                            @pointerup="release"
                            @pointercancel="release"
                        ></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
