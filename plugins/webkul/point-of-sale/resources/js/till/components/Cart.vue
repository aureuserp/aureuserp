<script setup>
import { inject, computed } from 'vue'

const till = inject('till')

const state = till.state

const order = computed(() => till.activeOrder)

const totals = computed(() => till.orderTotals())

const paying = computed(() => state.screen === 'payment')

const editable = computed(() => till.isEditable(order.value))

const methods = computed(() => till.master.payment_methods.all())

function productName(productId) {
    return till.master.products.get(productId)?.name ?? ''
}

function uomName(line) {
    return till.master.uoms.get(line.uom_id)?.name ?? ''
}

function productImage(productId) {
    if (!till.config.show_product_images) {
        return null
    }

    return till.master.products.get(productId)?.image ?? null
}
</script>

<template>
    <div class="flex min-h-0 flex-col overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
        <div v-if="paying" class="flex min-h-[clamp(4rem,12vh,7rem)] flex-auto flex-col gap-2 overflow-y-auto p-3">
            <button
                v-for="method in methods"
                :key="method.id"
                type="button"
                class="flex min-h-14 flex-none items-center gap-3 rounded-lg border border-gray-200 bg-white px-4 text-start text-base font-medium text-gray-950 transition-colors hover:border-primary-400 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                @click="till.addPayment(method.id)"
            >
                <svg class="size-6 flex-none text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                </svg>

                <span class="truncate">{{ method.name }}</span>
            </button>
        </div>

        <div v-else class="min-h-[clamp(4rem,12vh,7rem)] flex-auto overflow-y-auto">
            <div
                v-if="order?.draft_error"
                role="alert"
                class="flex items-start gap-3 border-b border-danger-200 bg-danger-50 px-3 py-2.5 text-sm text-danger-800 dark:border-danger-500/40 dark:bg-danger-500/10 dark:text-danger-200"
            >
                <div class="min-w-0 flex-1">
                    <p class="font-semibold">{{ till.t('drafts.not-shared') }}</p>

                    <p class="text-xs">{{ order.draft_error }}</p>
                </div>

                <button
                    type="button"
                    class="flex-none rounded-lg border border-danger-300 bg-white px-3 py-1.5 text-xs font-semibold text-danger-700 hover:bg-danger-100 dark:border-danger-500/40 dark:bg-gray-900 dark:text-danger-300 dark:hover:bg-gray-800"
                    @click="till.retryDraft(order)"
                >
                    {{ till.t('offline.retry') }}
                </button>
            </div>

            <div v-if="!order?.lines.length" class="flex flex-col items-center gap-3 p-6 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ till.t('cart.empty.description') }}
                </p>

                <button
                    v-if="till.canBookTable && !order.is_booked"
                    type="button"
                    class="rounded-lg bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-primary-700"
                    @click="till.bookTable()"
                >
                    {{ till.t('booking.book') }}
                </button>

                <button
                    v-else-if="till.canBookTable && order.is_booked"
                    type="button"
                    class="rounded-lg border border-danger-300 px-5 py-2.5 text-sm font-semibold text-danger-700 transition-colors hover:bg-danger-50 dark:border-danger-500/40 dark:text-danger-300 dark:hover:bg-danger-500/10"
                    @click="till.releaseTable()"
                >
                    {{ till.t('booking.release') }}
                </button>
            </div>

            <div
                v-for="line in order?.lines ?? []"
                :key="line.uuid"
                class="flex w-full items-start gap-2 border-b border-gray-100 px-3 py-2.5 text-start transition-colors dark:border-gray-800"
                :class="editable && state.activeLineUuid === line.uuid ? 'bg-primary-50 dark:bg-primary-500/10' : (editable ? 'hover:bg-gray-50 dark:hover:bg-gray-800' : '')"
            >
                <button
                    type="button"
                    class="flex min-w-0 flex-1 items-start gap-3 text-start"
                    :class="editable ? '' : 'cursor-default'"
                    :disabled="!editable"
                    @click="till.selectLine(line.uuid)"
                >
                <span
                    v-if="till.config.show_product_images"
                    class="flex size-10 flex-none items-center justify-center overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800"
                >
                    <img
                        v-if="productImage(line.product_id)"
                        :src="productImage(line.product_id)"
                        :alt="productName(line.product_id)"
                        class="h-full w-full object-cover"
                    >

                    <svg v-else class="size-5 text-gray-300 dark:text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                </span>

                <span class="min-w-0 flex-1">
                    <span class="block truncate text-sm font-medium text-gray-950 dark:text-white">
                        {{ productName(line.product_id) }}
                    </span>

                    <span class="block font-mono text-xs tabular-nums text-gray-500 dark:text-gray-400">
                        {{ line.qty }} &times; {{ till.money(line.price_unit) }}<template v-if="uomName(line)"> / {{ uomName(line) }}</template>
                        <template v-if="line.discount"> &middot; &minus;{{ till.t('cart.discount', { percentage: line.discount }) }}</template>
                    </span>

                    <span
                        v-if="till.lotsEnabled && till.isTracked(line.product_id)"
                        class="mt-0.5 block truncate font-mono text-xs tabular-nums"
                        :class="line.lots?.length ? 'text-gray-500 dark:text-gray-400' : 'text-danger-600 dark:text-danger-400'"
                        @click.stop="till.openLots(line.uuid)"
                    >
                        {{ line.lots?.length ? line.lots.map((lot) => lot.lot_name).join(', ') : till.t('lots.missing') }}
                    </span>

                    <span v-if="line.note" class="mt-0.5 flex flex-wrap items-center gap-1">
                        <span
                            v-for="(part, index) in till.noteParts(line.note)"
                            :key="index"
                            class="inline-flex max-w-full truncate rounded px-1.5 py-0.5 text-[0.6875rem] font-medium leading-tight"
                            :class="part.color ? '' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300'"
                            :style="part.color ? { backgroundColor: `${part.color}26`, color: part.color } : null"
                        >
                            {{ part.text }}
                        </span>
                    </span>
                </span>

                <span class="flex-none font-mono text-sm tabular-nums font-semibold text-gray-950 dark:text-white">
                    {{ till.money(till.displayLineTotal(line)) }}
                </span>
                </button>

                <button
                    v-if="editable"
                    type="button"
                    class="flex size-9 flex-none items-center justify-center rounded-lg text-gray-400 transition active:bg-danger-100 active:text-danger-700 hover:bg-danger-50 hover:text-danger-600 dark:hover:bg-danger-500/10 dark:hover:text-danger-400 dark:active:bg-danger-500/20"
                    :aria-label="till.t('cart.remove', { product: productName(line.product_id) })"
                    @click.stop="till.removeLine(line.uuid)"
                >
                    <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex-none border-t border-gray-100 p-[clamp(0.5rem,1.2vh,0.75rem)] dark:border-gray-800">
            <div v-if="order?.is_takeaway" class="mb-1.5 flex items-center gap-1.5 text-xs font-semibold text-warning-700 dark:text-warning-400">
                <svg class="size-4 flex-none" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M6 5v1H4.667a1.75 1.75 0 0 0-1.743 1.598l-.826 9.5A1.75 1.75 0 0 0 3.84 19H16.16a1.75 1.75 0 0 0 1.743-1.902l-.826-9.5A1.75 1.75 0 0 0 15.333 6H14V5a4 4 0 0 0-8 0Zm4-2.5A2.5 2.5 0 0 0 7.5 5v1h5V5A2.5 2.5 0 0 0 10 2.5ZM7.5 10a2.5 2.5 0 0 0 5 0V8.75a.75.75 0 0 1 1.5 0V10a4 4 0 0 1-8 0V8.75a.75.75 0 0 1 1.5 0V10Z" clip-rule="evenodd" />
                </svg>

                {{ till.t('takeaway.badge') }}
            </div>

            <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                <span>{{ till.t('cart.subtotal') }}</span>
                <span class="font-mono tabular-nums">{{ till.money(totals.subtotal) }}</span>
            </div>

            <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                <span>{{ till.t('cart.tax') }}</span>
                <span class="font-mono tabular-nums">{{ till.money(totals.tax) }}</span>
            </div>

            <div v-if="totals.rounding" class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                <span>{{ till.t('cart.rounding') }}</span>
                <span class="font-mono tabular-nums">{{ till.money(totals.rounding) }}</span>
            </div>

            <div class="mt-1.5 flex items-center justify-between border-t border-gray-100 pt-1.5 text-lg font-semibold text-gray-950 dark:border-gray-800 dark:text-white">
                <span>{{ till.t('cart.total') }}</span>
                <span class="font-mono tabular-nums">{{ till.money(totals.total) }}</span>
            </div>
        </div>
    </div>
</template>
