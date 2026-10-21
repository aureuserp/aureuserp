<script setup>
import { inject, computed, ref, watch } from 'vue'
import TillModal from './TillModal.vue'
import { computeAll } from '../tax/computer.js'

const till = inject('till')

const state = till.state

const draft = ref({ name: '', barcode: '', price: 0, is_storable: true, tracking: 'qty', category_id: null, tax_ids: [] })

const categories = computed(() => till.master.categories.all())

const trackingOptions = computed(() => till.config.tracking_options ?? [])

const taxes = computed(() => till.master.taxes.all().filter((tax) => tax.amount_type !== 'group'))

watch(() => state.productModalOpen, (open) => {
    if (open) {
        draft.value = { name: '', barcode: '', price: 0, is_storable: true, tracking: 'qty', category_id: null, tax_ids: [] }
    }
})

const inclusiveTotal = computed(() => {
    const selected = draft.value.tax_ids.map((id) => till.taxById.get(id)).filter(Boolean)

    if (!selected.length) {
        return null
    }

    return computeAll({
        taxes: selected,
        priceUnit: Number(draft.value.price) || 0,
        quantity: 1,
        currencyRounding: till.currency.rounding,
        roundingMethod: till.roundingMethod,
    }).total_included
})

function toggleTax(id) {
    const index = draft.value.tax_ids.indexOf(id)

    if (index === -1) {
        draft.value.tax_ids.push(id)
    } else {
        draft.value.tax_ids.splice(index, 1)
    }
}
</script>

<template>
    <TillModal :heading="till.t('product-form.heading')" :open="state.productModalOpen" width="max-w-xl" @close="till.closeProductForm()">

        <div class="flex min-h-0 flex-auto flex-col gap-4">
            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ till.t('product-form.name') }}</span>

                <input
                    v-model="draft.name"
                    type="text"
                    :placeholder="till.t('product-form.name-placeholder')"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-950 placeholder:text-gray-400 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                >
            </label>

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ till.t('product-form.barcode') }}</span>

                <input
                    v-model="draft.barcode"
                    type="text"
                    :placeholder="till.t('product-form.barcode-placeholder')"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 font-mono text-sm text-gray-950 placeholder:text-gray-400 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                >
            </label>

            <div class="flex flex-col gap-2">
                <label class="flex items-center gap-2">
                    <input v-model="draft.is_storable" type="checkbox" class="size-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">

                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ till.t('product-form.tracking') }}</span>
                </label>

                <select
                    v-if="draft.is_storable && trackingOptions.length"
                    v-model="draft.tracking"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-950 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                >
                    <option v-for="option in trackingOptions" :key="option.value" :value="option.value">
                        {{ option.label }}
                    </option>
                </select>
            </div>

            <label class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ till.t('product-form.price') }}</span>

                <input
                    v-model.number="draft.price"
                    type="number"
                    step="0.01"
                    min="0"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 font-mono text-sm tabular-nums text-gray-950 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                >
            </label>

            <div v-if="taxes.length" class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ till.t('product-form.taxes') }}</span>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        v-for="tax in taxes"
                        :key="tax.id"
                        type="button"
                        class="rounded-full border px-3 py-1.5 text-xs font-medium transition-colors"
                        :class="draft.tax_ids.includes(tax.id)
                            ? 'border-primary-500 bg-primary-50 text-primary-700 dark:border-primary-400 dark:bg-primary-500/15 dark:text-primary-300'
                            : 'border-gray-200 bg-white text-gray-600 hover:border-primary-400 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300'"
                        @click="toggleTax(tax.id)"
                    >
                        {{ tax.name }}
                    </button>

                    <span v-if="inclusiveTotal !== null" class="text-xs text-gray-500 dark:text-gray-400">
                        {{ till.t('product-form.tax-included', { amount: till.money(inclusiveTotal) }) }}
                    </span>
                </div>
            </div>

            <label v-if="categories.length" class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ till.t('product-form.category') }}</span>

                <select
                    v-model="draft.category_id"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-950 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                >
                    <option :value="null">{{ till.t('product-form.unsaleable') }}</option>

                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
            </label>

            <p v-if="state.productError" class="rounded-lg bg-danger-50 px-3 py-2 text-sm text-danger-700 dark:bg-danger-500/15 dark:text-danger-300">
                {{ state.productError }}
            </p>

            <div class="flex gap-2">
                <button
                    type="button"
                    class="flex min-h-11 flex-1 items-center justify-center rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                    @click="till.closeProductForm()"
                >
                    {{ till.t('common.discard') }}
                </button>

                <button
                    type="button"
                    class="flex min-h-11 flex-2 items-center justify-center rounded-lg bg-primary-600 text-sm font-semibold text-white hover:bg-primary-700 disabled:bg-gray-200 dark:disabled:bg-gray-800 disabled:text-gray-400"
                    :disabled="!draft.name.trim() || state.productSaving"
                    @click="till.createProduct(draft)"
                >
                    {{ till.t(state.productSaving ? 'common.saving' : 'common.save') }}
                </button>
            </div>
        </div>
    </TillModal>
</template>
