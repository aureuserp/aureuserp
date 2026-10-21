export const TRANSACTIONAL_MODELS = {
    'pos.order': { key: 'uuid', indexes: ['uuid'] },
    'pos.order.line': { key: 'uuid', indexes: ['uuid', 'order_uuid'] },
    'pos.payment': { key: 'uuid', indexes: ['uuid', 'order_uuid'] },
}

export const MASTER_INDEXES = {
    products: ['barcode'],
    partners: ['barcode'],
    taxes: [],
    categories: ['parent_id'],
    payment_methods: [],
    price_lists: [],
    fiscal_positions: [],
    bills: [],
    notes: [],
    uoms: [],
    currencies: [],
}

export class RecordSet {
    constructor(name, { key = 'id', indexes = [] } = {}) {
        this.name = name
        this.key = key
        this.byKey = new Map()
        this.indexNames = indexes
        this.indexes = new Map(indexes.map((index) => [index, new Map()]))
    }

    all() {
        return [...this.byKey.values()]
    }

    get size() {
        return this.byKey.size
    }

    get(key) {
        return this.byKey.get(key)
    }

    getBy(index, value) {
        const bucket = this.indexes.get(index)

        if (!bucket) {
            return []
        }

        return bucket.get(value) ?? []
    }

    firstBy(index, value) {
        return this.getBy(index, value)[0]
    }

    filter(predicate) {
        return this.all().filter(predicate)
    }

    find(predicate) {
        return this.all().find(predicate)
    }

    put(record) {
        const key = record[this.key]

        if (key === undefined || key === null) {
            throw new Error(`record for ${this.name} is missing its key ${this.key}`)
        }

        const previous = this.byKey.get(key)

        if (previous) {
            this.unindex(previous)
        }

        this.byKey.set(key, record)

        this.index(record)

        return record
    }

    putMany(records) {
        return records.map((record) => this.put(record))
    }

    delete(key) {
        const record = this.byKey.get(key)

        if (!record) {
            return false
        }

        this.unindex(record)

        this.byKey.delete(key)

        return true
    }

    clear() {
        this.byKey.clear()

        for (const bucket of this.indexes.values()) {
            bucket.clear()
        }
    }

    index(record) {
        for (const name of this.indexNames) {
            const value = record[name]

            if (value === undefined || value === null || value === '') {
                continue
            }

            const bucket = this.indexes.get(name)

            const existing = bucket.get(value)

            if (existing) {
                existing.push(record)
            } else {
                bucket.set(value, [record])
            }
        }
    }

    unindex(record) {
        for (const name of this.indexNames) {
            const value = record[name]

            if (value === undefined || value === null || value === '') {
                continue
            }

            const bucket = this.indexes.get(name)

            const existing = bucket.get(value)

            if (!existing) {
                continue
            }

            const position = existing.indexOf(record)

            if (position !== -1) {
                existing.splice(position, 1)
            }

            if (existing.length === 0) {
                bucket.delete(value)
            }
        }
    }
}

export function createRecordSets(definitions) {
    const sets = {}

    for (const [name, options] of Object.entries(definitions)) {
        sets[name] = new RecordSet(name, options)
    }

    return sets
}
