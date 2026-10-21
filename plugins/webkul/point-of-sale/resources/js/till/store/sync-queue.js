import { reactive } from 'vue'

const FLUSH_INTERVAL = 15000

export const SETTLED_ELSEWHERE = 'already-settled'

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
}

export class SyncQueue {
    constructor(database, { endpoint }) {
        this.database = database
        this.endpoint = endpoint
        this.flushing = false
        this.timer = null
        this.removed = new Set()
        this.onStorage = (event) => this.adoptStored(event)
        this.onOnline = () => {
            this.state.offline = false

            this.flush()
        }
        this.onOffline = () => {
            this.state.offline = true
        }

        this.state = reactive({
            offline: !navigator.onLine,
            pending: [],
            lastError: null,
            lastSyncedAt: null,
        })
    }

    get pendingCount() {
        return this.state.pending.length
    }

    push(entry) {
        const existing = this.state.pending.find((pending) => pending.uuid === entry.uuid)

        if (existing) {
            return existing
        }

        const queued = {
            uuid: entry.uuid,
            payload: entry.payload,
            attempts: 0,
            lastError: null,
            queuedAt: new Date().toISOString(),
        }

        this.state.pending.push(queued)

        this.persist()

        return queued
    }

    remove(uuid) {
        this.removed.add(uuid)

        const position = this.state.pending.findIndex((pending) => pending.uuid === uuid)

        if (position !== -1) {
            this.state.pending.splice(position, 1)
        }

        this.persist()
    }

    get storageKey() {
        return `pos.sync-queue.${this.endpoint}`
    }

    readStored(raw = window.localStorage.getItem(this.storageKey)) {
        try {
            const entries = JSON.parse(raw ?? '[]')

            return Array.isArray(entries) ? entries : []
        } catch {
            return []
        }
    }

    persist() {
        try {
            const local = new Set(this.state.pending.map((pending) => pending.uuid))

            const others = this.readStored().filter((entry) => !local.has(entry.uuid) && !this.removed.has(entry.uuid))

            window.localStorage.setItem(this.storageKey, JSON.stringify([...others, ...this.state.pending]))
        } catch {
            this.state.lastError = 'queue could not be persisted'
        }
    }

    restore() {
        this.state.pending = this.readStored().filter((entry) => !this.removed.has(entry.uuid))
    }

    adoptStored(event) {
        if (event.key !== this.storageKey) {
            return
        }

        const stored = this.readStored(event.newValue).filter((entry) => !this.removed.has(entry.uuid))

        const storedUuids = new Set(stored.map((entry) => entry.uuid))

        const settled = this.state.pending.map((pending) => pending.uuid).filter((uuid) => !storedUuids.has(uuid))

        this.state.pending = stored

        if (settled.length) {
            window.dispatchEvent(new CustomEvent('point-of-sale:queue-flushed', {
                detail: { synced: settled.length, failed: 0, pending: this.pendingCount, uuids: settled },
            }))
        }
    }

    watch() {
        this.restore()

        window.addEventListener('storage', this.onStorage)

        window.addEventListener('online', this.onOnline)

        window.addEventListener('offline', this.onOffline)

        this.timer = window.setInterval(() => this.flush(), FLUSH_INTERVAL)

        this.flush()
    }

    stop() {
        window.removeEventListener('storage', this.onStorage)

        window.removeEventListener('online', this.onOnline)

        window.removeEventListener('offline', this.onOffline)

        if (this.timer) {
            window.clearInterval(this.timer)

            this.timer = null
        }
    }

    get sendable() {
        return this.state.pending.filter((pending) => !pending.rejected)
    }

    get rejected() {
        return this.state.pending.filter((pending) => pending.rejected)
    }

    retryRejected() {
        for (const pending of this.rejected.filter((entry) => !this.isSettledElsewhere(entry))) {
            pending.rejected = false
        }

        this.persist()

        return this.flush()
    }

    reject(pending, message, code = null) {
        pending.rejected = true
        pending.attempts += 1
        pending.lastError = message ?? 'unknown error'
        pending.code = code
        pending.acknowledged = false

        this.persist()
    }

    isSettledElsewhere(pending) {
        return pending.code === SETTLED_ELSEWHERE
    }

    acknowledge(uuid) {
        const pending = this.state.pending.find((entry) => entry.uuid === uuid)

        if (pending) {
            pending.acknowledged = true

            this.persist()
        }
    }

    dismiss(uuid) {
        this.remove(uuid)
    }

    invalidEntries(batch, errors) {
        const messages = new Map()

        for (const [key, value] of Object.entries(errors ?? {})) {
            const index = Number(key.match(/^orders\.(\d+)\./)?.[1])

            if (Number.isInteger(index) && batch[index] && !messages.has(index)) {
                messages.set(index, Array.isArray(value) ? value[0] : String(value))
            }
        }

        return [...messages].map(([index, message]) => ({ entry: batch[index], message }))
    }

    post(batch) {
        return fetch(this.endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify({ orders: batch.map((pending) => pending.payload) }),
        })
    }

    async flush() {
        if (this.flushing || this.sendable.length === 0 || !navigator.onLine) {
            return { synced: 0, failed: 0, pending: this.pendingCount }
        }

        this.flushing = true

        try {
            let batch = this.sendable

            let failed = 0

            let body = null

            while (batch.length) {
                const response = await this.post(batch)

                if (response.status === 422) {
                    const invalid = this.invalidEntries(batch, (await response.json().catch(() => ({}))).errors)

                    if (!invalid.length) {
                        this.state.lastError = `sync failed with status ${response.status}`

                        return { synced: 0, failed: 0, pending: this.pendingCount }
                    }

                    for (const { entry, message } of invalid) {
                        this.reject(entry, message)
                    }

                    failed += invalid.length

                    batch = this.sendable

                    continue
                }

                if (!response.ok) {
                    this.state.lastError = `sync failed with status ${response.status}`

                    return { synced: 0, failed, pending: this.pendingCount }
                }

                body = await response.json()

                break
            }

            const synced = (body?.data ?? []).map((entry) => entry.uuid).filter(Boolean)

            for (const uuid of synced) {
                this.remove(uuid)
            }

            for (const failure of (body?.errors ?? []).filter((entry) => entry.uuid)) {
                const pending = this.state.pending.find((entry) => entry.uuid === failure.uuid)

                if (pending) {
                    this.reject(pending, failure.message, failure.code ?? null)

                    failed += 1
                }
            }

            this.persist()

            this.state.lastSyncedAt = new Date().toISOString()

            this.state.lastError = failed ? `${failed} order(s) rejected` : null

            window.dispatchEvent(new CustomEvent('point-of-sale:queue-flushed', {
                detail: { synced: synced.length, failed, pending: this.pendingCount, uuids: synced },
            }))

            return { synced: synced.length, failed, pending: this.pendingCount }
        } catch (error) {
            this.state.offline = !navigator.onLine

            this.state.lastError = error.message

            return { synced: 0, failed: 0, pending: this.pendingCount }
        } finally {
            this.flushing = false
        }
    }
}
