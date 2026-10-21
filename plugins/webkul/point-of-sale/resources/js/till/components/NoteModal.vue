<script setup>
import { inject, computed } from 'vue'
import TillModal from './TillModal.vue'

const till = inject('till')

const state = till.state

const presets = computed(() => till.master.notes.all())

const productName = computed(() => {
    const line = till.activeLine

    return line ? (till.master.products.get(line.product_id)?.name ?? '') : ''
})

function chipStyle(note) {
    if (!note.color) {
        return null
    }

    return { backgroundColor: `${note.color}1f`, borderColor: note.color, color: note.color }
}
</script>

<template>
    <TillModal :heading="till.noteLabel" :description="productName" :open="state.noteModalOpen" width="max-w-xl" @close="till.closeNotes()">

        <div class="flex min-h-0 flex-auto flex-col gap-3">
            <div v-if="presets.length" class="flex flex-wrap gap-2">
                <button
                    v-for="note in presets"
                    :key="note.id"
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-sm font-medium transition-colors"
                    :class="note.color
                        ? ''
                        : (till.isNoteSelected(note.name)
                            ? 'border-primary-600 bg-primary-50 text-primary-700 dark:border-primary-500 dark:bg-primary-500/15 dark:text-primary-300'
                            : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 dark:hover:border-gray-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300')"
                    :style="note.color ? chipStyle(note) : null"
                    @click="till.toggleNote(note.id)"
                >
                    <svg v-if="till.isNoteSelected(note.name)" class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                    </svg>

                    {{ note.name }}
                </button>
            </div>

            <textarea
                v-model="state.noteDraft"
                rows="4"
                :placeholder="till.t('notes.placeholder')"
                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-950 placeholder:text-gray-400 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
            ></textarea>

            <div class="flex gap-2">
                <button
                    type="button"
                    class="flex min-h-11 flex-1 items-center justify-center rounded-lg border border-gray-200 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                    @click="state.noteDraft = ''"
                >
                    {{ till.t('common.clear') }}
                </button>

                <button
                    type="button"
                    class="flex min-h-11 flex-2 items-center justify-center rounded-lg bg-primary-600 text-sm font-semibold text-white hover:bg-primary-700"
                    @click="till.applyNote()"
                >
                    {{ till.t('common.apply') }}
                </button>
            </div>
        </div>
    </TillModal>
</template>
