<script setup>
import { inject, computed, ref } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state

const line = computed(() => till.lotLine)

const product = computed(() => till.lotProduct)

const singleItem = computed(() => line.value && till.allowsOnlyOneLot(line.value.product_id))

const isRefund = computed(() => Number(line.value?.qty ?? 0) < 0)

const options = computed(() => (line.value ? till.existingLotsFor(line.value.product_id) : []))

const available = computed(() => (line.value && !isRefund.value ? till.availableLotsFor(line.value.product_id, line.value.uuid) : []))

const captured = computed(() => (line.value?.lots ?? []).map((lot) => lot.lot_name))

const openIndex = ref(null)

const highlighted = ref(0)

function suggestionsFor(index) {
    if (isRefund.value) {
        return []
    }

    const query = String(state.lotRows[index] ?? '').trim().toLowerCase()

    const taken = state.lotRows.filter((row, position) => position !== index).map((row) => String(row).trim())

    const pool = query ? options.value : available.value

    return pool.filter((lot) => !taken.includes(lot.name) && (!query || lot.name.toLowerCase().includes(query)))
}

function availableFor(index) {
    return options.value.filter((lot) => (
        (available.value.includes(lot) || captured.value.includes(lot.name))
        && (lot.name === state.lotRows[index] || !state.lotRows.includes(lot.name))
    ))
}

function remaining(lot) {
    return till.remainingLotQty(lot, line.value?.uuid ?? null)
}

function formatQty(qty) {
    return Number(qty).toLocaleString(undefined, { maximumFractionDigits: 4 })
}

function openSuggestions(index) {
    openIndex.value = index

    highlighted.value = 0
}

function closeSuggestions() {
    openIndex.value = null
}

function onInput(index, value) {
    till.setLotRow(index, value)

    openSuggestions(index)
}

function choose(index, lot) {
    till.setLotRow(index, lot.name)

    closeSuggestions()
}

function onKeydown(index, event) {
    const suggestions = suggestionsFor(index)

    if (event.key === 'ArrowDown') {
        event.preventDefault()

        if (openIndex.value !== index) {
            openSuggestions(index)

            return
        }

        highlighted.value = Math.min(highlighted.value + 1, suggestions.length - 1)
    } else if (event.key === 'ArrowUp') {
        event.preventDefault()

        highlighted.value = Math.max(highlighted.value - 1, 0)
    } else if (event.key === 'Enter' && openIndex.value === index && suggestions[highlighted.value]) {
        event.preventDefault()

        choose(index, suggestions[highlighted.value])
    }
}
</script>

<template>
    <TillModal :heading="till.t('lots.heading')" :description="product?.name" :open="state.lotLineUuid !== null" width="max-w-lg" @close="till.closeLots()">

        <div class="flex min-h-0 flex-auto flex-col gap-2">
            <p
                v-if="state.lotError"
                class="rounded-lg bg-danger-50 px-3 py-2 text-sm text-danger-700 dark:bg-danger-500/10 dark:text-danger-300"
            >
                {{ state.lotError }}
            </p>

            <div v-for="(row, index) in state.lotRows" :key="index" class="flex items-center gap-2">
                <select
                    v-if="!till.canCreateLots"
                    :value="row"
                    class="min-h-11 min-w-0 flex-1 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-950 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    @change="till.setLotRow(index, $event.target.value)"
                >
                    <option value="" disabled>{{ till.t('lots.placeholder') }}</option>

                    <option v-for="lot in availableFor(index)" :key="lot.id" :value="lot.name">
                        {{ lot.name }} ({{ formatQty(remaining(lot)) }})
                    </option>
                </select>

                <div v-else class="relative min-w-0 flex-1">
                    <input
                        :value="row"
                        type="text"
                        autocomplete="off"
                        role="combobox"
                        :aria-expanded="openIndex === index && suggestionsFor(index).length > 0"
                        class="min-h-11 w-full rounded-lg border border-gray-200 bg-white py-2 ps-3 pe-9 text-sm text-gray-950 placeholder:text-gray-400 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500"
                        :placeholder="till.t('lots.placeholder')"
                        @input="onInput(index, $event.target.value)"
                        @focus="openSuggestions(index)"
                        @click="openSuggestions(index)"
                        @blur="closeSuggestions()"
                        @keydown="onKeydown(index, $event)"
                    >

                    <svg
                        v-if="available.length"
                        class="pointer-events-none absolute end-3 top-1/2 size-4 -translate-y-1/2 text-gray-400 dark:text-gray-500"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>

                    <ul
                        v-if="openIndex === index && suggestionsFor(index).length"
                        role="listbox"
                        class="absolute inset-x-0 top-full z-10 mt-1 max-h-56 overflow-y-auto rounded-lg border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-900"
                    >
                        <li
                            v-for="(lot, position) in suggestionsFor(index)"
                            :key="lot.id"
                            role="option"
                            :aria-selected="position === highlighted"
                            class="flex cursor-pointer items-center justify-between gap-3 px-3 py-2 text-sm text-gray-950 dark:text-white"
                            :class="position === highlighted ? 'bg-gray-100 dark:bg-gray-800' : 'hover:bg-gray-50 dark:hover:bg-gray-800'"
                            @mousedown.prevent="choose(index, lot)"
                            @mouseenter="highlighted = position"
                        >
                            <span class="truncate">{{ lot.name }}</span>

                            <span
                                class="flex-none tabular-nums"
                                :class="remaining(lot) > 0 ? 'text-gray-500 dark:text-gray-400' : 'text-danger-600 dark:text-danger-400'"
                            >
                                {{ formatQty(remaining(lot)) }}
                            </span>
                        </li>
                    </ul>
                </div>

                <button
                    v-if="!singleItem"
                    type="button"
                    class="flex size-11 flex-none items-center justify-center rounded-lg text-gray-400 hover:bg-danger-50 hover:text-danger-600 dark:hover:bg-danger-500/10 dark:hover:text-danger-400"
                    :aria-label="till.t('lots.remove')"
                    @click="till.removeLotRow(index)"
                >
                    <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                    </svg>
                </button>
            </div>

            <button
                v-if="!singleItem"
                type="button"
                class="flex min-h-11 items-center justify-center gap-1 self-start rounded-lg border border-dashed border-gray-300 px-3 text-sm text-gray-600 hover:bg-gray-50 active:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800 dark:active:bg-gray-700"
                @click="till.addLotRow()"
            >
                + {{ till.t('lots.add') }}
            </button>
        </div>

        <template #footer>
            <button
                type="button"
                class="flex min-h-11 flex-1 items-center justify-center rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-600 hover:bg-gray-50 active:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:active:bg-gray-700"
                @click="till.closeLots()"
            >
                {{ till.t('common.discard') }}
            </button>

            <button
                type="button"
                class="flex min-h-11 flex-2 items-center justify-center rounded-lg bg-primary-600 text-sm font-semibold text-white hover:bg-primary-700"
                @click="till.confirmLots()"
            >
                {{ till.t('lots.warning.proceed') }}
            </button>
        </template>
    </TillModal>
</template>
