<script setup>
import { inject, ref, computed } from 'vue'

const till = inject('till')

const state = till.state

const element = document.getElementById('pos-till')

const brand = {
    logo: element?.dataset.brandLogo || '',
    backOffice: element?.dataset.backOfficeUrl || '#',
    logout: element?.dataset.logoutUrl || '#',
    userName: element?.dataset.userName || '',
    userAvatar: element?.dataset.userAvatar || '',
}

const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''

const menuOpen = ref(false)

const initials = computed(() => brand.userName
    .split(' ')
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0]?.toUpperCase() ?? '')
    .join(''))

const orderCount = computed(() => till.drafts.filter((order) => till.orderHasContent(order)).length)

function goHome() {
    state.historyOpen = false

    if (till.isRestaurant) {
        till.goToFloor()
    } else {
        till.goToProducts()
    }
}

function installApp() {
    menuOpen.value = false

    window.pointOfSaleInstall?.({
        title: till.t('install.label'),
        installed: till.t('install.installed'),
        unavailable: till.t('install.unavailable'),
    })
}

function openOrders() {
    menuOpen.value = false

    till.openHistory()
}

function openBackOffice() {
    menuOpen.value = false

    if (brand.backOffice && brand.backOffice !== '#') {
        window.location.href = brand.backOffice
    }
}

function openCashMovement() {
    menuOpen.value = false

    till.openCash('in')
}

function openCloseRegister() {
    menuOpen.value = false

    till.openClosing()
}

function toggleTheme() {
    const dark = document.documentElement.classList.toggle('dark')

    window.localStorage.setItem('theme', dark ? 'dark' : 'light')
}
</script>

<template>
    <header class="relative z-30 flex flex-none items-center gap-2 border-b border-gray-200 bg-white px-2 py-2 dark:border-white/10 dark:bg-gray-900 sm:gap-4 sm:px-4">
        <a :href="brand.backOffice" class="hidden flex-none items-center sm:flex">
            <img v-if="brand.logo" :src="brand.logo" alt="" class="h-8 w-auto">
        </a>

        <nav class="flex min-w-0 items-center gap-0.5 sm:gap-1">
            <a
                :href="till.boot.session.registers_url"
                class="inline-flex items-center gap-1 rounded-lg px-1.5 py-2 text-xs font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5 sm:gap-1.5 sm:px-3 sm:text-sm"
            >
                <svg class="size-5 flex-none" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M2 4.25A2.25 2.25 0 0 1 4.25 2h11.5A2.25 2.25 0 0 1 18 4.25v8.5A2.25 2.25 0 0 1 15.75 15h-3.105a3.5 3.5 0 0 0 .577 1.124c.25.32.1.876-.302.876H7.08c-.402 0-.552-.556-.302-.876A3.5 3.5 0 0 0 7.355 15H4.25A2.25 2.25 0 0 1 2 12.75v-8.5Zm1.5 0v7.5c0 .414.336.75.75.75h11.5a.75.75 0 0 0 .75-.75v-7.5a.75.75 0 0 0-.75-.75H4.25a.75.75 0 0 0-.75.75Z" />
                </svg>

                {{ till.t('nav.registers') }}
            </a>

            <button
                type="button"
                class="inline-flex items-center gap-1 rounded-lg px-1.5 py-2 text-xs font-medium sm:gap-1.5 sm:px-3 sm:text-sm"
                :class="!state.historyOpen
                    ? 'bg-primary-50 text-primary-700 dark:bg-primary-500/15 dark:text-primary-300'
                    : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5'"
                @click="goHome()"
            >
                <svg class="size-5 flex-none" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10.707 2.293a1 1 0 0 0-1.414 0l-7 7a1 1 0 0 0 1.414 1.414L4 10.414V17a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-3h2v3a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-6.586l.293.293a1 1 0 0 0 1.414-1.414l-7-7Z" />
                </svg>

                {{ till.t('nav.home') }}
            </button>
        </nav>

        <div class="ml-auto flex items-center gap-2 sm:gap-3">
            <div id="pos-status-slot" class="flex items-center gap-1"></div>

            <div class="relative">
                <button
                    type="button"
                    class="flex size-9 items-center justify-center rounded-full bg-primary-600 text-sm font-semibold uppercase leading-none text-white transition-colors hover:bg-primary-700"
                    @click.stop="menuOpen = !menuOpen"
                >
                    {{ initials || '?' }}
                </button>

            <div v-if="menuOpen" class="fixed inset-0 z-40" @click="menuOpen = false"></div>

            <div
                v-if="menuOpen"
                class="absolute right-0 z-50 mt-2 w-64 overflow-hidden rounded-xl border border-gray-200 bg-white py-1 shadow-lg ring-1 ring-gray-950/5 dark:border-white/10 dark:bg-gray-900 dark:ring-white/10"
            >
                <p v-if="brand.userName" class="truncate border-b border-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-950 dark:border-white/10 dark:text-white">{{ brand.userName }}</p>

                <button
                    type="button"
                    class="flex w-full items-center gap-3 whitespace-nowrap px-4 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-white/5"
                    @click="openOrders()"
                >
                    <svg class="size-5 shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4 2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H4Zm2 4a1 1 0 0 0 0 2h8a1 1 0 1 0 0-2H6Zm0 4a1 1 0 1 0 0 2h8a1 1 0 1 0 0-2H6Zm0 4a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2H6Z" clip-rule="evenodd" /></svg>

                    <span class="flex-1">{{ till.t('nav.orders') }}</span>

                    <span
                        v-if="orderCount"
                        class="inline-flex min-w-5 items-center justify-center rounded-full bg-primary-600 px-1.5 text-xs font-semibold text-white"
                    >{{ orderCount }}</span>
                </button>

                <button
                    type="button"
                    class="flex w-full items-center gap-3 whitespace-nowrap px-4 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-white/5"
                    @click="openCashMovement()"
                >
                    <svg class="size-5 shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-11.25a.75.75 0 0 0-1.5 0v.316a3 3 0 0 0-.863.27c-.665.336-1.387.99-1.387 1.964 0 .963.71 1.609 1.365 1.955.182.096.374.176.57.24v2.011a2.1 2.1 0 0 1-.246-.106c-.33-.17-.45-.339-.45-.439a.75.75 0 0 0-1.5 0c0 .974.722 1.628 1.387 1.964.274.138.57.234.863.27v.316a.75.75 0 0 0 1.5 0v-.316a3 3 0 0 0 .863-.27c.665-.336 1.387-.99 1.387-1.964 0-.963-.71-1.609-1.365-1.955a4 4 0 0 0-.57-.24V8.126c.085.03.166.065.246.106.33.17.45.339.45.439a.75.75 0 0 0 1.5 0c0-.974-.722-1.628-1.387-1.964a3 3 0 0 0-.863-.27V6.75Z" clip-rule="evenodd" /></svg>

                    {{ till.t('menu.cash-in-out') }}
                </button>

                <button
                    type="button"
                    class="flex w-full items-center gap-3 whitespace-nowrap px-4 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-white/5"
                    @click="installApp()"
                >
                    <svg class="size-5 shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 2.75a.75.75 0 0 0-1.5 0v8.614L6.295 8.235a.75.75 0 1 0-1.09 1.03l4.25 4.5a.75.75 0 0 0 1.09 0l4.25-4.5a.75.75 0 0 0-1.09-1.03l-2.955 3.129V2.75Z" /><path d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z" /></svg>

                    {{ till.t('menu.install') }}
                </button>

                <button
                    type="button"
                    class="flex w-full items-center gap-3 whitespace-nowrap px-4 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-white/5"
                    @click="menuOpen = false; toggleTheme()"
                >
                    <svg class="size-5 shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 2ZM10 15a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-1.5 0v-1.5A.75.75 0 0 1 10 15ZM10 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6ZM15.657 5.404a.75.75 0 1 0-1.06-1.06l-1.061 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM6.464 14.596a.75.75 0 1 0-1.06-1.06l-1.06 1.06a.75.75 0 0 0 1.06 1.06l1.06-1.06ZM18 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 18 10ZM5 10a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 5 10ZM14.596 15.657a.75.75 0 0 0 1.06-1.06l-1.06-1.061a.75.75 0 1 0-1.06 1.06l1.06 1.06ZM5.404 6.464a.75.75 0 0 0 1.06-1.06l-1.06-1.06a.75.75 0 1 0-1.061 1.06l1.06 1.06Z" /></svg>

                    {{ till.t('menu.theme') }}
                </button>

                <button
                    type="button"
                    class="flex w-full items-center gap-3 whitespace-nowrap px-4 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-white/5"
                    @click="openBackOffice()"
                >
                    <svg class="size-5 shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 0 0-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 0 0 .75-.75v-4a.75.75 0 0 1 1.5 0v4A2.25 2.25 0 0 1 12.75 17h-8.5A2.25 2.25 0 0 1 2 14.75v-8.5A2.25 2.25 0 0 1 4.25 4h5a.75.75 0 0 1 0 1.5h-5Z" clip-rule="evenodd" /><path fill-rule="evenodd" d="M6.194 12.753a.75.75 0 0 0 1.06.053L16.5 4.44v2.81a.75.75 0 0 0 1.5 0v-4.5a.75.75 0 0 0-.75-.75h-4.5a.75.75 0 0 0 0 1.5h2.553l-9.056 8.194a.75.75 0 0 0-.053 1.06Z" clip-rule="evenodd" /></svg>

                    {{ till.t('menu.back-office') }}
                </button>

                <button
                    type="button"
                    class="flex w-full items-center gap-3 whitespace-nowrap px-4 py-2.5 text-left text-sm font-medium text-danger-600 hover:bg-danger-50 dark:text-danger-400 dark:hover:bg-danger-500/10"
                    @click="openCloseRegister()"
                >
                    <svg class="size-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" /></svg>

                    {{ till.t('menu.close-register') }}
                </button>

                <div class="my-1 border-t border-gray-100 dark:border-white/10"></div>

                <form :action="brand.logout" method="POST">
                    <input type="hidden" name="_token" :value="csrf">

                    <button
                        type="submit"
                        class="flex w-full items-center gap-3 whitespace-nowrap px-4 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-white/5"
                    >
                        <svg class="size-5 shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z" clip-rule="evenodd" /><path fill-rule="evenodd" d="M6 10a.75.75 0 0 1 .75-.75h9.546l-1.048-.943a.75.75 0 1 1 1.004-1.114l2.5 2.25a.75.75 0 0 1 0 1.114l-2.5 2.25a.75.75 0 1 1-1.004-1.114l1.048-.943H6.75A.75.75 0 0 1 6 10Z" clip-rule="evenodd" /></svg>

                        {{ till.t('menu.logout') }}
                    </button>
                </form>
            </div>
            </div>
        </div>
    </header>
</template>
