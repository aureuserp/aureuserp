<script setup>
import { inject, computed } from 'vue'
import OrderTableBadge from './OrderTableBadge.vue'

const till = inject('till')

const state = till.state

const drafts = computed(() => till.parkedOrders)

const limit = 3

const visible = computed(() => {
    const all = drafts.value

    if (all.length <= limit) {
        return all
    }

    const active = all.find((order) => order.uuid === state.activeOrderUuid)

    const others = all.filter((order) => order.uuid !== active?.uuid)

    return active ? [active, ...others].slice(0, limit) : others.slice(0, limit)
})

const overflow = computed(() => drafts.value.length - visible.value.length)
</script>

<template>
    <div class="flex flex-none items-stretch gap-2">
        <button
            v-for="order in visible"
            :key="order.uuid"
            type="button"
            class="flex h-10 min-w-0 flex-1 items-center justify-center gap-1 whitespace-nowrap rounded-lg border px-3 text-xs font-semibold transition-colors"
            :class="state.activeOrderUuid === order.uuid
                ? 'border-primary-600 bg-primary-50 text-gray-950 dark:border-primary-500 dark:bg-primary-500/15 dark:text-white'
                : 'border-gray-200 bg-white text-gray-950 hover:border-gray-300 dark:hover:border-gray-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white'"
            @click="till.selectOrder(order.uuid)"
        >
            <OrderTableBadge :order="order" />

            <span class="truncate">{{ till.orderLabel(order) }}</span>
        </button>

        <button
            v-if="overflow > 0"
            type="button"
            class="flex h-10 flex-none items-center whitespace-nowrap rounded-lg border border-gray-200 bg-white px-3 text-xs font-semibold text-gray-950 transition-colors hover:border-gray-300 dark:hover:border-gray-600 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
            @click="till.openOrders()"
        >
            {{ till.t('common.more', { count: overflow }) }}
        </button>

        <button
            type="button"
            class="flex h-10 w-11 flex-none items-center justify-center rounded-lg border border-dashed border-gray-200 bg-transparent text-lg text-gray-600 transition-colors hover:border-gray-300 dark:hover:border-gray-600 dark:border-gray-700 dark:text-gray-300"
            :title="till.t('tabs.new')"
            :aria-label="till.t('tabs.new')"
            @click="till.newOrder()"
        >
            +
        </button>
    </div>
</template>
