<script setup>
import { inject, computed } from 'vue'

const till = inject('till')

const state = till.state

const categories = computed(() => till.visibleCategories(state.categoryId))

const selectedCategoryIds = computed(() => new Set(till.categoryPath(state.categoryId).map((category) => category.id)))

const products = computed(() => till.searchProducts(state.search, state.categoryId))

const cartQuantities = computed(() => till.cartQuantityByProduct())

function isDepleted(product) {
    return Boolean(product.is_storable) && till.freeQty(product.id) <= 0
}

function isCategorySelected(id) {
    return selectedCategoryIds.value.has(id)
}

function isSubCategory(category) {
    return Boolean(till.master.categories.get(category.parent_id))
}

function categoryChipClass(category) {
    if (isCategorySelected(category.id)) {
        return 'border-primary-600 bg-primary-600 text-white dark:border-primary-500 dark:bg-primary-500'
    }

    if (isSubCategory(category)) {
        return 'border-dashed border-primary-300 bg-primary-50 text-primary-700 hover:border-primary-500 dark:border-primary-500/50 dark:bg-primary-500/10 dark:text-primary-300'
    }

    return 'border-gray-200 bg-white text-gray-600 hover:border-primary-400 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300'
}

function categoryCardClass(category) {
    if (isCategorySelected(category.id)) {
        return 'border-primary-600 ring-1 ring-primary-600 dark:border-primary-500 dark:ring-primary-500'
    }

    if (isSubCategory(category)) {
        return 'border-dashed border-primary-300 hover:border-primary-500 dark:border-primary-500/50'
    }

    return 'border-gray-200 hover:border-primary-400 dark:border-gray-700'
}

function categoryCardLabelClass(category) {
    if (isCategorySelected(category.id)) {
        return 'bg-primary-600 text-white dark:bg-primary-500'
    }

    if (isSubCategory(category)) {
        return 'bg-primary-50 text-primary-700 dark:bg-primary-500/10 dark:text-primary-300'
    }

    return 'bg-white text-gray-600 dark:bg-gray-900 dark:text-gray-300'
}

function selectCategory(id) {
    till.selectCategory(id)
}

</script>

<template>
    <div class="flex min-h-0 flex-col gap-3">
        <div v-if="categories.length && !till.config.show_category_images" class="flex flex-none flex-wrap items-center gap-2">
            <button
                v-for="category in categories"
                :key="category.id"
                type="button"
                class="flex items-center gap-1 rounded-full border px-3 py-1.5 text-xs font-medium transition-colors"
                :class="categoryChipClass(category)"
                @click="selectCategory(category.id)"
            >
                <svg v-if="isSubCategory(category)" class="size-3.5 flex-none rtl:rotate-180" width="14" height="14" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                </svg>

                {{ category.name }}
            </button>
        </div>

        <div v-if="categories.length && till.config.show_category_images" class="flex flex-none flex-wrap gap-2 ps-0.5 pe-0.5 pt-0.5">
            <button
                v-for="category in categories"
                :key="category.id"
                type="button"
                class="flex w-24 flex-none flex-col overflow-hidden rounded-xl border transition-colors"
                :class="categoryCardClass(category)"
                @click="selectCategory(category.id)"
            >
                <span class="flex aspect-[4/3] w-full items-center justify-center overflow-hidden bg-gray-100 dark:bg-gray-800">
                    <img v-if="category.image" :src="category.image" :alt="category.name" class="h-full w-full object-cover">

                    <span v-else class="text-lg font-semibold text-gray-400">{{ category.name.charAt(0) }}</span>
                </span>

                <span
                    class="flex items-center justify-center gap-0.5 px-2 py-1.5 text-xs font-medium"
                    :class="categoryCardLabelClass(category)"
                >
                    <svg v-if="isSubCategory(category)" class="size-3 flex-none rtl:rotate-180" width="12" height="12" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>

                    <span class="truncate">{{ category.name }}</span>
                </span>
            </button>
        </div>

        <div class="grid min-h-0 flex-auto auto-rows-min gap-3 overflow-y-auto pb-0.5 ps-0.5 pe-1 pt-0.5 [grid-template-columns:repeat(auto-fill,minmax(9rem,1fr))]">
            <div
                v-for="product in products"
                :key="product.id"
                class="relative flex flex-col overflow-hidden rounded-xl border bg-white text-start transition-colors dark:bg-gray-900"
                :class="cartQuantities.get(product.id)
                    ? 'border-primary-500 ring-1 ring-primary-500 dark:border-primary-400 dark:ring-primary-400'
                    : 'border-gray-200 hover:border-primary-400 dark:border-gray-700'"
            >
                <button
                    type="button"
                    class="absolute end-0 top-0 z-[1] flex size-7 items-center justify-center rounded-es-lg text-white transition-colors"
                    :class="isDepleted(product)
                        ? 'bg-danger-600 hover:bg-danger-700'
                        : 'bg-gray-950/45 hover:bg-gray-950/70'"
                    :aria-label="isDepleted(product)
                        ? till.t('catalogue.info-depleted', { product: product.name })
                        : till.t('catalogue.info', { product: product.name })"
                    @click.stop="till.openProductInfo(product.id)"
                >
                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" />
                    </svg>
                </button>

                <button
                    type="button"
                    class="flex min-h-0 flex-auto flex-col text-start"
                    @click="till.pickProduct(product.id)"
                >
                    <span
                        v-if="till.config.show_product_images"
                        class="flex aspect-square w-full items-center justify-center overflow-hidden bg-gray-50 dark:bg-gray-800"
                    >
                        <img v-if="product.image" :src="product.image" :alt="product.name" class="h-full w-full object-cover">

                        <svg v-else class="size-8 text-gray-300 dark:text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                    </span>

                    <span class="flex flex-auto flex-col gap-0.5 p-2">
                        <span class="line-clamp-2 text-[0.8125rem] font-medium text-gray-950 dark:text-white">
                            {{ product.name }}
                        </span>

                        <span class="mt-auto flex min-h-6 items-end justify-between gap-2">
                            <span class="font-mono text-[0.9375rem] font-bold leading-tight tabular-nums text-primary-600 dark:text-primary-400">
                                {{ till.money(till.priceFor(product.id)) }}
                            </span>

                            <span
                                v-if="cartQuantities.get(product.id)"
                                class="text-2xl font-bold leading-none text-gray-400 dark:text-gray-500"
                            >
                                {{ +cartQuantities.get(product.id).toFixed(3) }}
                            </span>
                        </span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</template>
