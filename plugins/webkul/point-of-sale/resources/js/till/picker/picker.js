import { reactive } from 'vue'

function csrf() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
}

export class Picker {
    constructor(endpoints, strings, registers = [], configId = null) {
        this.endpoints = endpoints
        this.strings = strings ?? {}
        this.configId = configId

        this.state = reactive({
            loading: registers.length === 0,
            error: null,
            offline: !navigator.onLine,
            busyConfigId: null,
            confirmingConfigId: null,
            registers,
        })
    }

    t(key, replacements = {}) {
        const value = key.split('.').reduce((carry, part) => (carry == null ? carry : carry[part]), this.strings)

        if (typeof value !== 'string') {
            return key
        }

        return Object.entries(replacements).reduce(
            (text, [token, replacement]) => text.replaceAll(`:${token}`, replacement),
            value,
        )
    }

    async request(url, method = 'GET', body = null) {
        let response

        try {
            response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrf(),
                },
                body: body ? JSON.stringify(body) : null,
            })
        } catch {
            throw new Error(this.t('picker.failed'))
        }

        const payload = await response.json().catch(() => ({}))

        if (!response.ok) {
            throw new Error(payload.message ?? this.t('picker.failed'))
        }

        return payload
    }

    async load() {
        this.state.offline = !navigator.onLine

        if (!navigator.onLine) {
            this.state.loading = false

            return
        }

        this.state.loading = this.state.registers.length === 0
        this.state.error = null

        try {
            const payload = await this.request(this.endpoints.registers)

            const all = payload.data ?? []

            this.state.registers = this.configId
                ? all.filter((register) => register.id === this.configId)
                : all
        } catch (error) {
            if (this.state.registers.length === 0) {
                this.state.error = error.message
            }
        } finally {
            this.state.loading = false
        }
    }

    async open(register) {
        if (register.live_session) {
            await this.resume(register.live_session.id)

            return
        }

        if (!navigator.onLine) {
            this.state.error = this.t('picker.offline')

            return
        }

        this.state.error = null
        this.state.busyConfigId = register.id

        try {
            const opened = await this.request(this.endpoints.open.replace('__config__', register.id), 'POST')

            await this.enterTerminal(opened.data.id)
        } catch (error) {
            this.state.error = error.message
            this.state.busyConfigId = null
        }
    }

    async resume(sessionId) {
        if (!navigator.onLine) {
            this.state.error = this.t('picker.offline')

            return
        }

        this.state.error = null

        try {
            await this.enterTerminal(sessionId)
        } catch (error) {
            this.state.error = error.message
        }
    }

    async enterTerminal(sessionId) {
        const boot = await this.request(this.endpoints.boot.replace('__session__', sessionId), 'GET')

        window.pointOfSaleShell.openTerminal(boot.data)
    }

    async discard(register) {
        this.state.error = null
        this.state.busyConfigId = register.id

        try {
            await this.request(this.endpoints.discard.replace('__config__', register.id), 'POST')

            this.state.confirmingConfigId = null

            await this.load()
        } catch (error) {
            this.state.error = error.message
        } finally {
            this.state.busyConfigId = null
        }
    }
}
