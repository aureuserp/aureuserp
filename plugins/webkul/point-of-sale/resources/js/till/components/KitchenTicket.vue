<script setup>
import { inject, computed } from 'vue'

const till = inject('till')

const ticket = computed(() => till.state.kitchenTicket)

const sections = computed(() => [
    { key: 'added', title: till.t('kitchen.new'), entries: ticket.value?.added ?? [] },
    { key: 'cancelled', title: till.t('kitchen.cancelled'), entries: ticket.value?.cancelled ?? [] },
    { key: 'noted', title: till.t('kitchen.note-changed'), entries: ticket.value?.noted ?? [] },
].filter((section) => section.entries.length))

const mode = computed(() => {
    if (ticket.value?.mode_changed) {
        return ticket.value.takeaway ? till.t('kitchen.to-takeaway') : till.t('kitchen.to-dine-in')
    }

    return ticket.value?.takeaway ? till.t('kitchen.takeaway') : till.t('kitchen.dine-in')
})
</script>

<template>
    <div v-if="ticket" id="pos-kitchen-ticket" class="hidden">
        <div class="pos-receipt mx-auto w-[80mm] max-w-full p-4 text-gray-950">
            <div class="text-center">
                <p class="text-xs font-semibold uppercase tracking-wide">{{ ticket.printer }}</p>

                <p class="text-xl font-bold">{{ mode }}</p>

                <p class="text-xs">{{ ticket.register }} &middot; {{ ticket.time }}</p>

                <p v-if="ticket.cashier" class="text-xs">{{ till.t('kitchen.by', { name: ticket.cashier }) }}</p>

                <p class="mt-2 text-lg">
                    <template v-if="ticket.table">{{ till.t('kitchen.table', { table: ticket.table }) }}</template>
                    <template v-else-if="ticket.label">{{ ticket.label }}</template>
                    <template v-if="ticket.tracking_number"> &middot; #{{ ticket.tracking_number }}</template>
                </p>
            </div>

            <hr class="my-3 border-t-4 border-dashed border-gray-950">

            <div v-for="section in sections" :key="section.key" class="mb-3">
                <p class="text-center text-sm font-bold uppercase">{{ section.title }}</p>

                <div v-for="entry in section.entries" :key="`${section.key}-${entry.uuid}`" class="mt-1 text-lg">
                    <p class="flex gap-3">
                        <span class="font-semibold tabular-nums">{{ entry.qty }}</span>
                        <span>{{ entry.name }}</span>
                    </p>

                    <p v-if="entry.note" class="ps-8 text-sm italic">{{ entry.note }}</p>
                </div>
            </div>

            <p v-if="ticket.note" class="mt-4 text-base italic">
                {{ till.t('kitchen.order-note') }}: {{ ticket.note }}
            </p>
        </div>
    </div>
</template>
