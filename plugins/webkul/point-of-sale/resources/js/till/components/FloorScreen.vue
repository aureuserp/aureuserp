<script setup>
import { inject, computed, ref } from 'vue'
import FloorPlanEditor from './FloorPlanEditor.vue'
import FloorZoom from './FloorZoom.vue'
import { useFloorCanvas } from './floor-canvas.js'

const till = inject('till')

const floor = computed(() => till.activeFloor)

const tables = computed(() => floor.value?.tables ?? [])

const isMapped = computed(() => till.state.floorView === 'map'
    && (Boolean(floor.value?.background_image) || tables.value.some((table) => table.position_h > 0 || table.position_v > 0)))

const backdrop = computed(() => (floor.value?.background_color ? { '--floor-bg': floor.value.background_color } : {}))

const backdropClass = computed(() => (floor.value?.background_color
    ? 'bg-(--floor-bg) dark:bg-[oklch(from_var(--floor-bg)_0.32_calc(c*2)_h)]'
    : 'bg-gray-50 dark:bg-gray-950'))

const scroller = ref(null)

const canvas = useFloorCanvas(scroller, floor, tables)

const summaries = computed(() => new Map(tables.value.map((table) => [table.id, till.tableSummary(table.id)])))

function summary(table) {
    return summaries.value.get(table.id) ?? { count: 0, total: 0, guests: 0 }
}

function isOccupied(table) {
    return summary(table).count > 0
}

function paint(table) {
    if (!table.color) {
        return {}
    }

    return isOccupied(table)
        ? { borderColor: table.color, backgroundColor: table.color }
        : { borderColor: table.color }
}

function placement(table) {
    return {
        left: `${table.position_h}px`,
        top: `${table.position_v}px`,
        width: `${table.width}px`,
        height: `${table.height}px`,
        ...paint(table),
    }
}

function tileClass(table) {
    const shape = table.shape === 'round' ? 'rounded-full' : 'rounded-xl'

    if (isOccupied(table)) {
        return table.color
            ? `${shape} border-4 text-white`
            : `${shape} border-4 border-primary-600 bg-primary-600 text-white dark:border-primary-500 dark:bg-primary-500`
    }

    return table.color
        ? `${shape} border-4 bg-white/70 text-gray-900 hover:bg-white dark:bg-gray-900/70 dark:text-gray-100 dark:hover:bg-gray-900`
        : `${shape} border-4 border-gray-300 bg-white/70 text-gray-900 hover:border-gray-400 hover:bg-white dark:border-gray-600 dark:bg-gray-900/70 dark:text-gray-100 dark:hover:border-gray-500 dark:hover:bg-gray-900`
}
</script>

<template>
    <div class="flex min-h-0 flex-col gap-3">
        <div
            v-if="till.state.transferOrderUuid"
            class="flex flex-none flex-wrap items-center gap-3 rounded-xl border border-warning-300 bg-warning-50 px-4 py-3 text-sm text-warning-800 dark:border-warning-500/40 dark:bg-warning-500/10 dark:text-warning-200"
        >
            <span class="flex-1 font-semibold">
                {{ till.t('transfer.prompt', { order: till.transferOrder ? till.orderLabel(till.transferOrder) : '' }) }}
            </span>

            <span v-if="till.state.transferError" class="w-full text-danger-700 dark:text-danger-300 sm:order-last">
                {{ till.state.transferError }}
            </span>

            <button
                type="button"
                class="rounded-lg border border-warning-300 bg-white px-3 py-1.5 font-medium text-warning-800 transition-colors hover:bg-warning-100 dark:border-warning-500/40 dark:bg-gray-900 dark:text-warning-200 dark:hover:bg-gray-800"
                @click="till.cancelTransfer()"
            >
                {{ till.t('common.cancel') }}
            </button>
        </div>

        <div class="flex flex-none items-center gap-2">
            <div class="flex min-w-0 flex-1 items-center gap-2 overflow-x-auto">
                <button
                    v-for="entry in till.floors"
                    :key="entry.id"
                    type="button"
                    class="flex-none rounded-lg border px-4 py-2 text-sm font-semibold transition-colors"
                    :class="floor?.id === entry.id
                        ? 'border-primary-600 bg-primary-600 text-white dark:border-primary-500'
                        : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:border-gray-600'"
                    @click="till.selectFloor(entry.id)"
                >
                    {{ entry.name }}
                </button>
            </div>

            <FloorZoom v-if="!till.state.planEditing && isMapped" :canvas="canvas" />

            <button
                v-if="!till.state.planEditing && tables.length"
                type="button"
                class="flex flex-none items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:border-gray-600"
                :title="till.t('floor-plan.switch-view')"
                @click="till.toggleFloorView()"
            >
                {{ till.state.floorView === 'grid' ? till.t('floor-plan.view-map') : till.t('floor-plan.view-grid') }}
            </button>

            <button
                v-if="till.canEditPlan && !till.state.planEditing && (floor || till.config.floor_plan?.can_create)"
                type="button"
                class="flex flex-none items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:border-gray-600"
                @click="floor ? till.startPlanEdit() : till.addFloor()"
            >
                {{ till.t('floor-plan.edit') }}
            </button>

            <button
                v-if="!till.state.planEditing && !till.state.transferOrderUuid"
                type="button"
                class="flex flex-none items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:border-gray-600"
                @click="till.newOrder(null)"
            >
                + {{ till.t('tabs.new') }}
            </button>

            <button
                v-if="till.parkedOrders.length && !till.state.planEditing"
                type="button"
                class="flex flex-none items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition-colors hover:border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:border-gray-600"
                @click="till.openOrders()"
            >
                {{ till.t('tabs.all') }}

                <span class="rounded-full bg-gray-100 px-2 font-mono text-xs tabular-nums dark:bg-gray-800">{{ till.parkedOrders.length }}</span>
            </button>
        </div>

        <FloorPlanEditor v-if="till.state.planEditing" class="min-h-0 flex-auto" />

        <div
            v-else
            ref="scroller"
            class="min-h-0 flex-auto overflow-auto rounded-xl border border-gray-200 dark:border-gray-700"
            :class="backdropClass"
            :style="backdrop"
        >
            <p v-if="!floor" class="p-10 text-center text-sm text-gray-500 dark:text-gray-400">
                {{ till.t('floor.no-floors') }}
            </p>

            <p v-else-if="!tables.length" class="p-10 text-center text-sm text-gray-500 dark:text-gray-400">
                {{ till.t('floor.empty') }}
            </p>

            <div v-else-if="isMapped" class="relative" :style="canvas.frameStyle.value">
                <div class="absolute left-0 top-0 origin-top-left" :style="canvas.planStyle.value">
                    <button
                        v-for="table in tables"
                        :key="table.id"
                        type="button"
                        class="absolute flex flex-col items-center justify-center gap-0.5 p-1 text-center shadow-sm transition-colors"
                        :class="tileClass(table)"
                        :style="placement(table)"
                        @click="till.tapTable(table.id)"
                    >
                        <span class="text-base font-bold leading-none">{{ table.table_number }}</span>

                        <span v-if="summary(table).count" class="font-mono text-[0.625rem] leading-none tabular-nums">
                            {{ till.money(summary(table).total) }}
                        </span>

                        <span v-else class="text-[0.625rem] leading-none opacity-70">
                            {{ till.choice('floor.seats', table.seats) }}
                        </span>
                    </button>
                </div>
            </div>

            <div v-else class="grid grid-cols-[repeat(auto-fill,minmax(8rem,1fr))] gap-3 p-3">
                <button
                    v-for="table in tables"
                    :key="table.id"
                    type="button"
                    class="flex aspect-square flex-col items-center justify-center gap-1 p-2 text-center shadow-sm transition-colors"
                    :class="tileClass(table)"
                    :style="paint(table)"
                    @click="till.tapTable(table.id)"
                >
                    <span class="text-2xl font-bold leading-none">{{ table.table_number }}</span>

                    <span class="text-xs opacity-70">{{ till.choice('floor.seats', table.seats) }}</span>

                    <span v-if="summary(table).count" class="font-mono text-sm font-semibold tabular-nums">
                        {{ till.money(summary(table).total) }}
                    </span>

                    <span v-if="summary(table).count > 1" class="text-xs">
                        {{ till.choice('floor.orders', summary(table).count) }}
                    </span>

                    <span v-if="summary(table).guests" class="text-xs">
                        {{ till.choice('floor.guests', summary(table).guests) }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</template>
