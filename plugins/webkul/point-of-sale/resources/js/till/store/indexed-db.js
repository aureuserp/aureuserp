const RECONNECT_DELAY = 3000

const MAX_READ_RETRIES = 5

export class TillDatabase {
    constructor(name, version, stores, { onFatal = null } = {}) {
        this.db = null
        this.name = name
        this.version = version
        this.stores = stores
        this.driver = null
        this.onFatal = onFatal
        this.reconnecting = false
        this.fatalReported = false
        this.visibilityProbeAttached = false

        this.connect()
    }

    connect() {
        const driver = window.indexedDB
            || window.mozIndexedDB
            || window.webkitIndexedDB
            || window.msIndexedDB

        if (!driver) {
            this.reportFatal('unsupported')

            return
        }

        this.driver = driver

        const request = driver.open(this.name, this.version)

        request.onerror = (event) => {
            const error = event.target.error

            if (error?.name === 'UnknownError' && String(error.message).includes('Connection to Indexed Database server lost')) {
                this.reportFatal('connection-lost')
            }
        }

        request.onsuccess = (event) => {
            this.db = event.target.result

            this.attachVisibilityProbe()
        }

        request.onupgradeneeded = (event) => {
            for (const [keyPath, storeName] of this.stores) {
                if (!event.target.result.objectStoreNames.contains(storeName)) {
                    event.target.result.createObjectStore(storeName, { keyPath })
                }
            }
        }
    }

    reportFatal(reason) {
        if (this.fatalReported) {
            return
        }

        this.fatalReported = true

        if (this.onFatal) {
            this.onFatal(reason)
        }
    }

    transaction(storeNames, mode = 'readwrite') {
        try {
            if (!this.db) {
                return false
            }

            return this.db.transaction(storeNames, mode)
        } catch (error) {
            if (error.name === 'InvalidStateError') {
                this.db = null

                this.reconnect()
            }

            return false
        }
    }

    reconnect() {
        if (this.reconnecting) {
            return
        }

        this.reconnecting = true

        window.setTimeout(() => {
            if (this.db) {
                try {
                    this.db.close()
                } catch {
                    this.db = null
                }

                this.db = null
            }

            this.connect()

            this.reconnecting = false
        }, RECONNECT_DELAY)
    }

    attachVisibilityProbe() {
        if (this.visibilityProbeAttached) {
            return
        }

        this.visibilityProbeAttached = true

        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState !== 'visible' || !this.db) {
                return
            }

            try {
                this.db.transaction([this.stores[0][1]], 'readonly').abort()
            } catch {
                this.db = null

                this.reconnect()
            }
        })
    }

    write(storeName, records) {
        return this.run(storeName, records, 'put')
    }

    remove(storeName, keys) {
        return this.run(storeName, keys, 'delete')
    }

    run(storeName, records, method) {
        if (!records.length) {
            return Promise.resolve([])
        }

        const transaction = this.transaction([storeName], 'readwrite')

        if (!transaction) {
            return Promise.resolve(false)
        }

        const store = transaction.objectStore(storeName)

        return Promise.allSettled(records.map((record) => new Promise((resolve, reject) => {
            const request = store[method](method === 'delete' ? record : JSON.parse(JSON.stringify(record)))

            request.onsuccess = () => resolve()
            request.onerror = () => reject(request.error)
        })))
    }

    readAll(storeNames = [], retry = 0) {
        const names = storeNames.length > 0 ? storeNames : this.stores.map(([, name]) => name)

        const transaction = this.transaction(names, 'readonly')

        if (!transaction) {
            if (retry < MAX_READ_RETRIES) {
                return new Promise((resolve) => {
                    window.setTimeout(() => resolve(this.readAll(storeNames, retry + 1)), 120)
                })
            }

            return Promise.resolve({})
        }

        return Promise.allSettled(names.map((name) => new Promise((resolve, reject) => {
            const request = transaction.objectStore(name).getAll()

            request.onsuccess = (event) => resolve({ [name]: event.target.result })
            request.onerror = () => reject(request.error)
        }))).then((results) => results.reduce((carry, result) => (
            result.status === 'fulfilled' ? { ...carry, ...result.value } : carry
        ), {}))
    }

    reset() {
        if (!this.driver) {
            return
        }

        if (this.db) {
            try {
                this.db.close()
            } catch {
                this.db = null
            }
        }

        this.db = null

        this.driver.deleteDatabase(this.name)
    }
}
