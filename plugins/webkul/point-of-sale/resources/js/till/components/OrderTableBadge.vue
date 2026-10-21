<script setup>
import { inject, computed } from 'vue'

const props = defineProps({
    order: { type: Object, required: true },
})

const till = inject('till')

const table = computed(() => till.orderTable(props.order))
</script>

<template>
    <span
        v-if="till.isRestaurant"
        class="flex flex-none items-center gap-0.5 rounded px-1 py-px text-[0.625rem] font-bold leading-tight"
        :class="table
            ? 'bg-primary-600 text-white'
            : 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200'"
        :title="table ? till.tableLabel(table) : till.t('floor.no-table')"
    >
        <svg v-if="table" class="size-3 flex-none" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M2 5.75A.75.75 0 0 1 2.75 5h14.5a.75.75 0 0 1 0 1.5h-.75v8.75a.75.75 0 0 1-1.5 0V11h-10v4.25a.75.75 0 0 1-1.5 0V6.5h-.75A.75.75 0 0 1 2 5.75ZM5 6.5v3h10v-3H5Z" clip-rule="evenodd" />
        </svg>

        <svg v-else class="size-3 flex-none" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M6 5v1H4.667a1.75 1.75 0 0 0-1.743 1.598l-.826 9.5A1.75 1.75 0 0 0 3.84 19H16.16a1.75 1.75 0 0 0 1.743-1.902l-.826-9.5A1.75 1.75 0 0 0 15.333 6H14V5a4 4 0 0 0-8 0Zm4-2.5A2.5 2.5 0 0 0 7.5 5v1h5V5A2.5 2.5 0 0 0 10 2.5ZM7.5 10a2.5 2.5 0 0 0 5 0V8.75a.75.75 0 0 1 1.5 0V10a4 4 0 0 1-8 0V8.75a.75.75 0 0 1 1.5 0V10Z" clip-rule="evenodd" />
        </svg>

        <span v-if="table">{{ table.table_number }}</span>
    </span>
</template>
