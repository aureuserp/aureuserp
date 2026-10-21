import { reactive, computed, watch, nextTick } from 'vue'
import { computeAll } from '../tax/computer.js'
import { floatRound, floatCompare, floatIsZero } from '../tax/float.js'
import { TillDatabase } from './indexed-db.js'
import { createRecordSets, MASTER_INDEXES, TRANSACTIONAL_MODELS } from './records.js'
import { SyncQueue } from './sync-queue.js'

const PERSIST_DEBOUNCE = 200

const DATABASE_VERSION = 1

const BUFFER_MAX_LENGTH = 12

const KEYBOARD_BURST_WINDOW = 100

const KEYBOARD_BURST_LIMIT = 2

const FLOOR_IDLE_TIMEOUT = 180000

const DRAFT_REFRESH_INTERVAL = 20000

const TABLE_REFRESH_TIMEOUT = 1500

const BUFFER_KEYS = new Set(['Backspace', 'Delete', '+', '-', '.', ',', ...'0123456789'.split('')])

const MODIFIER_KEYS = new Set(['Shift', 'Control', 'Alt', 'Meta', 'AltGraph', 'CapsLock'])

function isBufferEmpty(buffer) {
    return buffer === '' || buffer === null
}

function nextBuffer(buffer, key, fresh = false) {
    if ((key === 'Backspace' || key === 'Delete') && fresh) {
        return ''
    }

    const current = buffer ?? ''

    let next = current

    if (key === '.' || key === ',') {
        if (isBufferEmpty(buffer)) {
            next = '0.'
        } else if (current === '-') {
            next = '-0.'
        } else if (!current.includes('.')) {
            next = `${current}.`
        }
    } else if (key === 'Delete') {
        next = isBufferEmpty(buffer) ? null : ''
    } else if (key === 'Backspace') {
        next = isBufferEmpty(buffer)
            ? null
            : current.slice(0, current.endsWith('.') ? -2 : -1)
    } else if (key === '+') {
        next = current.startsWith('-') ? current.slice(1) : current
    } else if (key === '-') {
        if (isBufferEmpty(buffer)) {
            next = '-0'
        } else {
            next = current.startsWith('-') ? current.slice(1) : `-${current}`
        }
    } else if (/^\+\d+(\.\d+)?$/.test(key)) {
        const bumped = Number(key.slice(1)) + (Number(current) || 0)

        next = String(floatRound(bumped, { precisionDigits: 4 }))
    } else if (/^\d$/.test(key)) {
        if (isBufferEmpty(buffer)) {
            next = key
        } else if (current.length <= BUFFER_MAX_LENGTH) {
            next = `${current}${key}`
        }
    }

    return next === '-' ? '' : next
}

function uuidv4() {
    if (window.crypto?.randomUUID) {
        return window.crypto.randomUUID()
    }

    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (char) => {
        const random = (Math.random() * 16) | 0
        const value = char === 'x' ? random : (random & 0x3) | 0x8

        return value.toString(16)
    })
}

export class Till {
    constructor(boot, { syncEndpoint, accessToken }) {
        this.boot = boot
        this.syncEndpoint = syncEndpoint
        this.accessToken = accessToken
        this.deferredEvictions = []
        this.draftEpoch = 0
        this.draftPullEpoch = -1
        this.removedDraftEpochs = new Map()
        this.savingDrafts = new Set()
        this.releasedWhileSaving = new Set()

        this.master = createRecordSets(
            Object.fromEntries(Object.entries(MASTER_INDEXES).map(([name, indexes]) => [name, { key: 'id', indexes }])),
        )

        this.state = reactive({
            ready: false,
            screen: 'products',
            search: '',
            categoryId: null,
            orders: [],
            activeOrderUuid: null,
            activeLineUuid: null,
            floorId: boot.floors?.[0]?.id ?? null,
            guestsModalOpen: false,
            guestsDraft: 0,
            splitQuantities: {},
            autoPrintOrderUuid: null,
            globalDiscountModalOpen: false,
            transferOrderUuid: null,
            transferWasBooked: false,
            kitchenTicket: null,
            sessionClosed: false,
            planEditing: false,
            planDraft: null,
            planSelectedKey: null,
            planSaving: false,
            planError: null,
            planConfirmDelete: false,
            floorView: 'map',
            openingTable: false,
            transferError: null,
            tableSelectorOpen: false,
            tableSelectorDraft: '',
            globalDiscountDraft: '',
            billModalOpen: false,
            tipModalOpen: false,
            tipDraft: '',
            orderNameModalOpen: false,
            orderNameDraft: '',
            numpadMode: 'qty',
            numpadBuffer: '',
            numpadFresh: true,
            priceListId: boot.config.price_list_id ?? null,
            fiscalPositionId: boot.config.fiscal_position_id ?? null,
            isTakeaway: false,
            toInvoice: false,
            databaseUnavailable: false,
            sequenceNumber: boot.session.sequence_number ?? 0,
            customerModalOpen: false,
            customerSearch: '',
            productInfoId: null,
            ordersModalOpen: false,
            ordersSearch: '',
            noteModalOpen: false,
            noteDraft: '',
            activePaymentUuid: null,
            paymentBuffer: '',
            paymentFresh: true,
            productModalOpen: false,
            actionsModalOpen: false,
            cancellingOrder: false,
            cancelError: null,
            variantProductId: null,
            lotLineUuid: null,
            lotRows: [],
            lotError: null,
            lotWarningOpen: false,
            cameraScanning: false,
            cameraError: null,
            variantSelection: {},
            priceListModalOpen: false,
            shipLaterModalOpen: false,
            shipLaterDraft: '',
            shipLaterError: null,
            productSaving: false,
            productError: null,
            rememberedOrderUnavailable: false,
            openingOpen: boot.session.state === 'opening_control',
            openingCash: Number(boot.session.previous_closing_balance ?? boot.session.cash_register_balance_start ?? 0),
            openingNotes: '',
            openingBusy: false,
            openingError: null,
            moneyDetailsOpen: false,
            moneyDetailsTarget: 'opening',
            moneyDetails: {},
            cashOpen: false,
            cashType: 'in',
            cashAmount: '',
            cashReason: '',
            cashBusy: false,
            cashError: null,
            showCashMoves: false,
            closingOpen: false,
            closingReport: null,
            closingBusy: false,
            closingError: null,
            closingNotes: '',
            closingCashReal: '',
            closingCounted: {},
            closingDrafts: false,
            closingUnbalanced: null,
            balancingAccountId: null,
            historyOpen: false,
            history: [],
            historyLoading: false,
            historyError: null,
            historySearch: '',
            historyStatus: 'active',
            historyPage: 1,
            historyLastPage: 1,
            historySelected: null,
            historyBusyId: null,
            invoiceCustomerOrder: null,
            invoiceCustomerStep: null,
            refundLines: {},
            refundActiveLine: null,
            refundBuffer: '',
            refundError: null,
            offlineAcknowledged: false,
            toasts: [],
        })

        this.database = new TillDatabase(
            `pos-config-${boot.config.id}-${accessToken}`,
            DATABASE_VERSION,
            Object.entries(TRANSACTIONAL_MODELS).map(([name, options]) => [options.key, name]),
            { onFatal: () => { this.state.databaseUnavailable = true } },
        )

        this.queue = new SyncQueue(this.database, { endpoint: syncEndpoint })

        this.loadMaster(boot)
    }

    loadMaster(boot) {
        this.master.products.putMany(boot.products ?? [])
        this.master.partners.putMany(boot.partners ?? [])
        this.master.taxes.putMany(boot.taxes ?? [])
        this.master.categories.putMany(boot.categories ?? [])
        this.master.payment_methods.putMany(boot.payment_methods ?? [])
        this.master.price_lists.putMany(boot.price_lists ?? [])
        this.master.fiscal_positions.putMany(boot.fiscal_positions ?? [])
        this.master.bills.putMany(boot.bills ?? [])
        this.master.notes.putMany(boot.notes ?? [])
        this.master.uoms.putMany(boot.uoms ?? [])
        this.master.currencies.putMany(boot.currencies ?? [])

        this.stock = { ...(boot.stock ?? {}) }
        this.prices = boot.prices ?? { 0: {} }
        this.priceTiers = boot.price_tiers ?? {}

        this.floors = reactive(boot.floors ?? [])

        this.indexTables()

        this.variantsByProduct = new Map((boot.variants ?? []).map((entry) => [entry.product_id, entry]))

        this.lotsByProduct = (boot.lots ?? []).reduce((carry, lot) => {
            carry.set(lot.product_id, [...(carry.get(lot.product_id) ?? []), lot])

            return carry
        }, new Map())

        this.taxById = new Map((boot.taxes ?? []).map((tax) => [tax.id, tax]))

        for (const tax of this.taxById.values()) {
            tax.children = (tax.children_tax_ids ?? []).map((id) => this.taxById.get(id)).filter(Boolean)
        }
    }

    async start() {
        await this.restore()

        this.mergeServerOrders()

        this.dropSettledOrders()

        const refund = this.openPendingRefund()

        this.activateDraft(this.rememberedOrderUuid())

        if (refund) {
            this.state.activeOrderUuid = refund.uuid
            this.state.screen = 'products'
        } else if (this.isRestaurant) {
            this.goToFloor()
        }

        this.startMenuListeners()

        this.watchActiveOrder()

        this.queue.watch()

        this.onQueueFlushed = (event) => this.evictSynced(event.detail.uuids ?? [])

        window.addEventListener('point-of-sale:queue-flushed', this.onQueueFlushed)

        this.watchPersistence()

        this.startWedge()

        this.startKeyboard()

        this.startIdleWatch()

        this.loadFloorView()

        this.startDraftRefresh()

        this.state.ready = true
    }

    async restore() {
        const stored = await this.database.readAll(Object.keys(TRANSACTIONAL_MODELS))

        const orders = stored['pos.order'] ?? []
        const lines = stored['pos.order.line'] ?? []
        const payments = stored['pos.payment'] ?? []

        const byOrder = new Map(orders.map((order) => [order.uuid, { ...order, lines: [], payments: [] }]))

        for (const line of lines) {
            byOrder.get(line.order_uuid)?.lines.push(line)
        }

        for (const payment of payments) {
            byOrder.get(payment.order_uuid)?.payments.push(payment)
        }

        const restored = [...byOrder.values()]

        const prefix = this.nextReference(0).slice(0, -4)

        for (const order of restored) {
            if (order.state === 'draft' && !order.serverId && order.pos_reference && !order.pos_reference.startsWith(prefix)) {
                order.pos_reference = null
                order.tracking_number = null
                order.sequence_number = null
            }
        }

        const highest = [...restored, ...(this.boot.orders ?? [])]
            .filter((order) => order.pos_reference?.startsWith(prefix))
            .reduce((carry, order) => {
                const number = parseInt(order.pos_reference.slice(prefix.length), 10)

                return Number.isNaN(number) ? carry : Math.max(carry, number)
            }, this.state.sequenceNumber)

        this.state.sequenceNumber = highest

        const seen = new Set([
            ...(this.boot.orders ?? []).map((order) => order.pos_reference),
            ...restored.filter((order) => order.serverId).map((order) => order.pos_reference),
        ])

        for (const order of restored) {
            order.sequence_number ??= this.sequenceFromReference(order.pos_reference) ?? this.nextSequenceNumber()

            order.tracking_number ??= this.trackingNumberFor(order.sequence_number)

            order.pos_reference ||= this.nextReference(order.sequence_number)

            if (!order.serverId && seen.has(order.pos_reference)) {
                order.sequence_number = this.nextSequenceNumber()

                order.tracking_number = this.trackingNumberFor(order.sequence_number)

                order.pos_reference = this.nextReference(order.sequence_number)
            }

            seen.add(order.pos_reference)

            this.state.orders.push(reactive(order))
        }

        return restored
    }

    mergeServerOrders() {
        const removed = new Set(this.removedDrafts())

        const serverOrders = (this.boot.orders ?? []).filter((order) => !removed.has(order.uuid))

        const serverUuids = new Set(serverOrders.map((order) => order.uuid))

        const stale = this.state.orders.filter((order) => order.serverId && order.state === 'draft' && !serverUuids.has(order.uuid))

        for (const order of stale) {
            this.database.remove('pos.order.line', order.lines.map((line) => line.uuid))
            this.database.remove('pos.payment', order.payments.map((payment) => payment.uuid))
            this.database.remove('pos.order', [order.uuid])
        }

        const staleUuids = new Set(stale.map((order) => order.uuid))

        this.state.orders = this.state.orders.filter((order) => !staleUuids.has(order.uuid))

        const localUuids = new Set(this.state.orders.map((order) => order.uuid))

        for (const order of serverOrders) {
            if (!localUuids.has(order.uuid)) {
                this.state.orders.push(this.hydrateSyncedDraft(order))
            }
        }
    }

    openPendingRefund(pending = this.boot.pending_refund) {
        if (!pending?.lines?.length) {
            return null
        }

        for (const product of pending.products ?? []) {
            if (!this.master.products.get(product.id)) {
                this.master.products.put(product)
            }
        }

        const destination = this.emptyOrderFor(pending.partner_id) ?? this.newOrder()

        destination.partner_id = pending.partner_id ?? destination.partner_id
        destination.price_list_id = pending.price_list_id ?? destination.price_list_id
        destination.fiscal_position_id = pending.fiscal_position_id ?? destination.fiscal_position_id
        destination.refunded_order_id = pending.refunded_order_id
        destination.locked_partner_id = pending.partner_id ?? null

        for (const line of pending.lines) {
            destination.lines.push(this.hydrateLine({ ...line, price_overridden: true }, destination.uuid))
        }

        return destination
    }

    emptyOrderFor(partnerId) {
        let fallback = null

        for (const order of this.drafts) {
            if (order.lines.length || order.payments.length) {
                continue
            }

            if ((order.partner_id ?? null) === (partnerId ?? null)) {
                return order
            }

            if (!order.partner_id && fallback === null) {
                fallback = order
            }
        }

        return fallback
    }

    hydrateServerOrder(order) {
        const sequenceNumber = order.sequence_number || this.sequenceFromReference(order.pos_reference) || this.nextSequenceNumber()

        return reactive({
            uuid: order.uuid ?? uuidv4(),
            serverId: order.id ?? null,
            sequence_number: sequenceNumber,
            pos_reference: order.pos_reference || this.nextReference(sequenceNumber),
            tracking_number: order.tracking_number || this.trackingNumberFor(sequenceNumber),
            partner_id: order.partner_id ?? null,
            state: order.state ?? 'draft',
            note: order.note ?? '',
            is_takeaway: Boolean(order.is_takeaway),
            table_id: order.table_id ?? null,
            customer_count: Number(order.customer_count ?? 0),
            floating_name: order.floating_name ?? '',
            is_booked: Boolean(order.is_booked),
            preparation_state: order.preparation_state ?? null,
            last_screen: order.last_screen ?? 'products',
            synced_signature: order.synced_signature ?? null,
            rejected_signature: order.rejected_signature ?? null,
            draft_error: order.draft_error ?? null,
            to_invoice: Boolean(order.to_invoice),
            shipped_at: order.shipped_at ?? null,
            price_list_id: order.price_list_id ?? this.state.priceListId,
            fiscal_position_id: order.fiscal_position_id ?? this.state.fiscalPositionId,
            refunded_order_id: order.refunded_order_id ?? null,
            locked_partner_id: order.locked_partner_id ?? null,
            created_at: order.created_at ?? new Date().toISOString(),
            lines: (order.lines ?? []).map((line) => this.hydrateLine(line, order.uuid)),
            payments: (order.payments ?? []).map((payment) => ({
                uuid: payment.uuid ?? uuidv4(),
                order_uuid: order.uuid,
                payment_method_id: payment.payment_method_id,
                amount: Number(payment.amount ?? 0),
                is_change: Boolean(payment.is_change),
            })),
        })
    }

    hydrateLine(line, orderUuid) {
        return {
            uuid: line.uuid ?? uuidv4(),
            order_uuid: orderUuid,
            product_id: line.product_id,
            uom_id: line.uom_id ?? this.master.products.get(line.product_id)?.uom_id ?? null,
            qty: Number(line.qty ?? 1),
            price_unit: Number(line.price_unit ?? 0),
            price_overridden: Boolean(line.price_overridden),
            discount: Number(line.discount ?? 0),
            note: line.note ?? '',
            lots: line.lots ?? [],
            tax_ids: line.tax_ids ?? [],
            refunded_order_line_id: line.refunded_order_line_id ?? null,
        }
    }

    get activeOrderStorageKey() {
        return `pos.active-order.${this.config.id}`
    }

    rememberedOrderUuid() {
        try {
            return window.localStorage.getItem(this.activeOrderStorageKey)
        } catch {
            return null
        }
    }

    watchActiveOrder() {
        this.stopActiveOrderWatch = watch(() => this.state.activeOrderUuid, (uuid) => {
            try {
                if (uuid) {
                    window.localStorage.setItem(this.activeOrderStorageKey, uuid)
                } else {
                    window.localStorage.removeItem(this.activeOrderStorageKey)
                }
            } catch {
                this.state.rememberedOrderUnavailable = true
            }
        }, { immediate: true })
    }

    watchPersistence() {
        this.stopPersistenceWatch = watch(
            () => JSON.stringify(this.state.orders),
            () => {
                window.clearTimeout(this.persistTimer)

                this.persistTimer = window.setTimeout(() => {
                    this.persistTimer = null

                    this.persist()
                }, PERSIST_DEBOUNCE)
            },
            { deep: true },
        )
    }

    async persist() {
        const orders = []
        const lines = []
        const payments = []

        for (const order of this.state.orders) {
            orders.push({
                uuid: order.uuid,
                serverId: order.serverId,
                pos_reference: order.pos_reference,
                tracking_number: order.tracking_number,
                sequence_number: order.sequence_number,
                partner_id: order.partner_id,
                state: order.state,
                note: order.note,
                is_takeaway: order.is_takeaway,
                table_id: order.table_id ?? null,
                customer_count: order.customer_count ?? 0,
                floating_name: order.floating_name ?? '',
                is_booked: Boolean(order.is_booked),
                preparation_state: order.preparation_state ?? null,
                last_screen: order.last_screen ?? 'products',
                synced_signature: order.synced_signature ?? null,
                rejected_signature: order.rejected_signature ?? null,
                draft_error: order.draft_error ?? null,
                to_invoice: order.to_invoice,
                shipped_at: order.shipped_at,
                price_list_id: order.price_list_id,
                fiscal_position_id: order.fiscal_position_id,
                refunded_order_id: order.refunded_order_id ?? null,
                locked_partner_id: order.locked_partner_id ?? null,
                created_at: order.created_at,
            })

            for (const line of order.lines) {
                lines.push({ ...line, order_uuid: order.uuid })
            }

            for (const payment of order.payments) {
                payments.push({ ...payment, order_uuid: order.uuid })
            }
        }

        await this.database.write('pos.order', orders)
        await this.database.write('pos.order.line', lines)
        await this.database.write('pos.payment', payments)
    }

    get rejectionAlert() {
        const pending = this.queue.state.pending.find((entry) => entry.rejected && !entry.acknowledged)

        if (!pending) {
            return null
        }

        const order = this.state.orders.find((entry) => entry.uuid === pending.uuid)

        const methods = [...new Set((pending.payload?.payments ?? [])
            .map((payment) => this.master.payment_methods.get(payment.payment_method_id)?.name)
            .filter(Boolean))]

        return {
            uuid: pending.uuid,
            settledElsewhere: this.queue.isSettledElsewhere(pending),
            order: order ? this.orderLabel(order) : (pending.payload?.reference ?? pending.uuid),
            table: this.tableById.get(pending.payload?.table_id)?.table_number ?? null,
            amount: this.money(Number(pending.payload?.amount_total ?? 0)),
            methods: methods.join(', '),
            message: pending.lastError,
        }
    }

    acknowledgeRejection(uuid) {
        this.queue.acknowledge(uuid)
    }

    dismissRejection(uuid) {
        this.queue.dismiss(uuid)

        this.evictSynced([uuid])
    }

    async retryRejection(uuid) {
        this.queue.acknowledge(uuid)

        await this.queue.retryRejected()
    }

    flushDeferredEvictions() {
        const deferred = this.deferredEvictions

        if (!deferred.length) {
            return
        }

        this.deferredEvictions = []

        this.evictSynced(deferred)
    }

    evictSynced(uuids) {
        if (!uuids.length) {
            return
        }

        const showingReceipt = this.state.screen === 'receipt'

        const evictable = new Set(uuids.filter((uuid) => {
            if (showingReceipt && uuid === this.state.activeOrderUuid) {
                if (!this.deferredEvictions.includes(uuid)) {
                    this.deferredEvictions.push(uuid)
                }

                return false
            }

            return true
        }))

        if (!evictable.size) {
            return
        }

        const removedLines = []
        const removedPayments = []

        this.state.orders = this.state.orders.filter((order) => {
            if (!evictable.has(order.uuid)) {
                return true
            }

            if (order.state === 'draft') {
                return true
            }

            removedLines.push(...order.lines.map((line) => line.uuid))
            removedPayments.push(...order.payments.map((payment) => payment.uuid))

            return false
        })

        this.database.remove('pos.order', [...evictable])
        this.database.remove('pos.order.line', removedLines)
        this.database.remove('pos.payment', removedPayments)

        if (!showingReceipt && this.activeOrder?.state !== 'draft') {
            this.activateDraft()
        }
    }

    dropSettledOrders() {
        const settled = this.state.orders.filter((order) => order.state !== 'draft')

        if (!settled.length) {
            return
        }

        this.database.remove('pos.order.line', settled.flatMap((order) => order.lines.map((line) => line.uuid)))
        this.database.remove('pos.payment', settled.flatMap((order) => order.payments.map((payment) => payment.uuid)))
        this.database.remove('pos.order', settled.map((order) => order.uuid))

        this.state.orders = this.state.orders.filter((order) => order.state === 'draft')
    }

    activateDraft(preferredUuid = null) {
        const drafts = this.drafts

        const preferred = drafts.find((order) => order.uuid === preferredUuid)

        if (this.isRestaurant && !preferred) {
            this.goToFloor()

            return null
        }

        const draft = preferred ?? drafts[0]

        if (!draft) {
            return this.newOrder()
        }

        this.state.activeOrderUuid = draft.uuid

        return draft
    }

    get config() {
        return this.boot.config
    }

    get currency() {
        return this.master.currencies.get(this.config.currency_id)
            ?? this.master.currencies.get(this.boot.company.currency_id)
            ?? { rounding: 0.01, decimal_places: 2, symbol: '', name: '', position: 'before' }
    }

    get roundingMethod() {
        return this.boot.company.tax_calculation_rounding_method ?? 'round_per_line'
    }

    get locale() {
        return this.boot.locale ?? { code: 'en', direction: 'ltr' }
    }

    get intlLocale() {
        return String(this.locale.code || document.documentElement.lang || 'en').replace('_', '-')
    }

    get isRtl() {
        return this.locale.direction === 'rtl'
    }

    t(key, replacements = {}) {
        let line = key.split('.').reduce(
            (branch, segment) => (branch && typeof branch === 'object' ? branch[segment] : undefined),
            this.boot.translations ?? {},
        )

        if (typeof line !== 'string') {
            return key
        }

        for (const [token, value] of Object.entries(replacements)) {
            line = line.replaceAll(`:${token}`, value)
        }

        return line
    }

    choice(key, count, replacements = {}) {
        const line = this.t(key, { ...replacements, count })

        if (!line.includes('|')) {
            return line
        }

        const segments = line.split('|').map((segment) => segment.trim())

        for (const segment of segments) {
            const exact = segment.match(/^\{(-?\d+)\}\s*(.*)$/s)

            if (exact && Number(exact[1]) === count) {
                return exact[2]
            }

            const range = segment.match(/^\[(-?\d+|\*),\s*(-?\d+|\*)\]\s*(.*)$/s)

            if (range) {
                const from = range[1] === '*' ? -Infinity : Number(range[1])
                const to = range[2] === '*' ? Infinity : Number(range[2])

                if (count >= from && count <= to) {
                    return range[3]
                }
            }
        }

        const plain = segments.map((segment) => segment.replace(/^(\{[^}]*\}|\[[^\]]*\])\s*/, ''))

        return count === 1 ? (plain[0] ?? line) : (plain[1] ?? plain[0] ?? line)
    }

    money(amount) {
        const currency = this.currency

        const decimalPlaces = currency.decimal_places ?? 2

        const value = Number(amount ?? 0)

        const formatted = new Intl.NumberFormat(this.intlLocale, {
            minimumFractionDigits: decimalPlaces,
            maximumFractionDigits: decimalPlaces,
        }).format(Math.abs(value))

        const sign = Number(value.toFixed(decimalPlaces)) < 0 ? '-' : ''

        const symbol = currency.symbol ?? ''

        return currency.position === 'after'
            ? `${sign}${formatted} ${symbol}`.trim()
            : `${sign}${symbol}${formatted}`.trim()
    }

    openOrders() {
        this.state.ordersSearch = ''
        this.state.ordersModalOpen = true
    }

    closeOrders() {
        this.state.ordersModalOpen = false
    }

    orderLabel(order) {
        if (order.floating_name) {
            return order.floating_name
        }

        if (order.partner_id) {
            return this.master.partners.get(order.partner_id)?.name ?? order.tracking_number
        }

        return order.tracking_number ?? order.pos_reference
    }

    orderQuantity(order) {
        return order.lines.reduce((carry, line) => carry + line.qty, 0)
    }

    get parkedOrders() {
        if (!this.isRestaurant) {
            return this.drafts
        }

        const tableId = this.activeOrder?.table_id ?? null

        return this.drafts.filter((order) => !order.table_id || order.table_id === tableId)
    }

    searchOrders(term) {
        const needle = term.trim().toLowerCase()

        if (!needle) {
            return this.parkedOrders
        }

        return this.parkedOrders.filter((order) => {
            const haystack = [
                this.orderLabel(order),
                order.pos_reference,
                order.tracking_number,
                this.orderTable(order)?.table_number,
                ...order.lines.map((line) => this.master.products.get(line.product_id)?.name),
            ]

            return haystack.some((field) => field && String(field).toLowerCase().includes(needle))
        })
    }

    cartQuantityByProduct() {
        const quantities = new Map()

        for (const line of this.activeOrder?.lines ?? []) {
            quantities.set(line.product_id, (quantities.get(line.product_id) ?? 0) + line.qty)

            const parentId = this.master.products.get(line.product_id)?.parent_id

            if (parentId) {
                quantities.set(parentId, (quantities.get(parentId) ?? 0) + line.qty)
            }
        }

        return quantities
    }

    openProductInfo(productId) {
        this.state.productInfoId = productId
    }

    closeProductInfo() {
        this.state.productInfoId = null
    }

    productInfo() {
        const product = this.master.products.get(this.state.productInfoId)

        if (!product) {
            return null
        }

        const price = this.priceFor(product.id)

        const cost = Number(product.cost ?? 0)

        const quantity = this.cartQuantityByProduct().get(product.id) ?? 0

        const cartPrice = quantity ? this.priceFor(product.id, quantity) : price

        return {
            id: product.id,
            name: product.name,
            reference: product.reference,
            barcode: product.barcode,
            image: product.image,
            is_storable: Boolean(product.is_storable),
            available: this.freeQty(product.id),
            price,
            cost,
            margin: price - cost,
            margin_ratio: price ? ((price - cost) / price) * 100 : 0,
            quantity,
            total_price: cartPrice * quantity,
            total_cost: cost * quantity,
            total_margin: (cartPrice - cost) * quantity,
        }
    }

    get noteLabel() {
        return this.t(this.config.is_restaurant ? 'notes.kitchen' : 'notes.internal')
    }

    openNotes() {
        const line = this.activeLine

        if (!line) {
            return
        }

        this.state.noteDraft = String(line.note ?? '')
        this.state.noteModalOpen = true
    }

    closeNotes() {
        this.state.noteModalOpen = false
        this.state.noteDraft = ''
    }

    noteLines() {
        return this.state.noteDraft.split('\n').filter((line) => line !== '')
    }

    noteParts(note) {
        return String(note ?? '')
            .split('\n')
            .filter((part) => part !== '')
            .map((text) => ({
                text,
                color: this.master.notes.find((preset) => preset.name === text)?.color ?? null,
            }))
    }

    isNoteSelected(name) {
        return this.noteLines().includes(name)
    }

    toggleNote(noteId) {
        const name = this.master.notes.get(noteId)?.name

        if (!name) {
            return
        }

        const lines = this.noteLines()

        this.state.noteDraft = lines.includes(name)
            ? lines.filter((line) => line !== name).join('\n')
            : [...lines, name].join('\n')
    }

    applyNote() {
        const line = this.activeLine

        if (!line) {
            return
        }

        line.note = this.state.noteDraft.trim() === '' ? '' : this.state.noteDraft

        this.closeNotes()
    }

    openProductForm() {
        this.state.productError = null
        this.state.productModalOpen = true
    }

    closeProductForm() {
        this.state.productModalOpen = false
        this.state.productError = null
    }

    get productEndpoint() {
        return this.boot.config.product_endpoint
    }

    async createProduct(draft) {
        this.state.productSaving = true
        this.state.productError = null

        try {
            const response = await fetch(this.productEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
                },
                body: JSON.stringify(draft),
            })

            const body = await response.json().catch(() => ({}))

            if (!response.ok) {
                this.state.productError = body.message ?? this.t('product-form.error.failed', { status: response.status })

                return null
            }

            const product = body.data

            this.master.products.put(product)

            const listKey = String(this.state.priceListId ?? 0)

            this.prices[listKey] ??= {}
            this.prices[listKey][String(product.id)] = product.resolved_price ?? product.price

            this.addProduct(product.id)

            this.closeProductForm()

            return product
        } catch (error) {
            this.state.productError = navigator.onLine
                ? error.message
                : this.t('product-form.error.offline')

            return null
        } finally {
            this.state.productSaving = false
        }
    }

    openCustomers() {
        if (this.isCustomerLocked) {
            return
        }

        this.state.customerSearch = ''
        this.state.customerModalOpen = true
    }

    closeCustomers() {
        this.state.customerModalOpen = false
    }

    goToPayment({ ignoreLots = false } = {}) {
        if (!this.activeOrder || !this.sellableLines(this.activeOrder).length) {
            return
        }

        if (!ignoreLots && this.linesMissingLots().length) {
            this.state.lotWarningOpen = true

            return
        }

        this.state.lotWarningOpen = false

        this.activeOrder.last_screen = 'payment'

        this.state.screen = 'payment'
    }

    goToProducts() {
        if (this.activeOrder) {
            this.activeOrder.last_screen = 'products'
        }

        this.state.screen = 'products'
    }

    get activeOrder() {
        return this.state.orders.find((order) => order.uuid === this.state.activeOrderUuid)
    }

    get isCustomerLocked() {
        return Boolean(this.activeOrder?.refunded_order_id) && Boolean(this.activeOrder?.locked_partner_id)
    }

    get activeLine() {
        return this.activeOrder?.lines.find((line) => line.uuid === this.state.activeLineUuid)
    }

    isEditable(order = this.activeOrder) {
        return order?.state === 'draft'
    }

    get drafts() {
        return this.state.orders.filter((order) => order.state === 'draft')
    }

    nextReference(sequence) {
        const session = String(this.boot.session.id).padStart(5, '0')

        const login = String(this.boot.session.login_number ?? 0).padStart(3, '0')

        return `${session}-${login}-${String(sequence).padStart(4, '0')}`
    }

    nextSequenceNumber() {
        this.state.sequenceNumber += 1

        return this.state.sequenceNumber
    }

    sequenceFromReference(reference) {
        const number = parseInt(String(reference ?? '').split('-').at(-1), 10)

        return Number.isNaN(number) || number < 1 ? null : number
    }

    trackingNumberFor(sequence) {
        return String(((this.boot.session.id % 10) * 100) + (sequence % 100)).padStart(3, '0')
    }

    newOrder(tableId = null) {
        this.state.priceListId = this.config.price_list_id ?? null

        const order = this.hydrateServerOrder({
            uuid: uuidv4(),
            lines: [],
            payments: [],
            price_list_id: this.config.price_list_id ?? null,
            fiscal_position_id: this.state.fiscalPositionId,
            is_takeaway: this.state.isTakeaway,
            table_id: this.isRestaurant ? tableId : null,
        })

        this.state.orders.push(order)

        this.state.activeOrderUuid = order.uuid
        this.state.activeLineUuid = null
        this.state.screen = 'products'

        this.resetNumpadBuffer()

        this.flushDeferredEvictions()

        return order
    }

    get preparationPrinters() {
        return this.config.preparation_printers ?? []
    }

    get sendsToKitchen() {
        return this.preparationPrinters.length > 0
    }

    preparationCategoryIds(productId) {
        const ids = new Set()

        for (const categoryId of this.master.products.get(productId)?.category_ids ?? []) {
            let current = categoryId

            while (current && !ids.has(current)) {
                ids.add(current)

                current = this.master.categories.get(current)?.parent_id ?? null
            }
        }

        return ids
    }

    printerTakes(printer, productId) {
        if (!printer.category_ids?.length) {
            return true
        }

        const ids = this.preparationCategoryIds(productId)

        return printer.category_ids.some((id) => ids.has(id))
    }

    preparesLine(line) {
        return !this.isLockedLine(line)
            && this.preparationPrinters.some((printer) => this.printerTakes(printer, line.product_id))
    }

    preparationState(order) {
        return order?.preparation_state ?? { lines: {}, note: '', takeaway: false }
    }

    preparationEntry(line, qty) {
        return {
            uuid: line.uuid,
            product_id: line.product_id,
            name: this.master.products.get(line.product_id)?.name ?? line.name ?? '',
            note: line.note ?? '',
            qty,
        }
    }

    preparationChanges(order) {
        const state = this.preparationState(order)

        const added = []
        const cancelled = []
        const noted = []
        const seen = new Set()

        for (const line of order.lines) {
            if (!this.preparesLine(line)) {
                continue
            }

            seen.add(line.uuid)

            const before = state.lines[line.uuid]

            const difference = floatRound(line.qty - (before?.qty ?? 0), { precisionDigits: 4 })

            if (difference > 0) {
                added.push(this.preparationEntry(line, difference))
            } else if (difference < 0) {
                cancelled.push(this.preparationEntry(line, -difference))
            }

            if (before && (before.note ?? '') !== (line.note ?? '')) {
                noted.push(this.preparationEntry(line, line.qty))
            }
        }

        for (const [uuid, before] of Object.entries(state.lines)) {
            if (!seen.has(uuid)) {
                cancelled.push({ ...before, uuid })
            }
        }

        const wasSent = Object.keys(state.lines).length > 0

        return {
            added,
            cancelled,
            noted,
            modeChanged: wasSent && Boolean(state.takeaway) !== Boolean(order.is_takeaway),
            noteChanged: (state.note ?? '') !== (order.note ?? ''),
        }
    }

    cancellationChanges(order) {
        return {
            added: [],
            cancelled: Object.entries(this.preparationState(order).lines).map(([uuid, before]) => ({ ...before, uuid })),
            noted: [],
            modeChanged: false,
            noteChanged: false,
        }
    }

    preparationSummary(order = this.activeOrder) {
        if (!order || !this.sendsToKitchen) {
            return { count: 0, categories: [] }
        }

        const changes = this.preparationChanges(order)

        const categories = new Map()

        for (const entry of changes.added) {
            const categoryId = this.master.products.get(entry.product_id)?.category_ids?.[0] ?? 0

            const name = this.master.categories.get(categoryId)?.name ?? ''

            categories.set(name, (categories.get(name) ?? 0) + entry.qty)
        }

        const count = [...changes.added, ...changes.cancelled].reduce((carry, entry) => carry + entry.qty, 0)
            + changes.noted.length

        return {
            count,
            categories: [...categories].map(([name, qty]) => ({ name, qty })).filter((entry) => entry.name),
        }
    }

    preparationTickets(order, changes) {
        const table = this.orderTable(order)

        const time = new Date().toLocaleTimeString(this.intlLocale, { hour: '2-digit', minute: '2-digit' })

        return this.preparationPrinters
            .map((printer) => {
                const take = (entries) => entries.filter((entry) => this.printerTakes(printer, entry.product_id))

                const added = take(changes.added)
                const cancelled = take(changes.cancelled)
                const noted = take(changes.noted)

                const hasLines = added.length + cancelled.length + noted.length > 0

                if (!hasLines && !changes.modeChanged) {
                    return null
                }

                return {
                    printer: printer.name,
                    register: this.config.name,
                    time,
                    cashier: this.config.cashier_name ?? '',
                    table: table?.table_number ?? null,
                    label: table || this.orderLabel(order) === order.tracking_number ? null : this.orderLabel(order),
                    tracking_number: order.tracking_number,
                    takeaway: Boolean(order.is_takeaway),
                    mode_changed: changes.modeChanged,
                    added,
                    cancelled,
                    noted,
                    note: hasLines && (changes.noteChanged || added.length) ? (order.note ?? '') : '',
                }
            })
            .filter(Boolean)
    }

    markPreparationSent(order) {
        const lines = {}

        for (const line of order.lines) {
            if (this.preparesLine(line) && line.qty > 0) {
                lines[line.uuid] = this.preparationEntry(line, line.qty)
            }
        }

        order.preparation_state = {
            lines,
            note: order.note ?? '',
            takeaway: Boolean(order.is_takeaway),
        }
    }

    async sendToKitchen(order = this.activeOrder) {
        if (!order || !this.sendsToKitchen || order.state !== 'draft') {
            return
        }

        const tickets = this.preparationTickets(order, this.preparationChanges(order))

        if (!tickets.length) {
            this.notifyKitchen(this.t('kitchen.nothing'))

            return
        }

        this.markPreparationSent(order)

        if (this.isRestaurant && navigator.onLine) {
            this.shareDrafts()
        }

        await this.printKitchenTickets(tickets)
    }

    async printKitchenTickets(tickets) {
        for (const ticket of tickets) {
            this.state.kitchenTicket = ticket

            await nextTick()

            await window.pointOfSaleReceipt?.print('#pos-kitchen-ticket')
        }

        this.state.kitchenTicket = null
    }

    notifyKitchen(message) {
        this.pushToast({ title: message, status: 'warning' })
    }

    pushToast({ title, body = '', status = 'danger', duration = 4000 }) {
        const id = uuidv4()

        this.state.toasts.push({ id, title, body, status })

        window.setTimeout(() => this.dismissToast(id), duration)

        return id
    }

    dismissToast(id) {
        this.state.toasts = this.state.toasts.filter((toast) => toast.id !== id)
    }

    get isRestaurant() {
        return Boolean(this.config.is_restaurant)
    }

    get activeFloor() {
        return this.floors.find((floor) => floor.id === this.state.floorId) ?? this.floors[0] ?? null
    }

    get activeTable() {
        return this.tableById.get(this.activeOrder?.table_id) ?? null
    }

    orderTable(order) {
        return this.tableById.get(order?.table_id) ?? null
    }

    tableLabel(table) {
        return this.t('floor.table', { table: table.table_number })
    }

    tableOrders(tableId) {
        return this.drafts.filter((order) => order.table_id === tableId && this.orderHasContent(order))
    }

    tableSummary(tableId) {
        const orders = this.tableOrders(tableId)

        return {
            count: orders.length,
            total: orders.reduce((carry, order) => carry + this.orderTotals(order).total, 0),
            guests: orders.reduce((carry, order) => carry + (order.customer_count ?? 0), 0),
        }
    }

    orderHasContent(order) {
        return order.lines.length > 0
            || (order.customer_count ?? 0) > 0
            || Boolean(order.is_booked)
    }

    resumeScreenFor(order) {
        return order?.state === 'draft' && order.last_screen === 'payment' && order.lines.length
            ? 'payment'
            : 'products'
    }

    selectFloor(floorId) {
        if (this.state.planEditing) {
            this.switchPlanFloor(floorId)

            return
        }

        this.state.floorId = floorId
    }

    async switchPlanFloor(floorId) {
        if (floorId === this.state.planDraft?.id || !(await this.savePlanBeforeLeaving())) {
            return
        }

        this.state.floorId = floorId

        this.startPlanEdit()
    }

    async savePlanBeforeLeaving() {
        if (!this.state.planEditing) {
            return true
        }

        await this.savePlan()

        return !this.state.planEditing
    }

    indexTables() {
        this.tableById = new Map(this.floors.flatMap((floor) => floor.tables.map((table) => [table.id, table])))
    }

    get floorViewKey() {
        return `pos.floor-view.${this.config.id}`
    }

    loadFloorView() {
        try {
            this.state.floorView = window.localStorage.getItem(this.floorViewKey) === 'grid' ? 'grid' : 'map'
        } catch {
            this.state.floorView = 'map'
        }
    }

    toggleFloorView() {
        this.state.floorView = this.state.floorView === 'grid' ? 'map' : 'grid'

        try {
            window.localStorage.setItem(this.floorViewKey, this.state.floorView)
        } catch {
            return
        }
    }

    get canEditPlan() {
        return Boolean(this.isRestaurant && this.config.floor_plan?.can_update && !this.state.transferOrderUuid)
    }

    get planLocked() {
        return !this.state.planDraft || this.state.planSaving
    }

    get planTables() {
        return this.state.planDraft?.tables ?? []
    }

    get planSelectedTable() {
        return this.planTables.find((table) => table.key === this.state.planSelectedKey) ?? null
    }

    startPlanEdit() {
        const floor = this.activeFloor

        if (!floor || !this.canEditPlan) {
            return
        }

        this.state.planDraft = {
            id: floor.id,
            name: floor.name,
            background_color: floor.background_color ?? '',
            background_image: floor.background_image ?? null,
            image_file: null,
            remove_image: false,
            tables: floor.tables.map((table) => ({ ...table, key: `t${table.id}` })),
        }

        this.state.planSelectedKey = null
        this.state.planError = null
        this.state.planConfirmDelete = false
        this.state.planEditing = true
    }

    cancelPlanEdit() {
        this.releaseImagePreview()

        this.state.planEditing = false
        this.state.planDraft = null
        this.state.planSelectedKey = null
        this.state.planError = null
        this.state.planConfirmDelete = false
    }

    selectPlanTable(key) {
        this.state.planSelectedKey = this.state.planSelectedKey === key ? null : key
    }

    nextTableNumber() {
        const numbers = this.floors
            .flatMap((floor) => floor.id === this.state.planDraft?.id ? [] : floor.tables)
            .concat(this.planTables)
            .map((table) => Number.parseInt(table.table_number, 10))
            .filter((number) => Number.isFinite(number))

        return String(numbers.length ? Math.max(...numbers) + 1 : 1)
    }

    freePlanSpot(width, height) {
        const columns = 6

        for (let index = 0; index < 120; index += 1) {
            const left = 20 + (index % columns) * 120
            const top = 20 + Math.floor(index / columns) * 120

            const overlaps = this.planTables.some((table) => left < table.position_h + table.width
                && left + width > table.position_h
                && top < table.position_v + table.height
                && top + height > table.position_v)

            if (!overlaps) {
                return { position_h: left, position_v: top }
            }
        }

        return { position_h: 20, position_v: 20 }
    }

    addPlanTable(source = null) {
        if (this.planLocked) {
            return
        }

        const width = source?.width ?? 90
        const height = source?.height ?? 90

        const table = {
            id: null,
            key: uuidv4(),
            floor_id: this.state.planDraft.id,
            table_number: this.nextTableNumber(),
            shape: source?.shape ?? 'square',
            seats: source?.seats ?? 4,
            color: source?.color ?? null,
            width,
            height,
            ...this.freePlanSpot(width, height),
        }

        this.state.planDraft.tables.push(table)

        this.state.planSelectedKey = table.key
    }

    duplicatePlanTable() {
        if (this.planSelectedTable) {
            this.addPlanTable(this.planSelectedTable)
        }
    }

    removePlanTable() {
        if (this.planLocked || !this.state.planSelectedKey) {
            return
        }

        this.state.planDraft.tables = this.planTables.filter((table) => table.key !== this.state.planSelectedKey)

        this.state.planSelectedKey = null
    }

    updatePlanTable(changes) {
        const table = this.planSelectedTable

        if (!table || this.planLocked) {
            return
        }

        Object.assign(table, changes)

        table.seats = Math.max(1, Math.trunc(Number(table.seats) || 1))
    }

    movePlanTable(key, left, top) {
        const table = this.planTables.find((entry) => entry.key === key)

        if (!table || this.planLocked) {
            return
        }

        table.position_h = Math.max(0, Math.round(left / 5) * 5)
        table.position_v = Math.max(0, Math.round(top / 5) * 5)
    }

    resizePlanTable(key, width, height) {
        const table = this.planTables.find((entry) => entry.key === key)

        if (!table || this.planLocked) {
            return
        }

        table.width = Math.max(40, Math.round(width / 5) * 5)
        table.height = Math.max(40, Math.round(height / 5) * 5)
    }

    floorEndpoint(floorId) {
        return this.config.floor_plan.floor_endpoint.replace('__floor__', floorId)
    }

    replaceFloor(payload) {
        const index = this.floors.findIndex((floor) => floor.id === payload.id)

        if (index === -1) {
            this.floors.push(payload)
        } else {
            this.floors.splice(index, 1, payload)
        }

        this.indexTables()
    }

    async savePlan() {
        const draft = this.state.planDraft

        if (!draft || this.state.planSaving) {
            return
        }

        this.state.planSaving = true
        this.state.planError = null

        try {
            const body = await this.requestPlan(this.floorEndpoint(draft.id), 'PUT', {
                name: draft.name,
                background_color: draft.background_color || null,
                tables: draft.tables.map((table) => ({
                    id: table.id,
                    table_number: String(table.table_number).trim(),
                    shape: table.shape,
                    position_h: table.position_h,
                    position_v: table.position_v,
                    width: table.width,
                    height: table.height,
                    seats: table.seats,
                    color: table.color || null,
                })),
            })

            this.replaceFloor(body.data)

            if (draft.image_file) {
                this.replaceFloor(await this.sendFloorImage(draft.id, draft.image_file))
            } else if (draft.remove_image) {
                this.replaceFloor((await this.requestPlan(this.backgroundEndpoint(draft.id), 'DELETE')).data)
            }

            this.cancelPlanEdit()
        } catch (error) {
            this.state.planError = error.message
        } finally {
            this.state.planSaving = false
        }
    }

    async addFloor() {
        if (!this.config.floor_plan?.can_create || this.state.planSaving) {
            return
        }

        if (!(await this.savePlanBeforeLeaving())) {
            return
        }

        this.state.planSaving = true
        this.state.planError = null

        try {
            const body = await this.requestPlan(this.config.floor_plan.create_endpoint, 'POST', {
                name: this.t('floor-plan.new-floor', { number: this.floors.length + 1 }),
            })

            this.replaceFloor(body.data)

            this.cancelPlanEdit()

            this.state.floorId = body.data.id

            this.startPlanEdit()
        } catch (error) {
            this.state.planError = error.message
        } finally {
            this.state.planSaving = false
        }
    }

    async deleteFloor() {
        const draft = this.state.planDraft

        if (!draft || !this.config.floor_plan?.can_delete || this.state.planSaving) {
            return
        }

        if (!this.state.planConfirmDelete) {
            this.state.planConfirmDelete = true

            return
        }

        this.state.planSaving = true
        this.state.planError = null

        try {
            await this.requestPlan(this.floorEndpoint(draft.id), 'DELETE')

            const index = this.floors.findIndex((floor) => floor.id === draft.id)

            if (index !== -1) {
                this.floors.splice(index, 1)
            }

            this.indexTables()

            this.cancelPlanEdit()

            this.state.floorId = this.floors[0]?.id ?? null
        } catch (error) {
            this.state.planError = error.message
            this.state.planConfirmDelete = false
        } finally {
            this.state.planSaving = false
        }
    }

    backgroundEndpoint(floorId) {
        return this.config.floor_plan.background_endpoint.replace('__floor__', floorId)
    }

    uploadFloorImage(file) {
        const draft = this.state.planDraft

        if (!draft || !file || this.state.planSaving) {
            return
        }

        this.releaseImagePreview()

        draft.background_image = URL.createObjectURL(file)
        draft.image_file = file
        draft.remove_image = false
    }

    removeFloorImage() {
        const draft = this.state.planDraft

        if (!draft || this.state.planSaving) {
            return
        }

        this.releaseImagePreview()

        draft.background_image = null
        draft.image_file = null
        draft.remove_image = true
    }

    releaseImagePreview() {
        const image = this.state.planDraft?.background_image

        if (image?.startsWith('blob:')) {
            URL.revokeObjectURL(image)
        }
    }

    async sendFloorImage(floorId, file) {
        const form = new FormData()

        form.append('image', file)

        const response = await this.reachPlanServer(this.backgroundEndpoint(floorId), {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
            },
            body: form,
        })

        const payload = await response.json().catch(() => ({}))

        if (!response.ok) {
            throw new Error(payload.message ?? this.t('floor-plan.failed', { status: response.status }))
        }

        return payload.data
    }

    async reachPlanServer(url, options) {
        try {
            return await fetch(url, options)
        } catch {
            throw new Error(this.t('floor-plan.unreachable'))
        }
    }

    async requestPlan(url, method, body = null) {
        const response = await this.reachPlanServer(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
            },
            body: body ? JSON.stringify(body) : null,
        })

        const payload = await response.json().catch(() => ({}))

        if (!response.ok) {
            throw new Error(payload.message ?? this.t('floor-plan.failed', { status: response.status }))
        }

        return payload
    }

    orderEndpoint(name, orderId) {
        const template = this.boot.config[name]

        return template ? template.replace('__order__', orderId) : null
    }

    async request(url, method = 'GET', body = null) {
        let response

        try {
            response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
                },
                body: body ? JSON.stringify(body) : null,
            })
        } catch {
            throw new Error(this.t('history.unreachable'))
        }

        const payload = await response.json().catch(() => ({}))

        if (!response.ok) {
            throw new Error(payload.message ?? this.t('history.failed', { status: response.status }))
        }

        return payload
    }

    async loadOrderHistory({ search = '', status = 'active', page = 1 } = {}) {
        const url = new URL(this.boot.session.orders_endpoint, window.location.origin)

        url.searchParams.set('status', status)
        url.searchParams.set('page', String(page))

        if (search) {
            url.searchParams.set('search', search)
        }

        return this.request(url.toString(), 'GET')
    }

    async fetchRefundableLines(orderId) {
        const payload = await this.request(this.orderEndpoint('order_refundable_endpoint', orderId), 'GET')

        return payload.data ?? {}
    }

    async prepareRefund(orderId, quantities) {
        const payload = await this.request(this.orderEndpoint('order_refund_endpoint', orderId), 'POST', { quantities })

        return payload.data
    }

    async invoiceServerOrder(orderId, partnerId = null) {
        const payload = await this.request(this.orderEndpoint('order_invoice_endpoint', orderId), 'POST', partnerId ? { partner_id: partnerId } : null)

        return payload.data
    }

    async cancelServerOrder(uuid) {
        const payload = await this.request(this.boot.config.order_cancel_endpoint, 'POST', { uuid })

        return payload.data
    }

    async fetchReceipt(orderId) {
        const payload = await this.request(this.orderEndpoint('order_receipt_endpoint', orderId), 'GET')

        return payload.data
    }

    receiptOrderFor(order) {
        if (!order) {
            return null
        }

        return {
            ...order,
            pos_reference: order.pos_reference ?? order.reference ?? null,
            partner_id: order.partner_id ?? order.partner?.id ?? null,
            table_id: order.table_id ?? order.table?.id ?? null,
            validated_at: order.validated_at ?? order.ordered_at ?? null,
            fiscal_position_id: order.fiscal_position_id ?? null,
            lines: (order.lines ?? []).map((line) => ({
                ...line,
                order_uuid: order.uuid,
                tax_ids: line.tax_ids ?? [],
                fiscal_position_id: order.fiscal_position_id ?? null,
            })),
            payments: (order.payments ?? []).map((payment) => ({
                ...payment,
                order_uuid: order.uuid,
            })),
        }
    }

    printHistoryReceipt() {
        window.pointOfSaleReceipt?.print('.pos-receipt')
    }

    async loadClosingControl() {
        const payload = await this.request(this.boot.session.closing_control_endpoint, 'GET')

        return payload.data
    }

    async recordCashMovement(type, amount, reason = null) {
        const payload = await this.request(this.boot.session.cash_movement_endpoint, 'POST', { type, amount, reason })

        return payload.data
    }

    async cancelDraftOrders() {
        const payload = await this.request(this.boot.session.cancel_drafts_endpoint, 'POST')

        return payload.data
    }

    async closeRegister({ cashBalanceEndReal = null, closingNotes = null, balancingAccountId = null, paymentDifferences = {} } = {}) {
        let response

        try {
            response = await fetch(this.boot.session.close_endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
                },
                body: JSON.stringify({
                    cash_balance_end_real: cashBalanceEndReal,
                    closing_notes: closingNotes,
                    balancing_account_id: balancingAccountId,
                    payment_differences: paymentDifferences,
                }),
            })
        } catch {
            throw new Error(this.t('history.unreachable'))
        }

        const payload = await response.json().catch(() => ({}))

        return { ok: response.ok, status: response.status, ...payload }
    }

    salesDetailsUrl() {
        return this.boot.session.sales_details_endpoint
    }

    downloadSalesDetails() {
        const link = document.createElement('a')

        link.href = this.salesDetailsUrl()

        link.rel = 'noopener'

        document.body.appendChild(link)

        link.click()

        link.remove()
    }

    async tapTable(tableId) {
        if (this.state.transferOrderUuid) {
            this.transferTo(tableId)

            return
        }

        if (this.state.openingTable) {
            return
        }

        this.state.openingTable = true

        try {
            await this.refreshBeforeOpening()
        } finally {
            this.state.openingTable = false
        }

        if (this.state.sessionClosed) {
            return
        }

        this.openTable(tableId)
    }

    refreshBeforeOpening() {
        if (!this.sharesDrafts || !navigator.onLine) {
            return Promise.resolve()
        }

        return Promise.race([
            this.pullDrafts(),
            new Promise((resolve) => window.setTimeout(resolve, TABLE_REFRESH_TIMEOUT)),
        ])
    }

    get canBookTable() {
        const order = this.activeOrder

        return Boolean(this.isRestaurant && order?.table_id && order.state === 'draft' && !order.lines.length)
    }

    bookTable() {
        if (!this.canBookTable) {
            return
        }

        this.activeOrder.is_booked = true

        this.goToFloor()
    }

    releaseTable() {
        const order = this.activeOrder

        if (!this.canBookTable) {
            return
        }

        order.is_booked = false

        this.discardOrder(order.uuid)
    }

    startTransfer() {
        const order = this.activeOrder

        if (!this.isRestaurant || !order || order.state !== 'draft') {
            return
        }

        this.closeActions()

        this.state.transferWasBooked = Boolean(order.is_booked)

        order.is_booked = true

        this.state.transferError = null

        this.goToFloor()

        this.state.transferOrderUuid = order.uuid
    }

    cancelTransfer() {
        const order = this.transferOrder

        this.endTransfer()

        if (!order || this.state.transferWasBooked) {
            return
        }

        order.is_booked = false

        if (order.state === 'draft' && !this.orderHasContent(order)) {
            this.dropOrder(order)
        } else {
            this.shareDrafts()
        }
    }

    endTransfer() {
        this.state.transferOrderUuid = null
        this.state.transferError = null
    }

    get transferOrder() {
        return this.state.orders.find((order) => order.uuid === this.state.transferOrderUuid) ?? null
    }

    canMergeLines(target, source) {
        return target.product_id === source.product_id
            && floatCompare(target.price_unit, source.price_unit, { precisionDigits: 4 }) === 0
            && floatCompare(target.discount, source.discount, { precisionDigits: 4 }) === 0
            && !target.note
            && !source.note
            && !target.lots?.length
            && !source.lots?.length
            && !this.isLockedLine(target)
            && !this.isLockedLine(source)
    }

    transferTo(tableId) {
        const source = this.transferOrder

        if (!source) {
            this.endTransfer()

            return
        }

        const target = this.drafts.find((order) => order.table_id === tableId && order.uuid !== source.uuid)

        if (!target && source.table_id === tableId) {
            this.endTransfer()

            this.selectOrder(source.uuid)

            return
        }

        if (!target) {
            source.table_id = tableId
            source.floating_name = ''

            this.endTransfer()

            this.selectOrder(source.uuid)

            this.shareDrafts()

            return
        }

        if (source.payments.length) {
            this.state.transferError = this.t('transfer.has-payments')

            return
        }

        for (const line of source.lines) {
            const adopting = target.lines.find((entry) => this.canMergeLines(entry, line))

            if (adopting) {
                adopting.qty = floatRound(adopting.qty + line.qty, { precisionDigits: 4 })

                this.repriceLine(adopting)

                continue
            }

            target.lines.push(reactive({
                ...line,
                lots: [...(line.lots ?? [])],
                uuid: uuidv4(),
                order_uuid: target.uuid,
            }))
        }

        target.customer_count = (target.customer_count ?? 0) + (source.customer_count ?? 0)

        this.dropOrder(source)

        this.endTransfer()

        this.selectOrder(target.uuid)

        this.shareDrafts()
    }

    openTableSelector() {
        if (!this.isRestaurant) {
            return
        }

        this.state.tableSelectorDraft = ''
        this.state.tableSelectorOpen = true
    }

    closeTableSelector() {
        this.state.tableSelectorOpen = false
    }

    findTableByNumber(number) {
        const needle = String(number).trim().toLowerCase()

        const matches = (table) => String(table.table_number).trim().toLowerCase() === needle

        return this.activeFloor?.tables.find(matches)
            ?? this.floors.flatMap((floor) => floor.tables).find(matches)
            ?? null
    }

    confirmTableSelector() {
        const input = String(this.state.tableSelectorDraft ?? '').trim()

        if (!input) {
            return
        }

        this.state.tableSelectorOpen = false

        const table = this.findTableByNumber(input)

        if (table) {
            this.state.floorId = table.floor_id

            this.openTable(table.id)

            return
        }

        const floating = this.drafts.find((order) => !order.table_id && order.floating_name === input)

        if (floating) {
            this.selectOrder(floating.uuid)

            return
        }

        const order = this.newOrder(null)

        order.floating_name = input.slice(0, 64)
        order.is_booked = true
    }

    openTable(tableId) {
        const existing = this.drafts.find((order) => order.table_id === tableId)

        if (existing) {
            this.selectOrder(existing.uuid)

            return existing
        }

        return this.newOrder(tableId)
    }

    goToFloor() {
        const order = this.activeOrder

        if (order?.table_id) {
            this.state.floorId = this.tableById.get(order.table_id)?.floor_id ?? this.state.floorId
        }

        this.state.activeOrderUuid = null
        this.state.activeLineUuid = null
        this.state.screen = 'floor'

        this.resetNumpadBuffer()

        if (order?.state === 'draft' && !this.orderHasContent(order)) {
            this.dropOrder(order)
        }

        this.shareDrafts()
    }

    finishOrder() {
        if (this.isRestaurant) {
            this.goToFloor()

            return
        }

        this.newOrder()
    }

    dropOrder(order) {
        this.removeDraftOnServer(order)

        this.database.remove('pos.order.line', order.lines.map((line) => line.uuid))
        this.database.remove('pos.payment', order.payments.map((payment) => payment.uuid))
        this.database.remove('pos.order', [order.uuid])

        this.state.orders = this.state.orders.filter((entry) => entry.uuid !== order.uuid)
    }

    get sharesDrafts() {
        return this.isRestaurant && Boolean(this.config.draft_endpoint) && Boolean(this.boot.session.drafts_endpoint)
    }

    async requestJson(url, { method = 'GET', body = null } = {}) {
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
            },
            body: body ? JSON.stringify(body) : null,
        })

        if (!response.ok) {
            const error = new Error(String(response.status))

            const body = await response.json().catch(() => ({}))

            error.status = response.status
            error.detail = body.message ?? Object.values(body.errors ?? {}).flat()[0] ?? null

            throw error
        }

        return response.json()
    }

    draftPayload(order) {
        const payload = this.orderPayload(order)

        delete payload.payments
        delete payload.amount_total

        return payload
    }

    draftSignature(order) {
        return JSON.stringify(this.draftPayload(order))
    }

    isDraftDirty(order) {
        return order.state === 'draft'
            && (Boolean(order.serverId) || this.orderHasContent(order))
            && order.synced_signature !== this.draftSignature(order)
    }

    hydrateSyncedDraft(serverOrder) {
        const order = this.hydrateServerOrder(serverOrder)

        order.synced_signature = this.draftSignature(order)

        return order
    }

    async pushDraft(order) {
        if (!this.sharesDrafts || !order || order.state !== 'draft' || !navigator.onLine) {
            return
        }

        const payload = this.draftPayload(order)

        const signature = JSON.stringify(payload)

        this.savingDrafts.add(order.uuid)

        try {
            const body = await this.requestJson(this.config.draft_endpoint, {
                method: 'POST',
                body: { orders: [payload] },
            })

            const saved = (body.data ?? []).find((entry) => entry.uuid === order.uuid)

            const failure = (body.errors ?? []).find((entry) => entry.uuid === order.uuid)

            if (saved) {
                order.serverId = saved.id
                order.synced_signature = signature
                order.pushed_epoch = ++this.draftEpoch
                order.draft_error = null
                order.rejected_signature = null

                if (this.releasedWhileSaving.delete(order.uuid)) {
                    this.removeDraftOnServer(order)

                    return
                }
            }

            if (failure) {
                order.draft_error = failure.message ?? this.t('drafts.rejected')
                order.rejected_signature = signature

                await this.pullDrafts()
            }
        } catch (error) {
            if (error.status >= 400 && error.status < 500 && ![401, 409, 419].includes(error.status)) {
                order.draft_error = error.detail ?? this.t('drafts.rejected')
                order.rejected_signature = signature
            }
        } finally {
            this.savingDrafts.delete(order.uuid)
            this.releasedWhileSaving.delete(order.uuid)
        }
    }

    isDraftRejected(order) {
        return Boolean(order.rejected_signature) && order.rejected_signature === this.draftSignature(order)
    }

    retryDraft(order = this.activeOrder) {
        if (!order) {
            return
        }

        order.draft_error = null
        order.rejected_signature = null

        this.shareDrafts()
    }

    get removedDraftsKey() {
        return `pos.removed-drafts.${this.config.id}`
    }

    removedDrafts() {
        try {
            return JSON.parse(window.localStorage.getItem(this.removedDraftsKey) ?? '[]')
        } catch {
            return []
        }
    }

    rememberRemovedDrafts(uuids) {
        try {
            window.localStorage.setItem(this.removedDraftsKey, JSON.stringify([...new Set(uuids)]))
        } catch {
            return
        }
    }

    removeDraftOnServer(order) {
        if (this.sharesDrafts && order && !order.serverId && this.savingDrafts.has(order.uuid)) {
            this.releasedWhileSaving.add(order.uuid)

            return
        }

        if (!this.sharesDrafts || !order?.serverId || order.state !== 'draft') {
            return
        }

        this.rememberRemovedDrafts([...this.removedDrafts(), order.uuid])

        this.flushRemovedDrafts()
    }

    async flushRemovedDrafts() {
        if (!navigator.onLine) {
            return
        }

        for (const uuid of this.removedDrafts()) {
            try {
                await this.requestJson(this.boot.config.order_cancel_endpoint, {
                    method: 'POST',
                    body: { uuid },
                })

                this.forgetRemovedDraft(uuid)
            } catch (error) {
                if (/^4\d\d$/.test(error.message)) {
                    this.forgetRemovedDraft(uuid)
                }
            }
        }
    }

    forgetRemovedDraft(uuid) {
        this.rememberRemovedDrafts(this.removedDrafts().filter((entry) => entry !== uuid))

        this.removedDraftEpochs.set(uuid, ++this.draftEpoch)
    }

    pullDrafts() {
        if (!this.sharesDrafts || !navigator.onLine) {
            return Promise.resolve()
        }

        if (this.draftPull && this.draftPullEpoch === this.draftEpoch) {
            return this.draftPull
        }

        const epoch = this.draftEpoch

        const pull = this.requestJson(this.boot.session.drafts_endpoint)
            .then((body) => this.mergeDrafts(body.data ?? [], epoch))
            .catch((error) => {
                if (error.message === '409') {
                    this.state.sessionClosed = true
                }
            })
            .finally(() => {
                if (this.draftPull === pull) {
                    this.draftPull = null
                }
            })

        this.draftPull = pull
        this.draftPullEpoch = epoch

        return pull
    }

    mergeDrafts(orders, epoch = this.draftEpoch) {
        const removed = new Set(this.removedDrafts())

        const serverOrders = orders.filter((order) => !removed.has(order.uuid)
            && !this.releasedWhileSaving.has(order.uuid)
            && (this.removedDraftEpochs.get(order.uuid) ?? -1) <= epoch)

        const serverUuids = new Set(serverOrders.map((order) => order.uuid))

        const busy = (order) => order.uuid === this.state.activeOrderUuid
            || order.uuid === this.state.transferOrderUuid
            || order.state !== 'draft'
            || (order.pushed_epoch ?? -1) > epoch
            || this.isDraftDirty(order)

        for (const order of this.state.orders.filter((entry) => entry.serverId && !busy(entry) && !serverUuids.has(entry.uuid))) {
            this.database.remove('pos.order.line', order.lines.map((line) => line.uuid))
            this.database.remove('pos.payment', order.payments.map((payment) => payment.uuid))
            this.database.remove('pos.order', [order.uuid])

            this.state.orders = this.state.orders.filter((entry) => entry.uuid !== order.uuid)
        }

        for (const serverOrder of serverOrders) {
            const index = this.state.orders.findIndex((entry) => entry.uuid === serverOrder.uuid)

            if (index === -1) {
                this.state.orders.push(this.hydrateSyncedDraft(serverOrder))

                continue
            }

            const local = this.state.orders[index]

            if (busy(local)) {
                continue
            }

            const fresh = this.hydrateSyncedDraft({ ...serverOrder, payments: local.payments })

            fresh.last_screen = local.last_screen

            const kept = new Set(fresh.lines.map((line) => line.uuid))

            this.database.remove('pos.order.line', local.lines.map((line) => line.uuid).filter((uuid) => !kept.has(uuid)))

            this.state.orders.splice(index, 1, fresh)
        }
    }

    async shareDrafts() {
        if (!this.sharesDrafts || this.state.sessionClosed) {
            return
        }

        await this.flushRemovedDrafts()

        for (const pending of this.state.orders.filter((entry) => this.isDraftDirty(entry) && !this.isDraftRejected(entry))) {
            if (this.state.orders.some((entry) => entry.uuid === pending.uuid)) {
                await this.pushDraft(pending)
            }
        }

        await this.pullDrafts()
    }

    startDraftRefresh() {
        if (this.draftRefresh || !this.sharesDrafts) {
            return
        }

        this.draftRefresh = window.setInterval(() => {
            if (this.state.screen === 'floor' && !document.hidden) {
                this.shareDrafts()
            }
        }, DRAFT_REFRESH_INTERVAL)

        this.shareDrafts()
    }

    openGuests() {
        const order = this.activeOrder

        if (!order) {
            return
        }

        this.state.guestsDraft = order.customer_count ?? 0
        this.state.guestsModalOpen = true
    }

    closeGuests() {
        this.state.guestsModalOpen = false
    }

    adjustGuests(step) {
        this.state.guestsDraft = Math.max(0, Math.trunc(Number(this.state.guestsDraft) || 0) + step)
    }

    confirmGuests() {
        const order = this.activeOrder

        this.state.guestsModalOpen = false

        if (!order) {
            return
        }

        order.customer_count = Math.max(0, Math.trunc(Number(this.state.guestsDraft) || 0))

        if (this.isRestaurant && order.customer_count === 0 && !order.lines.length && !order.payments.length) {
            this.goToFloor()
        }
    }

    get canSplit() {
        const order = this.activeOrder

        return Boolean(this.isRestaurant && this.config.enable_split_bill && order)
            && order.lines.reduce((carry, line) => carry + Math.abs(line.qty), 0) >= 2
    }

    openSplit() {
        if (!this.canSplit) {
            return
        }

        this.closeActions()

        this.state.splitQuantities = {}
        this.state.screen = 'split'
    }

    closeSplit() {
        this.state.splitQuantities = {}
        this.state.screen = 'products'
    }

    splitsWhole(line) {
        return !Number.isInteger(line.qty) || line.lots?.length > 0 || this.isLockedLine(line)
    }

    tapSplitLine(line) {
        const current = this.state.splitQuantities[line.uuid] ?? 0

        let next = current + 1

        if (this.splitsWhole(line)) {
            next = current === line.qty ? 0 : line.qty
        } else if (current >= line.qty) {
            next = 0
        }

        this.state.splitQuantities = { ...this.state.splitQuantities, [line.uuid]: next }
    }

    get splitTotal() {
        const order = this.activeOrder

        if (!order) {
            return 0
        }

        return order.lines.reduce((carry, line) => {
            const quantity = this.state.splitQuantities[line.uuid] ?? 0

            if (!quantity || !line.qty) {
                return carry
            }

            return carry + (this.lineTotals(line).total / line.qty) * quantity
        }, 0)
    }

    orderBaseName(order) {
        return this.orderTable(order)?.table_number ?? order.floating_name ?? ''
    }

    nextSplitName(order) {
        const base = String(this.orderBaseName(order) || order.tracking_number || '')

        const taken = this.drafts
            .map((entry) => entry.floating_name)
            .filter((name) => name && name.length === base.length + 1 && name.startsWith(base))
            .map((name) => name.slice(-1))
            .sort()

        const last = taken.at(-1)

        if (!last) {
            return `${base}B`
        }

        if (last >= 'Z') {
            return null
        }

        return `${base}${String.fromCharCode(last.charCodeAt(0) + 1)}`
    }

    confirmSplit() {
        const original = this.activeOrder

        if (!original) {
            return
        }

        const moving = original.lines.filter((line) => (this.state.splitQuantities[line.uuid] ?? 0) > 0)

        if (!moving.length) {
            return
        }

        const name = this.nextSplitName(original)

        if (!name) {
            return
        }

        const target = this.newOrder(null)

        target.floating_name = name
        target.partner_id = original.partner_id
        target.price_list_id = original.price_list_id
        target.fiscal_position_id = original.fiscal_position_id
        target.is_takeaway = original.is_takeaway

        const emptied = []

        for (const line of moving) {
            const quantity = this.state.splitQuantities[line.uuid]

            const moved = reactive({
                ...line,
                lots: [...(line.lots ?? [])],
                uuid: uuidv4(),
                order_uuid: target.uuid,
                qty: quantity,
            })

            target.lines.push(moved)

            this.repriceLine(moved)

            const remaining = floatRound(line.qty - quantity, { precisionDigits: 4 })

            if (floatIsZero(remaining, { precisionDigits: 4 })) {
                emptied.push(line.uuid)
            } else {
                line.qty = remaining

                this.repriceLine(line)
            }
        }

        if (emptied.length) {
            this.database.remove('pos.order.line', emptied)

            original.lines = original.lines.filter((line) => !emptied.includes(line.uuid))
        }

        original.customer_count = Math.max(0, (original.customer_count ?? 0) - 1)

        this.state.splitQuantities = {}

        this.selectOrder(target.uuid)
    }

    get printsReceipts() {
        return this.config.enable_receipt_print !== false
    }

    get printsReceiptAutomatically() {
        return this.printsReceipts && Boolean(this.config.enable_receipt_auto_print)
    }

    openBill() {
        if (!this.activeOrder?.lines.length) {
            return
        }

        this.closeActions()

        this.state.billModalOpen = true
    }

    closeBill() {
        this.state.billModalOpen = false
    }

    get canToggleTakeaway() {
        return Boolean(this.isRestaurant && this.config.enable_takeaway && this.activeOrder)
    }

    toggleTakeaway() {
        const order = this.activeOrder

        if (!order || !this.canToggleTakeaway) {
            return
        }

        order.is_takeaway = !order.is_takeaway

        order.fiscal_position_id = order.is_takeaway
            ? (this.config.takeaway_fiscal_position_id ?? this.config.fiscal_position_id ?? null)
            : (this.config.fiscal_position_id ?? null)

        this.closeActions()
    }

    get canRenameOrder() {
        return Boolean(this.isRestaurant && this.activeOrder && !this.activeOrder.table_id)
    }

    openOrderName() {
        if (!this.canRenameOrder) {
            return
        }

        this.closeActions()

        this.state.orderNameDraft = this.activeOrder.floating_name ?? ''
        this.state.orderNameModalOpen = true
    }

    closeOrderName() {
        this.state.orderNameModalOpen = false
    }

    confirmOrderName() {
        const order = this.activeOrder

        if (order) {
            order.floating_name = String(this.state.orderNameDraft ?? '').trim().slice(0, 64)
        }

        this.state.orderNameModalOpen = false
    }

    get globalDiscountProductId() {
        const productId = this.config.discount_product_id

        return this.config.enable_global_discount && productId && this.master.products.get(productId) ? productId : null
    }

    isDiscountLine(line) {
        return Boolean(this.globalDiscountProductId) && line.product_id === this.globalDiscountProductId
    }

    isLockedLine(line) {
        return this.isTipLine(line) || this.isDiscountLine(line)
    }

    get canApplyGlobalDiscount() {
        return Boolean(this.globalDiscountProductId)
            && Boolean(this.activeOrder?.lines.some((line) => !this.isLockedLine(line)))
    }

    openGlobalDiscount() {
        if (!this.canApplyGlobalDiscount) {
            return
        }

        this.closeActions()

        this.state.globalDiscountDraft = String(this.config.global_discount_percentage ?? 10)
        this.state.globalDiscountModalOpen = true
    }

    closeGlobalDiscount() {
        this.state.globalDiscountModalOpen = false
    }

    confirmGlobalDiscount() {
        this.applyGlobalDiscount(Number(this.state.globalDiscountDraft) || 0)

        this.state.globalDiscountModalOpen = false
    }

    discountableBase(line) {
        const totals = this.lineTotals(line)

        const excluded = totals.breakdown
            .filter((tax) => !tax.price_include)
            .reduce((carry, tax) => carry + tax.amount, 0)

        return totals.total - excluded
    }

    applyGlobalDiscount(percentage) {
        const order = this.activeOrder

        const productId = this.globalDiscountProductId

        if (!order || !productId) {
            return
        }

        const rate = Math.min(Math.max(percentage, 0), 100)

        for (const line of order.lines.filter((entry) => this.isDiscountLine(entry))) {
            this.removeLine(line.uuid)
        }

        const groups = new Map()

        for (const line of order.lines) {
            if (this.isLockedLine(line)) {
                continue
            }

            const taxIds = [...(line.tax_ids ?? [])].sort((a, b) => a - b)

            const key = taxIds.join(',')

            const group = groups.get(key) ?? { taxIds, base: 0 }

            group.base += this.discountableBase(line)

            groups.set(key, group)
        }

        for (const { taxIds, base } of groups.values()) {
            const amount = floatRound(-(rate / 100) * base, { precisionRounding: this.currency.rounding })

            if (amount >= 0) {
                continue
            }

            order.lines.push(reactive({
                uuid: uuidv4(),
                order_uuid: order.uuid,
                product_id: productId,
                qty: 1,
                price_unit: amount,
                price_overridden: true,
                discount: 0,
                note: '',
                lots: [],
                tax_ids: taxIds.filter((id) => this.taxById.get(id)?.amount_type !== 'fixed'),
            }))
        }
    }

    get tipProductId() {
        const productId = this.config.tip_product_id

        return this.config.enable_tip && productId && this.master.products.get(productId) ? productId : null
    }

    isTipLine(line) {
        return Boolean(this.tipProductId) && line.product_id === this.tipProductId
    }

    tipLine(order = this.activeOrder) {
        return order?.lines.find((line) => this.isTipLine(line)) ?? null
    }

    tipAmount(order = this.activeOrder) {
        return this.tipLine(order)?.price_unit ?? 0
    }

    openTip() {
        const order = this.activeOrder

        if (!order || !this.tipProductId) {
            return
        }

        const tip = this.tipAmount(order)

        const change = this.orderTotals(order).change

        const start = tip === 0 && change > 0 ? change : tip

        this.state.tipDraft = start ? String(floatRound(start, { precisionRounding: this.currency.rounding })) : ''
        this.state.tipModalOpen = true
    }

    closeTip() {
        this.state.tipModalOpen = false
    }

    confirmTip() {
        this.setTip(Number(this.state.tipDraft) || 0)

        this.state.tipModalOpen = false
    }

    setTip(amount) {
        const order = this.activeOrder

        if (!order || !this.tipProductId) {
            return
        }

        const tip = Math.max(0, floatRound(amount, { precisionRounding: this.currency.rounding }))

        const line = this.tipLine(order)

        if (floatIsZero(tip, { precisionRounding: this.currency.rounding })) {
            if (line) {
                this.removeLine(line.uuid)
            }

            return
        }

        if (line) {
            line.price_unit = tip
            line.qty = 1

            return
        }

        const product = this.master.products.get(this.tipProductId)

        order.lines.push(reactive({
            uuid: uuidv4(),
            order_uuid: order.uuid,
            product_id: this.tipProductId,
            qty: 1,
            price_unit: tip,
            price_overridden: true,
            discount: 0,
            note: '',
            lots: [],
            tax_ids: product?.tax_ids ?? [],
        }))
    }

    startIdleWatch() {
        if (this.idleWatch || !this.isRestaurant) {
            return
        }

        this.idleWatch = () => {
            window.clearTimeout(this.idleTimer)

            this.idleTimer = window.setTimeout(() => this.returnToFloorWhenIdle(), FLOOR_IDLE_TIMEOUT)
        }

        window.addEventListener('pointerdown', this.idleWatch)
        window.addEventListener('keydown', this.idleWatch)

        this.idleWatch()
    }

    returnToFloorWhenIdle() {
        if (this.state.screen !== 'products' || document.querySelector('.fi-modal-open')) {
            this.idleWatch?.()

            return
        }

        this.goToFloor()
    }

    selectOrder(uuid) {
        const leaving = this.activeOrder

        this.state.activeOrderUuid = uuid
        this.state.activeLineUuid = null
        this.state.screen = this.resumeScreenFor(this.activeOrder)

        this.resetNumpadBuffer()

        this.state.priceListId = this.activeOrder?.price_list_id ?? this.config.price_list_id ?? null

        this.flushDeferredEvictions()

        if (leaving && leaving.uuid !== uuid) {
            this.shareDrafts()
        }
    }

    get priceLists() {
        return this.master.price_lists.all()
    }

    get activePriceList() {
        return this.master.price_lists.get(this.state.priceListId) ?? null
    }

    canSelectPriceList() {
        return Boolean(this.config.enable_price_list) && this.priceLists.length > 0
    }

    openActions() {
        this.state.actionsModalOpen = true
    }

    closeActions() {
        this.state.actionsModalOpen = false
        this.state.cancelError = null
    }

    openPriceLists() {
        this.state.actionsModalOpen = false
        this.state.priceListModalOpen = true
    }

    closePriceLists() {
        this.state.priceListModalOpen = false
    }

    selectPriceList(priceListId) {
        this.state.priceListId = priceListId

        const order = this.activeOrder

        if (order) {
            order.price_list_id = priceListId

            for (const line of order.lines) {
                this.repriceLine(line)
            }
        }

        this.closePriceLists()
    }

    async cancelActiveOrder() {
        const order = this.activeOrder

        if (!order || this.state.cancellingOrder) {
            return
        }

        if (order.serverId && !(await this.cancelOnServer(order))) {
            return
        }

        this.closeActions()

        this.discardOrder(order.uuid)
    }

    async cancelOnServer(order) {
        this.state.cancellingOrder = true
        this.state.cancelError = null

        try {
            const response = await fetch(this.boot.config.order_cancel_endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
                },
                body: JSON.stringify({ uuid: order.uuid }),
            })

            if (!response.ok) {
                const body = await response.json().catch(() => ({}))

                this.state.cancelError = body.message ?? this.t('actions.cancel-order.failed', { status: response.status })

                return false
            }

            return true
        } catch (error) {
            this.state.cancelError = navigator.onLine
                ? error.message
                : this.t('actions.cancel-order.offline')

            return false
        } finally {
            this.state.cancellingOrder = false
        }
    }

    discardOrder(uuid) {
        const order = this.state.orders.find((entry) => entry.uuid === uuid)

        if (!order) {
            return
        }

        if (this.sendsToKitchen && Object.keys(this.preparationState(order).lines).length) {
            this.printKitchenTickets(this.preparationTickets(order, this.cancellationChanges(order)))
        }

        this.removeDraftOnServer(order)

        this.database.remove('pos.order.line', order.lines.map((line) => line.uuid))
        this.database.remove('pos.payment', order.payments.map((payment) => payment.uuid))
        this.database.remove('pos.order', [uuid])

        this.state.orders = this.state.orders.filter((entry) => entry.uuid !== uuid)

        if (this.state.activeOrderUuid === uuid || !this.drafts.length) {
            this.activateDraft()
        }
    }

    priceFor(productId, qty = 1, priceListId = this.state.priceListId) {
        const listKey = String(priceListId ?? 0)

        const list = this.prices[listKey] ?? this.prices['0'] ?? {}

        const price = list[String(productId)]

        let resolved = price !== undefined
            ? Number(price)
            : Number(this.master.products.get(productId)?.price ?? 0)

        const tiers = this.priceTiers[listKey]?.[String(productId)] ?? []

        for (const tier of tiers) {
            if (floatCompare(qty, tier.min_qty, { precisionDigits: 4 }) >= 0) {
                resolved = Number(tier.price)
            }
        }

        return resolved
    }

    repriceLine(line) {
        if (line.price_overridden || line.refunded_order_line_id) {
            return
        }

        const order = this.state.orders.find((entry) => entry.uuid === line.order_uuid)

        line.price_unit = this.priceFor(line.product_id, line.qty, order?.price_list_id ?? this.state.priceListId)
    }

    taxesFor(line) {
        const order = this.state.orders.find((entry) => entry.uuid === line.order_uuid)

        const fiscalPositionId = order
            ? order.fiscal_position_id
            : (line.fiscal_position_id ?? this.state.fiscalPositionId)

        const mapped = this.mapTaxIds(line.tax_ids ?? [], fiscalPositionId)

        return mapped.map((id) => this.taxById.get(id)).filter(Boolean)
    }

    mapTaxIds(taxIds, fiscalPositionId = this.state.fiscalPositionId) {
        if (!fiscalPositionId || !taxIds.length) {
            return taxIds
        }

        const position = this.master.fiscal_positions.get(fiscalPositionId)

        if (!position || !position.tax_map.length) {
            return taxIds
        }

        const mapped = []

        for (const taxId of taxIds) {
            const matches = position.tax_map.filter((entry) => entry.source_id === taxId)

            if (!matches.length) {
                mapped.push(taxId)

                continue
            }

            for (const match of matches) {
                if (match.destination_id && !mapped.includes(match.destination_id)) {
                    mapped.push(match.destination_id)
                }
            }
        }

        return mapped
    }

    lineTotals(line) {
        const taxes = this.taxesFor(line)

        if (!taxes.length) {
            const subtotal = floatRound(line.price_unit * (1 - line.discount / 100) * line.qty, { precisionDigits: 4 })

            return { subtotal, tax: 0, total: subtotal, breakdown: [] }
        }

        const outcome = computeAll({
            taxes,
            priceUnit: line.price_unit * (1 - line.discount / 100),
            quantity: line.qty,
            currencyRounding: this.currency.rounding,
            roundingMethod: this.roundingMethod,
        })

        return {
            subtotal: floatRound(outcome.total_excluded, { precisionDigits: 4 }),
            tax: floatRound(outcome.total_tax, { precisionDigits: 4 }),
            total: floatRound(outcome.total_included, { precisionDigits: 4 }),
            breakdown: outcome.taxes,
        }
    }

    displayLineTotal(line) {
        const totals = this.lineTotals(line)

        return this.config.tax_display === 'total' ? totals.total : totals.subtotal
    }

    orderTotals(order = this.activeOrder) {
        if (!order) {
            return { subtotal: 0, tax: 0, total: 0, rounding: 0, paid: 0, due: 0, change: 0, breakdown: [] }
        }

        let subtotal = 0
        let tax = 0

        const breakdown = new Map()

        for (const line of order.lines) {
            const totals = this.lineTotals(line)

            subtotal += totals.subtotal
            tax += totals.tax

            for (const entry of totals.breakdown) {
                const current = breakdown.get(entry.id) ?? { id: entry.id, name: entry.name, amount: 0, base: 0 }

                current.amount += entry.amount
                current.base += entry.base

                breakdown.set(entry.id, current)
            }
        }

        subtotal = floatRound(subtotal, { precisionDigits: 4 })
        tax = floatRound(tax, { precisionDigits: 4 })

        let total = floatRound(subtotal + tax, { precisionDigits: 4 })

        const rounding = this.cashRoundingFor(order, total)

        total = floatRound(total + rounding, { precisionDigits: 4 })

        const settled = order.payments
            .filter((payment) => !payment.is_change)
            .reduce((carry, payment) => carry + payment.amount, 0)

        const refund = floatCompare(total, 0, { precisionRounding: this.currency.rounding }) < 0

        const balance = floatCompare(settled, total, { precisionRounding: this.currency.rounding })

        const change = !refund && balance > 0
            ? floatRound(settled - total, { precisionRounding: this.currency.rounding })
            : 0

        return {
            subtotal,
            tax,
            total,
            rounding,
            paid: settled,
            due: floatRound(total - settled, { precisionRounding: this.currency.rounding }),
            change,
            refund,
            covered: refund ? balance <= 0 : balance >= 0,
            breakdown: [...breakdown.values()],
        }
    }

    cashRoundingFor(order, total) {
        const rounding = this.config.cash_rounding

        if (!rounding) {
            return 0
        }

        if (this.config.enable_only_round_cash_method && !this.hasCashPayment(order)) {
            return 0
        }

        const rounded = floatRound(total, {
            precisionRounding: rounding.rounding,
            roundingMethod: rounding.rounding_method,
        })

        return floatRound(rounded - total, { precisionRounding: this.currency.rounding })
    }

    hasCashPayment(order) {
        return order.payments.some((payment) => {
            if (payment.is_change) {
                return false
            }

            return Boolean(this.master.payment_methods.get(payment.payment_method_id)?.is_cash_count)
        })
    }

    trackingFor(productId) {
        return this.master.products.get(productId)?.tracking ?? 'qty'
    }

    isTracked(productId) {
        return ['lot', 'serial'].includes(this.trackingFor(productId))
    }

    get lotsEnabled() {
        return Boolean(this.config.use_create_lots || this.config.use_existing_lots)
    }

    get lotLine() {
        return this.activeOrder?.lines.find((line) => line.uuid === this.state.lotLineUuid) ?? null
    }

    get lotProduct() {
        const line = this.lotLine

        return line ? this.master.products.get(line.product_id) ?? null : null
    }

    allowsOnlyOneLot(productId) {
        return this.trackingFor(productId) === 'lot'
    }

    get canCreateLots() {
        return Boolean(this.config.use_create_lots || !this.config.use_existing_lots)
    }

    existingLotsFor(productId) {
        return this.lotsByProduct.get(productId) ?? []
    }

    openLots(lineUuid) {
        const line = this.activeOrder?.lines.find((entry) => entry.uuid === lineUuid)

        if (!line || !this.isEditable()) {
            return
        }

        const existing = this.existingLotsFor(line.product_id)

        if (!this.canCreateLots && !existing.length) {
            this.state.lotError = this.t('lots.none-available')
            this.state.lotLineUuid = lineUuid
            this.state.lotRows = []

            return
        }

        const captured = (line.lots ?? []).map((lot) => lot.lot_name)

        if (!captured.length && existing.length === 1) {
            this.applyLots(line, [existing[0].name])

            return
        }

        const blanks = Math.max(Math.ceil(Math.abs(line.qty) - captured.length), 1)

        const rows = this.allowsOnlyOneLot(line.product_id)
            ? [captured[0] ?? '']
            : [...captured, ...Array.from({ length: blanks }, () => '')]

        this.state.lotError = null
        this.state.lotRows = rows
        this.state.lotLineUuid = lineUuid
    }

    closeLots() {
        this.state.lotLineUuid = null
        this.state.lotRows = []
        this.state.lotError = null
    }

    setLotRow(index, value) {
        this.state.lotRows[index] = value
    }

    addLotRow() {
        this.state.lotRows.push('')
    }

    removeLotRow(index) {
        this.state.lotRows.splice(index, 1)
    }

    applyLots(line, names) {
        const serial = this.trackingFor(line.product_id) === 'serial'

        const unique = serial ? [...new Set(names)] : names

        const existing = this.existingLotsFor(line.product_id)

        if (serial && unique.length) {
            line.qty = line.qty < 0 ? -unique.length : unique.length

            this.repriceLine(line)
        }

        const share = unique.length ? Math.abs(line.qty) / unique.length : 0

        line.lots = unique.map((name) => ({
            lot_name: name,
            lot_id: existing.find((lot) => lot.name === name)?.id ?? null,
            qty: serial ? 1 : share,
        }))

        this.closeLots()
    }

    confirmLots() {
        const line = this.lotLine

        if (!line) {
            return
        }

        const names = this.state.lotRows.map((row) => String(row).trim()).filter(Boolean)

        if (!this.canCreateLots) {
            const available = this.existingLotsFor(line.product_id).map((lot) => lot.name)

            const unknown = names.filter((name) => !available.includes(name))

            if (unknown.length) {
                this.state.lotError = this.t('lots.none-available')

                return
            }
        }

        this.applyLots(line, names)
    }

    linesMissingLots(order = this.activeOrder) {
        if (!order || !this.lotsEnabled) {
            return []
        }

        return this.sellableLines(order).filter((line) => {
            if (!this.isTracked(line.product_id)) {
                return false
            }

            const captured = line.lots?.length ?? 0

            return this.trackingFor(line.product_id) === 'serial'
                ? captured !== Math.abs(line.qty)
                : captured === 0
        })
    }

    dismissLotWarning() {
        this.state.lotWarningOpen = false
    }

    bufferAmount(buffer) {
        const value = Number(buffer)

        return Number.isFinite(value) ? value : 0
    }

    pickProduct(productId) {
        const product = this.master.products.get(productId)

        if (product?.is_configurable && this.openVariants(productId)) {
            return
        }

        this.addLine(productId)
    }

    addLine(productId) {
        const line = this.addProduct(productId)

        if (line && this.lotsEnabled && this.isTracked(productId) && !line.lots?.length) {
            this.openLots(line.uuid)
        }

        return line
    }

    variantsFor(productId) {
        return this.variantsByProduct.get(productId) ?? null
    }

    get variantProduct() {
        return this.state.variantProductId ? this.master.products.get(this.state.variantProductId) : null
    }

    get variantAttributes() {
        return this.variantsFor(this.state.variantProductId)?.attributes ?? []
    }

    openVariants(productId) {
        const entry = this.variantsFor(productId)

        if (!entry) {
            return false
        }

        this.state.variantProductId = productId

        this.state.variantSelection = Object.fromEntries(
            entry.attributes.map((attribute) => [attribute.id, attribute.values[0]?.id ?? null]),
        )

        return true
    }

    closeVariants() {
        this.state.variantProductId = null
        this.state.variantSelection = {}
    }

    selectVariantValue(attributeId, valueId) {
        this.state.variantSelection[attributeId] = valueId
    }

    resolveVariant() {
        const entry = this.variantsFor(this.state.variantProductId)

        if (!entry) {
            return null
        }

        const chosen = Object.values(this.state.variantSelection).filter((id) => id !== null)

        if (chosen.length !== entry.attributes.length) {
            return null
        }

        const signature = [...chosen].sort((a, b) => a - b).join(',')

        const match = entry.variants.find((variant) => variant.value_ids.join(',') === signature)

        return match ? this.master.products.get(match.id) ?? null : null
    }

    confirmVariant() {
        const variant = this.resolveVariant()

        if (!variant) {
            return
        }

        const productId = variant.id

        this.closeVariants()

        this.addLine(productId)
    }

    addProduct(productId, { qty = 1 } = {}) {
        const order = this.activeOrder?.state === 'draft' ? this.activeOrder : this.newOrder()

        const product = this.master.products.get(productId)

        if (!product) {
            return null
        }

        const existing = order.lines.find((line) => (
            line.product_id === productId
            && line.discount === 0
            && !line.price_overridden
            && !line.note
        ))

        this.resetNumpadBuffer()

        if (existing) {
            existing.qty = floatRound(existing.qty + qty, { precisionDigits: 4 })

            this.repriceLine(existing)

            this.state.activeLineUuid = existing.uuid

            return existing
        }

        const line = reactive({
            uuid: uuidv4(),
            order_uuid: order.uuid,
            product_id: productId,
            uom_id: product.uom_id,
            qty,
            price_unit: this.priceFor(productId, qty, order.price_list_id ?? this.state.priceListId),
            price_overridden: false,
            discount: 0,
            note: '',
            lots: [],
            tax_ids: product.tax_ids ?? [],
        })

        order.lines.push(line)

        this.state.activeLineUuid = line.uuid

        return line
    }

    selectLine(uuid) {
        if (!this.isEditable()) {
            return
        }

        this.state.activeLineUuid = this.state.activeLineUuid === uuid ? null : uuid

        this.resetNumpadBuffer()
    }

    removeLine(uuid) {
        const order = this.activeOrder

        if (!this.isEditable(order)) {
            return
        }

        this.database.remove('pos.order.line', [uuid])

        order.lines = order.lines.filter((line) => line.uuid !== uuid)

        if (this.state.activeLineUuid === uuid) {
            this.state.activeLineUuid = null
        }
    }

    numpadModeAllowed(mode) {
        if (mode === 'discount') {
            return Boolean(this.config.enable_line_discount)
        }

        if (mode === 'price') {
            return this.config.can_edit_price !== false
        }

        return mode === 'qty'
    }

    setNumpadMode(mode) {
        if (!this.numpadModeAllowed(mode)) {
            return
        }

        this.state.numpadMode = mode

        this.resetNumpadBuffer()
    }

    resetNumpadBuffer() {
        this.state.numpadBuffer = ''
        this.state.numpadFresh = true
    }

    pressNumpad(key) {
        const line = this.activeLine

        if (!line || !this.isEditable()) {
            return
        }

        if (this.isLockedLine(line) && this.state.numpadMode !== 'price') {
            if (key === 'Backspace' || key === 'Delete') {
                this.removeLine(line.uuid)

                this.resetNumpadBuffer()
            }

            return
        }

        let buffer = nextBuffer(this.state.numpadBuffer, key, this.state.numpadFresh)

        if (key === '-' && buffer === '-0') {
            buffer = this.negatedNumpadValue(line) ?? buffer
        }

        this.state.numpadBuffer = buffer ?? ''
        this.state.numpadFresh = false

        if (buffer === null) {
            if (this.state.numpadMode === 'qty') {
                this.removeLine(line.uuid)
            }

            this.state.numpadMode = 'qty'

            this.resetNumpadBuffer()

            return
        }

        this.applyNumpad(line, this.bufferAmount(buffer))
    }

    negatedNumpadValue(line) {
        if (this.state.numpadMode === 'qty') {
            return line.refunded_order_line_id ? null : String(-line.qty)
        }

        if (this.state.numpadMode === 'discount') {
            return String(-line.discount)
        }

        if (this.state.numpadMode === 'price') {
            return String(-line.price_unit)
        }

        return null
    }

    applyNumpad(line, value) {
        if (this.state.numpadMode === 'qty') {
            line.qty = value

            this.repriceLine(line)
        } else if (this.state.numpadMode === 'discount') {
            line.discount = Math.min(Math.max(value, 0), 100)
        } else if (this.state.numpadMode === 'price') {
            line.price_unit = value
            line.price_overridden = true
        }
    }

    selectCustomer(partnerId) {
        const order = this.activeOrder

        if (order && !this.isCustomerLocked) {
            order.partner_id = partnerId
            this.state.shipLaterError = null
        }
    }

    createCustomer(draft) {
        if (!draft.name || !draft.name.trim()) {
            return null
        }

        const partner = {
            id: `local-${uuidv4()}`,
            name: draft.name.trim(),
            email: draft.email?.trim() || null,
            phone: draft.phone?.trim() || null,
            mobile: draft.mobile?.trim() || null,
            street1: draft.street1?.trim() || null,
            city: draft.city?.trim() || null,
            zip: draft.zip?.trim() || null,
            is_local: true,
        }

        this.master.partners.put(partner)

        this.selectCustomer(partner.id)

        return partner
    }

    partnerDraftFor(order) {
        if (!order.partner_id || typeof order.partner_id !== 'string') {
            return null
        }

        const partner = this.master.partners.get(order.partner_id)

        if (!partner || !partner.is_local) {
            return null
        }

        return {
            name: partner.name,
            email: partner.email,
            phone: partner.phone,
            mobile: partner.mobile,
            street1: partner.street1,
            city: partner.city,
            zip: partner.zip,
        }
    }

    searchPartners(term) {
        const needle = term.trim().toLowerCase()

        if (!needle) {
            return this.master.partners.all().slice(0, 50)
        }

        return this.master.partners
            .filter((partner) => [partner.name, partner.email, partner.phone, partner.mobile]
                .some((field) => field && String(field).toLowerCase().includes(needle)))
            .slice(0, 50)
    }

    rootCategories() {
        return this.master.categories.filter((category) => !this.master.categories.get(category.parent_id))
    }

    childCategories(categoryId) {
        return this.master.categories.getBy('parent_id', categoryId)
    }

    categoryPath(categoryId) {
        const path = []

        let category = this.master.categories.get(categoryId)

        while (category && !path.includes(category)) {
            path.unshift(category)

            category = this.master.categories.get(category.parent_id)
        }

        return path
    }

    visibleCategories(categoryId) {
        const path = this.categoryPath(categoryId)

        const expand = (categories) => categories.flatMap((category) => (
            path.includes(category)
                ? [category, ...expand(this.childCategories(category.id))]
                : [category]
        ))

        return expand(this.rootCategories())
    }

    categoryWithDescendantIds(categoryId) {
        const ids = new Set([categoryId])

        const pending = [categoryId]

        while (pending.length) {
            for (const child of this.childCategories(pending.shift())) {
                if (!ids.has(child.id)) {
                    ids.add(child.id)

                    pending.push(child.id)
                }
            }
        }

        return ids
    }

    selectCategory(categoryId) {
        if (this.state.categoryId !== categoryId) {
            this.state.categoryId = categoryId

            return
        }

        const parentId = this.master.categories.get(categoryId)?.parent_id

        this.state.categoryId = this.master.categories.get(parentId) ? parentId : null
    }

    searchProducts(term, categoryId) {
        const needle = term.trim().toLowerCase()

        const categoryIds = categoryId ? this.categoryWithDescendantIds(categoryId) : null

        return this.master.products.filter((product) => {
            if (product.parent_id || product.is_hidden) {
                return false
            }

            if (categoryIds && !(product.category_ids ?? []).some((id) => categoryIds.has(id))) {
                return false
            }

            if (!needle) {
                return true
            }

            return [product.name, product.reference, product.barcode]
                .some((field) => field && String(field).toLowerCase().includes(needle))
        })
    }

    get cameraScanSupported() {
        return typeof window !== 'undefined' && 'BarcodeDetector' in window
    }

    startWedge() {
        if (this.wedge) {
            return
        }

        let buffer = ''
        let timer = null

        this.wedge = (event) => {
            const editing = event.target instanceof HTMLInputElement
                || event.target instanceof HTMLTextAreaElement
                || event.target instanceof HTMLSelectElement
                || event.target?.isContentEditable

            if (event.key === 'Enter') {
                const code = buffer.trim()

                buffer = ''

                if (code.length < 3) {
                    return
                }

                if (this.scan(code) && editing) {
                    event.preventDefault()
                }

                return
            }

            if (event.key.length !== 1) {
                return
            }

            buffer += event.key

            window.clearTimeout(timer)

            timer = window.setTimeout(() => { buffer = '' }, 120)
        }

        window.addEventListener('keydown', this.wedge)
    }

    startKeyboard() {
        if (this.keyboard) {
            return
        }

        let keys = []

        const flush = () => {
            const pressed = keys

            keys = []

            if (pressed.length > KEYBOARD_BURST_LIMIT || pressed.some((key) => !BUFFER_KEYS.has(key))) {
                return
            }

            pressed.forEach((key) => this.pressKeyboardKey(key))
        }

        this.keyboard = (event) => {
            if (event.repeat || !this.acceptsKeyboard(event) || MODIFIER_KEYS.has(event.key)) {
                return
            }

            keys.push(event.key)

            window.clearTimeout(this.keyboardTimer)

            this.keyboardTimer = window.setTimeout(flush, KEYBOARD_BURST_WINDOW)
        }

        window.addEventListener('keydown', this.keyboard)
    }

    acceptsKeyboard(event) {
        if (!this.state.ready || event.ctrlKey || event.metaKey || event.altKey) {
            return false
        }

        const target = event.target

        const editing = target instanceof HTMLInputElement
            || target instanceof HTMLTextAreaElement
            || target instanceof HTMLSelectElement
            || target?.isContentEditable

        if (editing || this.state.cameraScanning || document.querySelector('.fi-modal-open')) {
            return false
        }

        return this.state.screen === 'products' || this.state.screen === 'payment'
    }

    pressKeyboardKey(key) {
        if (this.state.screen === 'payment') {
            this.pressPaymentKey(key)

            return
        }

        this.pressNumpad(key)
    }

    startMenuListeners() {
        this.menuHandlers = {
            'pos-open-orders': () => this.openHistory(),
        }

        for (const [event, handler] of Object.entries(this.menuHandlers)) {
            window.addEventListener(event, handler)
        }
    }

    stopMenuListeners() {
        if (!this.menuHandlers) {
            return
        }

        for (const [event, handler] of Object.entries(this.menuHandlers)) {
            window.removeEventListener(event, handler)
        }

        this.menuHandlers = null
    }

    cancelOpening() {
        window.pointOfSaleShell.showPicker()
    }

    async confirmOpening() {
        this.state.openingError = null
        this.state.openingBusy = true

        try {
            await this.request(this.boot.session.opening_control_endpoint, 'POST', {
                cash_balance_start: Number(this.state.openingCash) || 0,
                opening_notes: this.state.openingNotes || null,
            })

            this.boot.session.state = 'opened'

            this.state.openingOpen = false

            if (this.isRestaurant) {
                this.goToFloor()
            } else {
                this.state.screen = 'products'
            }
        } catch (error) {
            this.state.openingError = error.message
        } finally {
            this.state.openingBusy = false
        }
    }

    get needsOpeningControl() {
        return this.boot.session.state === 'opening_control'
    }

    get previousClosingBalance() {
        return this.boot.session.previous_closing_balance ?? null
    }

    get hasCashCountMethod() {
        return Boolean(this.boot.session.has_cash_payment_method)
    }

    roundAmount(amount) {
        return Math.round((Number(amount) || 0) * 100) / 100
    }

    get moneyBills() {
        return this.master.bills.all().slice().sort((a, b) => Number(b.value) - Number(a.value))
    }

    openMoneyDetails(target = 'opening') {
        this.state.moneyDetailsTarget = target
        this.state.moneyDetailsOpen = true
    }

    stepMoneyDetail(billId, step) {
        const current = Number(this.state.moneyDetails[billId] ?? 0)

        this.state.moneyDetails[billId] = Math.max(0, current + step)
    }

    setMoneyDetail(billId, value) {
        this.state.moneyDetails[billId] = Math.max(0, Math.trunc(Number(value) || 0))
    }

    moneyDetailsTotal() {
        return this.roundAmount(this.moneyBills.reduce(
            (total, bill) => total + (Number(bill.value) || 0) * Number(this.state.moneyDetails[bill.id] ?? 0),
            0,
        ))
    }

    moneyDetailsNote(total) {
        if (this.roundAmount(total) === 0) {
            return ''
        }

        const heading = this.state.moneyDetailsTarget === 'closing'
            ? this.t('money-details.closing-heading')
            : this.t('money-details.opening-heading')

        let note = `${heading}\n`

        for (const bill of this.moneyBills.slice().sort((a, b) => Number(a.value) - Number(b.value))) {
            const quantity = Number(this.state.moneyDetails[bill.id] ?? 0)

            if (quantity === 0) {
                continue
            }

            note += `\t${quantity} x ${this.money(Number(bill.value))}\n`
        }

        return note + this.t('money-details.total', { total: this.money(total) })
    }

    confirmMoneyDetails() {
        const total = this.moneyDetailsTotal()

        if (this.state.moneyDetailsTarget === 'closing') {
            this.state.closingCashReal = total
            this.state.closingNotes = this.moneyDetailsNote(total)
        } else {
            this.state.openingCash = total
            this.state.openingNotes = this.moneyDetailsNote(total)
        }

        this.state.moneyDetailsOpen = false
    }

    toggleCashMoves() {
        this.state.showCashMoves = !this.state.showCashMoves
    }

    cashDifference(cash) {
        return this.state.closingCashReal === '' || this.state.closingCashReal === null
            ? 0
            : this.roundAmount(Number(this.state.closingCashReal) - Number(cash.amount))
    }

    paymentDifference(method) {
        const counted = this.state.closingCounted[method.id]

        return counted === undefined || counted === null || counted === ''
            ? 0
            : this.roundAmount(Number(counted) - Number(method.amount))
    }

    exceedsAuthorizedDifference(control) {
        if (control.amount_authorized_diff === null || control.amount_authorized_diff === undefined) {
            return false
        }

        const cash = control.default_cash_details ?? null

        const differences = Object.values(this.closingPaymentDifferences())

        if (cash) {
            differences.push(this.cashDifference(cash))
        }

        const largest = differences.reduce((max, value) => Math.max(max, Math.abs(Number(value) || 0)), 0)

        return this.roundAmount(largest) > this.roundAmount(Number(control.amount_authorized_diff))
    }

    copyExpectedCash() {
        const cash = this.state.closingReport?.default_cash_details

        if (cash) {
            this.state.closingCashReal = this.roundAmount(Number(cash.amount))
        }
    }

    copyExpectedPayment(methodId, amount) {
        this.state.closingCounted[methodId] = this.roundAmount(Number(amount))
    }

    openCash(type = 'in') {
        this.state.cashType = type
        this.state.cashAmount = ''
        this.state.cashReason = ''
        this.state.cashError = null
        this.state.cashOpen = true
    }

    async submitCash() {
        const amount = Number(this.state.cashAmount)

        if (!(amount > 0)) {
            this.state.cashError = this.t('cash-movement.amount')

            return
        }

        this.state.cashError = null
        this.state.cashBusy = true

        try {
            await this.recordCashMovement(this.state.cashType, amount, this.state.cashReason || null)

            this.state.cashOpen = false
        } catch (error) {
            this.state.cashError = error.message
        } finally {
            this.state.cashBusy = false
        }
    }

    async openClosing() {
        this.state.closingOpen = true
        this.state.closingError = null
        this.state.closingDrafts = false
        this.state.closingUnbalanced = null
        this.state.closingReport = null
        this.state.closingBusy = true

        try {
            this.state.closingReport = await this.loadClosingControl()
        } catch (error) {
            this.state.closingError = error.message
        } finally {
            this.state.closingBusy = false
        }
    }

    closingPaymentDifferences() {
        const report = this.state.closingReport

        if (!report) {
            return {}
        }

        const differences = {}

        for (const method of report.non_cash_payment_methods ?? []) {
            const counted = this.state.closingCounted[method.id]

            if (counted !== undefined && counted !== '') {
                differences[method.id] = Number(counted) - Number(method.amount)
            }
        }

        return differences
    }

    async submitClose() {
        this.state.closingError = null
        this.state.closingDrafts = false
        this.state.closingUnbalanced = null
        this.state.closingBusy = true

        try {
            const result = await this.closeRegister({
                cashBalanceEndReal: this.state.closingCashReal === '' ? null : Number(this.state.closingCashReal),
                closingNotes: this.state.closingNotes || null,
                balancingAccountId: this.state.balancingAccountId,
                paymentDifferences: this.closingPaymentDifferences(),
            })

            if (result.ok) {
                this.state.closingOpen = false

                window.localStorage.removeItem('pos-active-session')

                window.pointOfSaleShell.showPicker()

                return
            }

            if (result.code === 'draft-orders') {
                this.state.closingDrafts = true
            } else if (result.code === 'unbalanced') {
                this.state.closingUnbalanced = result.delta ?? null
            } else {
                this.state.closingError = result.message ?? this.t('history.failed', { status: result.status })
            }
        } catch (error) {
            this.state.closingError = error.message
        } finally {
            this.state.closingBusy = false
        }
    }

    async confirmCancelDraftOrders() {
        this.state.closingBusy = true

        try {
            await this.cancelDraftOrders()

            this.state.closingDrafts = false

            await this.submitClose()
        } catch (error) {
            this.state.closingError = error.message
        } finally {
            this.state.closingBusy = false
        }
    }

    async openHistory(status = null) {
        if (status) {
            this.state.historyStatus = status
            this.state.historySearch = ''
        }

        this.state.historyOpen = true
        this.state.historySelected = null
        this.state.historyPage = 1

        await this.refreshHistory()
    }

    async refreshHistory() {
        this.state.historyError = null

        if (!navigator.onLine) {
            this.state.history = this.localOrderHistory()
            this.state.historyPage = 1
            this.state.historyLastPage = 1
            this.state.historyLoading = false

            return
        }

        this.state.historyLoading = true

        try {
            const payload = await this.loadOrderHistory({
                search: this.state.historySearch,
                status: this.state.historyStatus,
                page: this.state.historyPage,
            })

            this.state.history = payload.data ?? []
            this.state.historyLastPage = payload.last_page ?? 1
        } catch (error) {
            this.state.history = this.localOrderHistory()
            this.state.historyLastPage = 1
            this.state.historyError = error.message
        } finally {
            this.state.historyLoading = false
        }
    }

    localOrderHistory() {
        const needle = this.state.historySearch.trim().toLowerCase()

        const status = this.state.historyStatus

        return this.state.orders
            .filter((order) => order.lines.length > 0)
            .filter((order) => {
                if (status === 'active') {
                    if (order.state === 'cancel') {
                        return false
                    }
                } else if (order.state !== status) {
                    return false
                }

                if (!needle) {
                    return true
                }

                const haystack = [
                    this.orderLabel(order),
                    order.pos_reference,
                    order.name,
                    order.tracking_number,
                    this.master.partners.get(order.partner_id)?.name,
                    ...order.lines.map((line) => this.master.products.get(line.product_id)?.name),
                ]

                return haystack.some((field) => field && String(field).toLowerCase().includes(needle))
            })
            .map((order) => this.historyRowFromLocal(order))
    }

    historyRowFromLocal(order) {
        const totals = this.orderTotals(order)

        return {
            id: order.serverId ?? order.uuid,
            uuid: order.uuid,
            ordered_at: order.created_at ?? null,
            reference: order.pos_reference,
            name: order.name ?? order.pos_reference,
            tracking_number: order.tracking_number,
            state: order.state,
            amount_total: totals.total,
            amount_tax: totals.tax,
            is_invoiced: false,
            cashier: '',
            partner: order.partner_id ? { name: this.master.partners.get(order.partner_id)?.name ?? null } : null,
            table: null,
            lines: order.lines.map((line) => {
                const lineTotals = this.lineTotals(line)

                return {
                    id: line.uuid,
                    full_product_name: this.master.products.get(line.product_id)?.name ?? '',
                    qty: line.qty,
                    price_unit: line.price_unit,
                    price_subtotal_incl: floatRound(lineTotals.subtotal + lineTotals.tax, { precisionDigits: 4 }),
                }
            }),
        }
    }

    async goToHistoryPage(page) {
        if (page < 1 || page > this.state.historyLastPage) {
            return
        }

        this.state.historyPage = page

        await this.refreshHistory()
    }

    invoiceHistoryOrder(order) {
        if (order.partner) {
            this.runInvoice(order)

            return
        }

        this.state.customerSearch = ''
        this.state.invoiceCustomerOrder = order
        this.state.invoiceCustomerStep = 'confirm'
    }

    confirmInvoiceCustomer() {
        this.state.invoiceCustomerStep = 'select'
    }

    cancelInvoiceCustomer() {
        this.state.invoiceCustomerOrder = null
        this.state.invoiceCustomerStep = null
        this.state.customerSearch = ''
    }

    async chooseInvoiceCustomer(partnerId) {
        const order = this.state.invoiceCustomerOrder

        this.cancelInvoiceCustomer()

        if (order) {
            await this.runInvoice(order, partnerId)
        }
    }

    async runInvoice(order, partnerId = null) {
        this.state.historyBusyId = order.id

        try {
            const updated = await this.invoiceServerOrder(order.id, partnerId)

            if (updated) {
                order.is_invoiced = updated.is_invoiced ?? true
                order.state = updated.state ?? order.state

                if (partnerId) {
                    const partner = this.master.partners.get(partnerId)

                    if (partner) {
                        order.partner = { id: partner.id, name: partner.name }
                    }
                }
            }
        } catch (error) {
            this.state.historyError = error.message
        } finally {
            this.state.historyBusyId = null
        }
    }

    async cancelHistoryOrder(order) {
        this.state.historyBusyId = order.id

        try {
            await this.cancelServerOrder(order.uuid)

            await this.refreshHistory()
        } catch (error) {
            this.state.historyError = error.message
        } finally {
            this.state.historyBusyId = null
        }
    }

    async selectHistoryOrder(order) {
        this.state.historySelected = order
        this.state.refundLines = {}
        this.state.refundActiveLine = null
        this.state.refundBuffer = ''
        this.state.refundError = null

        if (!order || order.state === 'cancel' || order.state === 'draft' || !navigator.onLine || typeof order.id !== 'number') {
            return
        }

        try {
            const lines = await this.fetchRefundableLines(order.id)

            const map = {}

            for (const [lineId, refundable] of Object.entries(lines)) {
                if (Number(refundable) > 0) {
                    map[lineId] = { refundable: Number(refundable), qty: 0 }
                }
            }

            this.state.refundLines = map
        } catch (error) {
            this.state.refundError = error.message
        }
    }

    setRefundQty(lineId, value) {
        const line = this.state.refundLines[lineId]

        if (!line) {
            return
        }

        const requested = Math.max(Number(value) || 0, 0)

        line.qty = Math.min(requested, line.refundable)
    }

    selectRefundLine(lineId) {
        if (!this.state.refundLines[lineId]) {
            return
        }

        this.state.refundActiveLine = lineId
        this.state.refundBuffer = ''
    }

    pressRefund(key) {
        const lineId = this.state.refundActiveLine

        const line = lineId !== null ? this.state.refundLines[lineId] : null

        if (!line) {
            return
        }

        if (key === 'backspace') {
            this.state.refundBuffer = this.state.refundBuffer.slice(0, -1)
        } else if (key === 'clear') {
            this.state.refundBuffer = ''
        } else if (key === '.') {
            if (!this.state.refundBuffer.includes('.')) {
                this.state.refundBuffer = (this.state.refundBuffer || '0') + '.'
            }
        } else {
            this.state.refundBuffer += key
        }

        const requested = Number(this.state.refundBuffer || 0)

        line.qty = Math.min(Math.max(requested, 0), line.refundable)
    }

    refundTotalQty() {
        return Object.values(this.state.refundLines).reduce((sum, line) => sum + Number(line.qty || 0), 0)
    }

    canRefundSelected() {
        return Object.keys(this.state.refundLines).length > 0
    }

    async submitRefund() {
        const order = this.state.historySelected

        if (!order) {
            return
        }

        const quantities = {}

        for (const [lineId, line] of Object.entries(this.state.refundLines)) {
            if (Number(line.qty) > 0) {
                quantities[lineId] = Number(line.qty)
            }
        }

        if (!Object.keys(quantities).length) {
            return
        }

        this.state.refundError = null
        this.state.historyBusyId = order.id

        try {
            const pending = await this.prepareRefund(order.id, quantities)

            const refundOrder = this.openPendingRefund(pending)

            if (refundOrder) {
                this.state.activeOrderUuid = refundOrder.uuid
                this.state.historyOpen = false
                this.state.historySelected = null
                this.state.refundLines = {}
                this.state.refundActiveLine = null
                this.state.refundBuffer = ''
                this.state.screen = 'products'
            }
        } catch (error) {
            this.state.refundError = error.message
        } finally {
            this.state.historyBusyId = null
        }
    }

    stop() {
        this.queue.stop()

        this.stopMenuListeners()

        window.removeEventListener('point-of-sale:queue-flushed', this.onQueueFlushed)

        this.stopActiveOrderWatch?.()

        this.stopPersistenceWatch?.()

        if (this.persistTimer) {
            window.clearTimeout(this.persistTimer)

            this.persistTimer = null

            this.persist()
        }

        if (this.wedge) {
            window.removeEventListener('keydown', this.wedge)

            this.wedge = null
        }

        if (this.keyboard) {
            window.removeEventListener('keydown', this.keyboard)

            window.clearTimeout(this.keyboardTimer)

            this.keyboard = null
        }

        if (this.draftRefresh) {
            window.clearInterval(this.draftRefresh)

            this.draftRefresh = null
        }

        if (this.idleWatch) {
            window.removeEventListener('pointerdown', this.idleWatch)
            window.removeEventListener('keydown', this.idleWatch)

            window.clearTimeout(this.idleTimer)

            this.idleWatch = null
        }
    }

    scan(code) {
        const product = this.productByBarcode(code)

        if (product) {
            this.pickProduct(product.id)

            return true
        }

        const partner = this.master.partners.firstBy('barcode', code)

        if (partner) {
            this.selectCustomer(partner.id)

            return true
        }

        const line = this.activeLine

        if (line && this.isTracked(line.product_id) && this.lotsEnabled) {
            this.state.lotLineUuid = line.uuid
            this.state.lotRows = [...(line.lots ?? []).map((lot) => lot.lot_name), code]

            this.confirmLots()

            return true
        }

        return false
    }

    productByBarcode(barcode) {
        return this.master.products.firstBy('barcode', barcode)
    }

    freeQty(productId) {
        return Number(this.stock[String(productId)] ?? this.stock[productId] ?? 0)
    }

    addPayment(paymentMethodId, amount = null) {
        const order = this.activeOrder

        if (!order) {
            return null
        }

        const totals = this.orderTotals(order)

        if (amount === null) {
            const outstanding = this.outstandingFor(totals)

            const reusable = order.payments.find((line) => (
                line.payment_method_id === paymentMethodId
                && !line.is_change
                && (floatIsZero(line.amount, { precisionRounding: this.currency.rounding })
                    || floatIsZero(outstanding, { precisionRounding: this.currency.rounding }))
            ))

            if (reusable) {
                if (!floatIsZero(outstanding, { precisionRounding: this.currency.rounding })) {
                    reusable.amount = outstanding
                }

                this.selectPayment(reusable.uuid)

                return reusable
            }
        }

        const payment = reactive({
            uuid: uuidv4(),
            order_uuid: order.uuid,
            payment_method_id: paymentMethodId,
            amount: amount === null ? 0 : amount,
            is_change: false,
        })

        order.payments.push(payment)

        if (amount === null) {
            payment.amount = this.outstandingFor(this.orderTotals(order))
        }

        this.selectPayment(payment.uuid)

        return payment
    }

    outstandingFor(totals) {
        return totals.refund ? Math.min(totals.due, 0) : Math.max(totals.due, 0)
    }

    toggleToInvoice() {
        const order = this.activeOrder

        if (order) {
            order.to_invoice = !order.to_invoice
        }
    }

    canShipLater() {
        return Boolean(this.config.enable_ship_later)
    }

    get today() {
        return new Date().toISOString().split('T')[0]
    }

    toggleShipLater() {
        const order = this.activeOrder

        if (!order) {
            return
        }

        if (order.shipped_at) {
            order.shipped_at = null

            return
        }

        this.state.shipLaterDraft = this.today
        this.state.shipLaterModalOpen = true
    }

    closeShipLater() {
        this.state.shipLaterModalOpen = false
    }

    dismissShipLaterError() {
        this.state.shipLaterError = null
    }

    confirmShipLater() {
        const order = this.activeOrder

        if (order) {
            const draft = this.state.shipLaterDraft

            order.shipped_at = !draft || draft < this.today ? this.today : draft
        }

        this.state.shipLaterModalOpen = false
    }

    get activePayment() {
        return this.activeOrder?.payments.find((payment) => payment.uuid === this.state.activePaymentUuid)
    }

    selectPayment(uuid) {
        this.state.activePaymentUuid = uuid

        this.resetPaymentBuffer()
    }

    resetPaymentBuffer() {
        this.state.paymentBuffer = ''
        this.state.paymentFresh = true
    }

    pressPaymentKey(key) {
        const order = this.activeOrder

        if (order && !order.payments.length && this.startsPaymentLine(key)) {
            const method = this.master.payment_methods.all()[0]

            if (method) {
                this.addPayment(method.id)
            }
        }

        const payment = this.activePayment

        if (!payment) {
            return
        }

        if (key === 'Backspace' && this.state.paymentFresh) {
            this.state.paymentBuffer = this.paymentAmountBuffer(payment)
            this.state.paymentFresh = false
        }

        const buffer = nextBuffer(this.state.paymentBuffer, key, this.state.paymentFresh)

        let amount = this.bufferAmount(buffer ?? '')

        if (!this.hasCashPaymentMethod) {
            const cap = this.orderTotals(order).due + payment.amount

            if (floatCompare(amount, cap, { precisionRounding: this.currency.rounding }) > 0) {
                amount = cap

                this.state.paymentBuffer = this.paymentAmountBuffer({ amount })
                this.state.paymentFresh = false

                payment.amount = amount

                this.notifyMaxPaymentValue()

                return
            }
        }

        this.state.paymentBuffer = buffer ?? ''
        this.state.paymentFresh = false

        payment.amount = amount
    }

    get hasCashPaymentMethod() {
        return this.master.payment_methods.all().some((method) => method.is_cash_count)
    }

    notifyMaxPaymentValue() {
        this.pushToast({
            title: this.t('payment.max-value-title'),
            body: this.t('payment.max-value-body'),
        })
    }

    startsPaymentLine(key) {
        return /^\d$/.test(key) || /^\+\d+(\.\d+)?$/.test(key)
    }

    paymentAmountBuffer(payment) {
        if (floatIsZero(payment.amount, { precisionRounding: this.currency.rounding })) {
            return ''
        }

        return String(floatRound(payment.amount, { precisionRounding: this.currency.rounding }))
    }

    removePayment(uuid) {
        const order = this.activeOrder

        if (!order) {
            return
        }

        this.database.remove('pos.payment', [uuid])

        order.payments = order.payments.filter((payment) => payment.uuid !== uuid)

        if (this.state.activePaymentUuid === uuid) {
            this.state.activePaymentUuid = order.payments[0]?.uuid ?? null

            this.resetPaymentBuffer()
        }
    }

    sellableLines(order) {
        return order.lines.filter((line) => !floatIsZero(line.qty, { precisionDigits: 4 }))
    }

    canValidate(order = this.activeOrder) {
        if (!order || !this.sellableLines(order).length) {
            return false
        }

        return this.orderTotals(order).covered
    }

    canAttemptValidate(order = this.activeOrder) {
        return Boolean(order && this.sellableLines(order).length)
    }

    notifyIncompletePayment() {
        this.pushToast({
            title: this.t('payment.incomplete-title'),
            body: this.t('payment.incomplete-body'),
        })
    }

    customerRequiredReason(order) {
        if (this.config.enable_customer_required) {
            return 'terminal'
        }

        if (order.payments.some((payment) => this.master.payment_methods.get(payment.payment_method_id)?.split_transactions)) {
            return 'payment-method'
        }

        if (order.to_invoice) {
            return 'invoice'
        }

        return null
    }

    notifyCustomerRequired(reason) {
        this.pushToast({
            title: this.t('payment.customer-required-title'),
            body: this.t(`payment.customer-required-body-${reason}`),
        })
    }

    orderPayload(order) {
        const payments = order.payments
            .filter((payment) => !payment.is_change)
            .map((payment) => ({
                uuid: payment.uuid,
                payment_method_id: payment.payment_method_id,
                amount: payment.amount,
            }))

        const totals = this.orderTotals(order)

        const partnerDraft = this.partnerDraftFor(order)

        return {
            uuid: order.uuid,
            reference: order.pos_reference,
            tracking_number: order.tracking_number,
            sequence_number: order.sequence_number,
            config_id: this.config.id,
            session_id: this.boot.session.id,
            partner_id: partnerDraft ? null : order.partner_id,
            floating_name: order.floating_name || null,
            is_booked: Boolean(order.is_booked),
            ...(order.preparation_state ? { preparation_state: order.preparation_state } : {}),
            partner: partnerDraft,
            price_list_id: order.price_list_id,
            fiscal_position_id: order.fiscal_position_id,
            is_takeaway: order.is_takeaway,
            table_id: order.table_id ?? null,
            customer_count: order.customer_count ?? 0,
            is_to_invoice: order.to_invoice,
            refunded_order_id: order.refunded_order_id ?? null,
            shipped_at: order.shipped_at,
            ordered_at: order.validated_at ?? null,
            note: order.note,
            amount_total: totals.total,
            lines: this.sellableLines(order).map((line) => {
                const payload = {
                    uuid: line.uuid,
                    product_id: line.product_id,
                    qty: line.qty,
                    discount: line.discount,
                    note: line.note,
                    tax_ids: line.tax_ids,
                }

                if (line.price_overridden) {
                    payload.price_unit = line.price_unit
                    payload.price_type = 'manual'
                }

                if (line.refunded_order_line_id) {
                    payload.refunded_order_line_id = line.refunded_order_line_id
                }

                if (line.lots?.length) {
                    payload.lots = line.lots.map((lot) => ({
                        lot_name: lot.lot_name,
                        lot_id: lot.lot_id ?? null,
                        qty: Number(lot.qty ?? 1),
                    }))
                }

                return payload
            }),
            payments,
        }
    }

    validate() {
        const order = this.activeOrder

        if (!order || !this.sellableLines(order).length) {
            return null
        }

        if (!this.canValidate(order)) {
            this.notifyIncompletePayment()

            return null
        }

        if (!order.partner_id) {
            const reason = this.customerRequiredReason(order)

            if (reason) {
                this.notifyCustomerRequired(reason)

                return null
            }
        }

        if (order.shipped_at) {
            if (!order.partner_id) {
                this.state.shipLaterError = this.t('payment.ship-later-no-customer')

                return null
            }

            if (!this.master.partners.get(order.partner_id)?.has_delivery_address) {
                this.state.shipLaterError = this.t('payment.ship-later-no-address')

                return null
            }
        }

        const totals = this.orderTotals(order)

        if (totals.change > 0) {
            order.payments.push(reactive({
                uuid: `${order.uuid}-change`,
                order_uuid: order.uuid,
                payment_method_id: this.master.payment_methods.find((method) => method.is_cash_count)?.id ?? null,
                amount: -totals.change,
                is_change: true,
            }))
        }

        const kitchenTickets = this.sendsToKitchen && !this.isRestaurant
            ? this.preparationTickets(order, this.preparationChanges(order))
            : []

        if (kitchenTickets.length) {
            this.markPreparationSent(order)
        }

        order.state = 'paid'
        order.validated_at = new Date().toISOString()

        this.queue.push({ uuid: order.uuid, payload: this.orderPayload(order) })

        this.printKitchenTickets(kitchenTickets)

        this.state.autoPrintOrderUuid = this.printsReceiptAutomatically ? order.uuid : null

        this.state.screen = 'receipt'

        this.queue.flush()

        return order
    }
}

export function useTotals(till) {
    return computed(() => till.orderTotals())
}
