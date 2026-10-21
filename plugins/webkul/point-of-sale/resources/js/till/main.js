import { createApp } from 'vue'
import App from './App.vue'
import PickerApp from './PickerApp.vue'
import { Till } from './store/till.js'
import { Picker } from './picker/picker.js'

let mounted = null

let activeTill = null

let shellElement = null

function mountTillWithBoot(element, boot) {
    unmountMounted()

    const till = new Till(boot.data ?? boot, {
        syncEndpoint: element.dataset.syncEndpoint,
        accessToken: element.dataset.accessToken ?? 'anonymous',
    })

    activeTill = till

    till.start().then(() => {
        if (activeTill !== till) {
            till.stop()

            return
        }

        const app = createApp(App)

        app.provide('till', till)

        app.mount(element)

        mounted = app

        window.pointOfSaleTill = till
    })
}

function mountPicker(element) {
    unmountMounted()

    const picker = new Picker(
        {
            registers: element.dataset.registersEndpoint,
            open: element.dataset.openEndpoint,
            discard: element.dataset.discardEndpoint,
            boot: element.dataset.bootEndpoint,
        },
        JSON.parse(element.dataset.strings ?? '{}'),
        JSON.parse(element.dataset.registers ?? '[]'),
        Number(element.dataset.configId) || null,
    )

    const app = createApp(PickerApp)

    app.provide('picker', picker)

    app.mount(element)

    mounted = app

    window.pointOfSalePicker = picker

    picker.load()
}

function unmountMounted() {
    activeTill?.stop()

    activeTill = null

    mounted?.unmount()

    mounted = null

    document.getElementById('pos-status-slot')?.replaceChildren()
}

function leaveTill() {
    unmountMounted()

    document.getElementById('pos-till')?.removeAttribute('data-mounted')
}

function boot() {
    const element = document.getElementById('pos-till')

    if (!element || element.dataset.mounted === 'true') {
        return
    }

    element.dataset.mounted = 'true'

    shellElement = element

    if (element.dataset.boot) {
        mountTillWithBoot(element, JSON.parse(element.dataset.boot))

        return
    }

    mountPicker(element)
}

window.pointOfSaleShell = {
    openTerminal(bootData) {
        if (shellElement) {
            mountTillWithBoot(shellElement, bootData)
        }
    },
    showPicker() {
        if (shellElement) {
            mountPicker(shellElement)
        }
    },
}

document.addEventListener('DOMContentLoaded', boot)
document.addEventListener('livewire:navigating', leaveTill)
document.addEventListener('livewire:navigated', boot)

if (document.readyState !== 'loading') {
    boot()
}
