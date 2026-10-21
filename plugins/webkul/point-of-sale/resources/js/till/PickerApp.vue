<script setup>
import { inject, reactive, ref, computed, onMounted, onBeforeUnmount } from 'vue'

const picker = inject('picker')

const state = picker.state

const registers = computed(() => state.registers)

const element = document.getElementById('pos-till')

const links = {
    backOffice: element?.dataset.backOfficeUrl ?? '#',
    logout: element?.dataset.logoutUrl ?? '#',
    brandLogo: element?.dataset.brandLogo ?? '',
}

const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''

const clock = reactive({ time: '', date: '' })

const idle = ref(false)

const IDLE_MS = 120000

const activityEvents = ['mousemove', 'mousedown', 'keydown', 'touchstart', 'wheel', 'scroll']

let timer = null

let idleTimer = null

function resetIdle() {
    if (idle.value) {
        idle.value = false
    }

    if (idleTimer) {
        window.clearTimeout(idleTimer)
    }

    idleTimer = window.setTimeout(() => {
        idle.value = true
    }, IDLE_MS)
}

function tick() {
    const now = new Date()

    clock.time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' })
    clock.date = now.toLocaleDateString([], { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
}

function toggleDiscard(register) {
    state.confirmingConfigId = state.confirmingConfigId === register.id ? null : register.id
}

function handleOnline() {
    state.offline = false

    picker.load()
}

function handleOffline() {
    state.offline = true
}

onMounted(() => {
    tick()

    timer = window.setInterval(tick, 1000)

    resetIdle()

    activityEvents.forEach((name) => window.addEventListener(name, resetIdle, { passive: true }))

    window.addEventListener('online', handleOnline)
    window.addEventListener('offline', handleOffline)
})

onBeforeUnmount(() => {
    if (timer) {
        window.clearInterval(timer)
    }

    if (idleTimer) {
        window.clearTimeout(idleTimer)
    }

    activityEvents.forEach((name) => window.removeEventListener(name, resetIdle))

    window.removeEventListener('online', handleOnline)
    window.removeEventListener('offline', handleOffline)
})
</script>

<template>
    <div class="fixed inset-0 flex min-h-0 flex-col overflow-hidden bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950">
        <div aria-hidden="true" class="pointer-events-none absolute -left-32 top-8 -z-10 size-96 rounded-full bg-primary-400/20 blur-3xl dark:bg-primary-600/10"></div>

        <div aria-hidden="true" class="pointer-events-none absolute -right-40 bottom-4 -z-10 size-[30rem] rounded-full bg-primary-300/20 blur-3xl dark:bg-primary-500/10"></div>

        <header class="flex flex-none items-start justify-between px-8 py-6">
            <img v-if="links.brandLogo" :src="links.brandLogo" alt="" class="h-10 w-auto opacity-90">

            <div class="text-right">
                <p class="font-mono text-4xl font-light tabular-nums tracking-tight text-gray-950 dark:text-white">{{ clock.time }}</p>

                <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">{{ clock.date }}</p>
            </div>
        </header>

        <div
            v-if="state.offline"
            class="mx-6 mb-2 flex-none rounded-lg border border-warning-300 bg-warning-50 px-4 py-2 text-center text-sm font-medium text-warning-800 dark:border-warning-500/40 dark:bg-warning-500/10 dark:text-warning-300"
        >
            {{ picker.t('picker.offline') }}
        </div>

        <div class="flex min-h-0 flex-auto flex-col items-center justify-center overflow-y-auto px-6 pb-6">
            <div
                v-if="state.error"
                class="mb-6 w-full max-w-md rounded-lg border border-danger-300 bg-danger-50 px-4 py-3 text-center text-sm text-danger-700 dark:border-danger-500/40 dark:bg-danger-500/10 dark:text-danger-300"
            >
                {{ state.error }}
            </div>

            <p v-if="state.loading" class="text-sm text-gray-500 dark:text-gray-400">
                {{ picker.t('picker.opening') }}
            </p>

            <div
                v-else-if="!registers.length"
                class="max-w-md rounded-2xl bg-white p-10 text-center shadow-sm dark:bg-gray-900"
            >
                <p class="text-base font-semibold text-gray-950 dark:text-white">{{ picker.t('picker.empty.heading') }}</p>

                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ picker.t('picker.empty.description') }}</p>
            </div>

            <div v-else class="flex flex-wrap items-stretch justify-center gap-6">
                <div
                    v-for="register in registers"
                    :key="register.id"
                    class="group relative flex w-72 flex-col items-center gap-4 rounded-3xl border border-gray-200/70 bg-white/90 p-8 text-center shadow-sm ring-1 ring-transparent backdrop-blur transition-all duration-200 hover:-translate-y-1 hover:border-primary-200 hover:shadow-xl hover:ring-primary-100 dark:border-white/10 dark:bg-gray-900/80 dark:hover:border-primary-500/40 dark:hover:ring-primary-500/20"
                >
                    <button
                        v-if="register.is_discardable"
                        type="button"
                        class="absolute right-3 top-3 flex size-8 items-center justify-center rounded-lg text-gray-300 opacity-60 transition hover:bg-danger-50 hover:text-danger-600 group-hover:opacity-100 dark:text-gray-600 dark:hover:bg-danger-500/10 dark:hover:text-danger-400"
                        :aria-label="picker.t('picker.discard')"
                        @click="toggleDiscard(register)"
                    >
                        <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443A24 24 0 0 0 3.32 4.5a.75.75 0 0 0 .06 1.499l.46-.02.613 8.58A3 3 0 0 0 7.445 17.5h5.11a3 3 0 0 0 2.992-2.94l.613-8.581.46.02a.75.75 0 1 0 .06-1.5 24 24 0 0 0-2.68-.306V3.75A2.75 2.75 0 0 0 11.25 1h-2.5Z" clip-rule="evenodd" /></svg>
                    </button>

                    <div class="flex size-20 items-center justify-center rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-500/30 transition-transform duration-200 group-hover:scale-105">
                        <svg class="size-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                    </div>

                    <div class="space-y-0.5">
                        <p class="text-lg font-bold text-gray-950 dark:text-white">{{ register.name }}</p>

                        <p v-if="register.code" class="text-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">{{ register.code }}</p>
                    </div>

                    <span
                        v-if="register.live_session"
                        class="inline-flex items-center gap-1.5 rounded-full bg-success-50 px-3 py-1 text-xs font-semibold text-success-700 ring-1 ring-success-200 dark:bg-success-500/10 dark:text-success-300 dark:ring-success-500/30"
                    >
                        <span class="relative flex size-2">
                            <span class="absolute inline-flex size-full animate-ping rounded-full bg-success-400 opacity-75"></span>

                            <span class="relative inline-flex size-2 rounded-full bg-success-500"></span>
                        </span>

                        {{ picker.t('picker.open-by', { name: register.live_session.user ?? '' }) }}
                    </span>

                    <button
                        v-if="state.confirmingConfigId === register.id"
                        type="button"
                        class="mt-1 flex w-full items-center justify-center gap-1.5 rounded-xl bg-danger-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-danger-700 hover:shadow-md disabled:opacity-60"
                        :disabled="state.busyConfigId === register.id"
                        @click="picker.discard(register)"
                    >
                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443A24 24 0 0 0 3.32 4.5a.75.75 0 0 0 .06 1.499l.46-.02.613 8.58A3 3 0 0 0 7.445 17.5h5.11a3 3 0 0 0 2.992-2.94l.613-8.581.46.02a.75.75 0 1 0 .06-1.5 24 24 0 0 0-2.68-.306V3.75A2.75 2.75 0 0 0 11.25 1h-2.5Z" clip-rule="evenodd" /></svg>

                        {{ picker.t('picker.discard-confirm.confirm') }}
                    </button>

                    <button
                        v-else
                        type="button"
                        class="mt-1 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-br from-primary-600 to-primary-700 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:shadow-md hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="state.busyConfigId === register.id"
                        @click="picker.open(register)"
                    >
                        <svg v-if="state.busyConfigId === register.id" class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />

                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.373 0 0 5.373 0 12h4Z" />
                        </svg>

                        <span>{{ state.busyConfigId === register.id
                            ? picker.t('picker.opening')
                            : (register.live_session ? picker.t('picker.resume') : picker.t('picker.open')) }}</span>

                        <svg v-if="state.busyConfigId !== register.id" class="size-4 transition-transform duration-200 group-hover:translate-x-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" /></svg>
                    </button>
                </div>
            </div>
        </div>

        <footer class="mb-6 flex flex-none items-center justify-center gap-3 px-6 pb-10 pt-5">
            <a
                :href="links.backOffice"
                class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800 hover:shadow-md dark:bg-white/10 dark:hover:bg-white/20"
            >
                {{ picker.t('picker.back-office') }}
            </a>

            <form :action="links.logout" method="POST">
                <input type="hidden" name="_token" :value="csrf">

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-medium text-gray-500 transition hover:bg-danger-50 hover:text-danger-600 dark:text-gray-400 dark:hover:bg-danger-500/10 dark:hover:text-danger-400"
                >
                    {{ picker.t('picker.logout') }}
                </button>
            </form>
        </footer>

        <div
            v-if="idle"
            class="fixed inset-0 z-[60] flex cursor-none flex-col items-center justify-center gap-10 bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950"
        >
            <img v-if="links.brandLogo" :src="links.brandLogo" alt="" class="h-48 w-auto opacity-90 sm:h-64">

            <div class="text-center">
                <p class="font-mono text-xl font-light tabular-nums tracking-tight text-gray-950 dark:text-white sm:text-[4rem]">{{ clock.time }}</p>

                <p class="mt-2 text-lg font-medium text-gray-500 dark:text-gray-400 sm:text-2xl">{{ clock.date }}</p>
            </div>
        </div>
    </div>
</template>
