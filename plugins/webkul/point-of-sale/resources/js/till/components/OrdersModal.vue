<script setup>
import { inject, computed, ref } from 'vue'
import TillModal from './TillModal.vue'
import OrderTableBadge from './OrderTableBadge.vue'

const till = inject('till')

const state = till.state

const results = computed(() => till.searchOrders(state.ordersSearch))

const confirming = ref(null)

function resume(uuid) {
    till.selectOrder(uuid)

    till.closeOrders()
}

function discard(uuid) {
    till.discardOrder(uuid)

    confirming.value = null
}
</script>

<template>
    <TillModal
        :open="state.ordersModalOpen"
        width="max-w-2xl"
        :heading="till.t('parked.heading')"
        @close="till.closeOrders()"
    >
            <div class="flex flex-none items-center gap-2">
                <input
                    v-model="state.ordersSearch"
                    type="search"
                    :placeholder="till.t('parked.search')"
                    class="h-full min-w-0 flex-1 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-950 placeholder:text-gray-400 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                >

                <button
                    type="button"
                    class="flex-none rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white hover:bg-primary-700"
                    @click="till.newOrder(); till.closeOrders()"
                >
                    {{ till.t('common.new') }}
                </button>
            </div>

            <div class="flex min-h-0 flex-auto flex-col gap-2 overflow-y-auto">
                <div
                    v-for="order in results"
                    :key="order.uuid"
                    class="flex items-center gap-3 rounded-[0.625rem] border px-3 py-2.5"
                    :class="state.activeOrderUuid === order.uuid
                        ? 'border-primary-600 bg-primary-50 dark:border-primary-500 dark:bg-primary-500/15'
                        : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900'"
                >
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-gray-950 dark:text-white">
                            <span class="flex min-w-0 items-center gap-1.5">
                                <OrderTableBadge :order="order" />

                                <span class="truncate">{{ till.orderLabel(order) }}</span>
                            </span>
                        </p>

                        <p class="truncate font-mono text-xs tabular-nums text-gray-500 dark:text-gray-400">
                            {{ till.choice('parked.items', till.orderQuantity(order)) }}
                        </p>
                    </div>

                    <span class="shrink-0 font-mono text-sm font-semibold tabular-nums text-gray-950 dark:text-white">
                        {{ till.money(till.orderTotals(order).total) }}
                    </span>

                    <div class="flex shrink-0 items-center gap-2">
                        <span
                            v-if="state.activeOrderUuid === order.uuid"
                            class="rounded-md bg-primary-100 px-2 py-1 text-xs font-medium text-primary-700 dark:bg-primary-500/20 dark:text-primary-300"
                        >
                            {{ till.t('common.open') }}
                        </span>

                        <button
                            v-else
                            type="button"
                            class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                            @click="resume(order.uuid)"
                        >
                            {{ till.t('common.resume') }}
                        </button>

                        <button
                            v-if="confirming !== order.uuid"
                            type="button"
                            class="flex size-8 items-center justify-center rounded-lg text-gray-400 hover:bg-danger-50 hover:text-danger-600 dark:hover:bg-danger-500/10 dark:hover:text-danger-400"
                            :aria-label="till.t('parked.discard')"
                            @click="confirming = order.uuid"
                        >
                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443A24 24 0 0 0 3.32 4.5a.75.75 0 0 0 .06 1.499l.46-.02.613 8.58A3 3 0 0 0 7.445 17.5h5.11a3 3 0 0 0 2.992-2.94l.613-8.581.46.02a.75.75 0 1 0 .06-1.5 24 24 0 0 0-2.68-.306V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325A42 42 0 0 1 10 4Z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <button
                            v-else
                            type="button"
                            class="rounded-lg bg-danger-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-danger-700"
                            @click="discard(order.uuid)"
                        >
                            {{ till.t('common.confirm') }}
                        </button>
                    </div>
                </div>

                <p v-if="!results.length" class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ till.t('parked.no-match') }}
                </p>
            </div>
    </TillModal>
</template>
