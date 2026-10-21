<script setup>
import { inject } from 'vue'
import OrderTabs from './components/OrderTabs.vue'
import ProductGrid from './components/ProductGrid.vue'
import FloorScreen from './components/FloorScreen.vue'
import GuestsModal from './components/GuestsModal.vue'
import SplitScreen from './components/SplitScreen.vue'
import BillModal from './components/BillModal.vue'
import TipModal from './components/TipModal.vue'
import OrderNameModal from './components/OrderNameModal.vue'
import GlobalDiscountModal from './components/GlobalDiscountModal.vue'
import TableSelectorModal from './components/TableSelectorModal.vue'
import Cart from './components/Cart.vue'
import Numpad from './components/Numpad.vue'
import PaymentPad from './components/PaymentPad.vue'
import PaymentScreen from './components/PaymentScreen.vue'
import ReceiptScreen from './components/ReceiptScreen.vue'
import CustomerModal from './components/CustomerModal.vue'
import OrdersModal from './components/OrdersModal.vue'
import ProductInfoModal from './components/ProductInfoModal.vue'
import NoteModal from './components/NoteModal.vue'
import ProductFormModal from './components/ProductFormModal.vue'
import ActionsModal from './components/ActionsModal.vue'
import PriceListModal from './components/PriceListModal.vue'
import VariantModal from './components/VariantModal.vue'
import LotModal from './components/LotModal.vue'
import MissingLotsModal from './components/MissingLotsModal.vue'
import ShipLaterModal from './components/ShipLaterModal.vue'
import ShipLaterErrorModal from './components/ShipLaterErrorModal.vue'
import RejectedOrderModal from './components/RejectedOrderModal.vue'
import KitchenTicket from './components/KitchenTicket.vue'
import StatusBar from './components/StatusBar.vue'
import Toaster from './components/Toaster.vue'
import OfflineModal from './components/OfflineModal.vue'
import ScannerButton from './components/ScannerButton.vue'
import AppTopbar from './components/AppTopbar.vue'
import OrdersScreen from './components/OrdersScreen.vue'
import OpeningControlModal from './components/OpeningControlModal.vue'
import CashMovementModal from './components/CashMovementModal.vue'
import ClosingModal from './components/ClosingModal.vue'
import MoneyDetailsModal from './components/MoneyDetailsModal.vue'
import DraftOrdersModal from './components/DraftOrdersModal.vue'

const till = inject('till')

const state = till.state
</script>

<template>
    <div class="pos-screen fixed inset-0 flex min-h-0 flex-col">
        <AppTopbar />

        <ScannerButton />

        <StatusBar />

        <OrdersScreen v-if="state.historyOpen" class="min-h-0 flex-auto" />

        <FloorScreen v-else-if="state.screen === 'floor'" class="min-h-0 flex-auto p-4" />

        <SplitScreen v-else-if="state.screen === 'split'" class="min-h-0 flex-auto p-4" />

        <div v-else class="grid h-full min-h-0 flex-auto grid-cols-[1fr] grid-rows-[minmax(0,1fr)] gap-4 p-4 lg:grid-cols-[1fr_minmax(22rem,32%)]">
            <section class="flex min-h-0 min-w-0 flex-col gap-3">
                <div v-if="state.screen === 'products'" class="flex flex-none items-center gap-2">
                    <div class="relative min-w-0 flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-gray-400">
                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 2.95 10.14l3.2 3.21a.75.75 0 1 0 1.06-1.06l-3.21-3.2A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                            </svg>
                        </span>

                        <input
                            v-model="state.search"
                            type="text"
                            :placeholder="till.t('catalogue.search')"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-white pl-11 pr-3 text-sm text-gray-950 placeholder:text-gray-400 focus:border-primary-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                        >
                    </div>

                    <button
                        type="button"
                        class="flex size-10 flex-none items-center justify-center rounded-lg bg-primary-600 text-white transition-colors hover:bg-primary-700"
                        :title="till.t('catalogue.create-product')"
                        :aria-label="till.t('catalogue.create-product')"
                        @click="till.openProductForm()"
                    >
                        <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                        </svg>
                    </button>

                    <template v-if="till.isRestaurant">
                        <button
                            type="button"
                            class="ml-auto flex h-10 flex-none items-center gap-1.5 rounded-lg border border-primary-600 bg-primary-600 px-3 text-xs font-semibold text-white transition-colors hover:bg-primary-700 dark:border-primary-500"
                            :title="till.t('floor.back')"
                            @click="till.goToFloor()"
                        >
                            <svg class="size-4 flex-none" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.25 2A2.25 2.25 0 0 0 2 4.25v2.5A2.25 2.25 0 0 0 4.25 9h2.5A2.25 2.25 0 0 0 9 6.75v-2.5A2.25 2.25 0 0 0 6.75 2h-2.5Zm0 9A2.25 2.25 0 0 0 2 13.25v2.5A2.25 2.25 0 0 0 4.25 18h2.5A2.25 2.25 0 0 0 9 15.75v-2.5A2.25 2.25 0 0 0 6.75 11h-2.5Zm9-9A2.25 2.25 0 0 0 11 4.25v2.5A2.25 2.25 0 0 0 13.25 9h2.5A2.25 2.25 0 0 0 18 6.75v-2.5A2.25 2.25 0 0 0 15.75 2h-2.5Zm0 9A2.25 2.25 0 0 0 11 13.25v2.5A2.25 2.25 0 0 0 13.25 18h2.5A2.25 2.25 0 0 0 18 15.75v-2.5A2.25 2.25 0 0 0 15.75 11h-2.5Z" clip-rule="evenodd" />
                            </svg>

                            <span class="truncate">{{ till.activeTable ? till.tableLabel(till.activeTable) : till.t('floor.back') }}</span>
                        </button>

                        <button
                            type="button"
                            class="flex h-10 flex-none items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 text-xs font-semibold text-gray-700 transition-colors hover:border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:border-gray-600"
                            :title="till.t('table-selector.label')"
                            @click="till.openTableSelector()"
                        >
                            <svg class="size-4 flex-none" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
                            </svg>

                            <span class="truncate">{{ till.t('table-selector.label') }}</span>
                        </button>
                    </template>
                </div>

                <ProductGrid v-if="state.screen === 'products'" />

                <PaymentScreen v-else-if="state.screen === 'payment'" />

                <ReceiptScreen v-else-if="state.screen === 'receipt'" />
            </section>

            <aside class="flex min-h-0 min-w-0 flex-col gap-[clamp(0.375rem,1vh,0.75rem)] overflow-y-auto">
                <OrderTabs />

                <Cart class="flex-auto" />

                <Numpad v-if="state.screen === 'products'" class="flex-none" />

                <PaymentPad v-else-if="state.screen === 'payment'" class="flex-none" />
            </aside>
        </div>

        <CustomerModal />

        <OrdersModal />

        <ProductInfoModal />

        <NoteModal />

        <ProductFormModal />

        <ActionsModal />

        <PriceListModal />

        <VariantModal />

        <LotModal />

        <MissingLotsModal />

        <ShipLaterModal />

        <ShipLaterErrorModal />

        <GuestsModal />

        <BillModal />

        <TipModal />

        <OrderNameModal />

        <GlobalDiscountModal />

        <TableSelectorModal />

        <RejectedOrderModal />

        <KitchenTicket />

        <OpeningControlModal />

        <ClosingModal />

        <CashMovementModal />

        <MoneyDetailsModal />

        <DraftOrdersModal />

        <OfflineModal />

        <Toaster />

        <div
            v-if="state.sessionClosed"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-950/60 p-4"
            role="alertdialog"
            aria-modal="true"
        >
            <div class="w-full max-w-md rounded-xl bg-white p-6 text-center shadow-xl dark:bg-gray-900">
                <p class="text-lg font-semibold text-gray-950 dark:text-white">{{ till.t('session-closed.heading') }}</p>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ till.t('session-closed.body') }}</p>

                <button
                    type="button"
                    class="mt-5 inline-flex min-h-11 items-center justify-center rounded-lg bg-primary-600 px-6 text-sm font-semibold text-white hover:bg-primary-700"
                    @click="till.cancelOpening()"
                >
                    {{ till.t('session-closed.back') }}
                </button>
            </div>
        </div>
    </div>
</template>
