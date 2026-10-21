window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault()

    window.pointOfSaleInstallPrompt = event

    window.dispatchEvent(new CustomEvent('point-of-sale:installable'))
})

window.addEventListener('appinstalled', () => {
    window.pointOfSaleInstallPrompt = null

    window.dispatchEvent(new CustomEvent('point-of-sale:installed'))
})

const installed = () => window.matchMedia('(display-mode: standalone)').matches
    || window.navigator.standalone === true

window.pointOfSaleInstall = async (messages = {}) => {
    const prompt = window.pointOfSaleInstallPrompt

    if (!prompt) {
        const body = installed() ? messages.installed : messages.unavailable

        if (window.FilamentNotification) {
            new window.FilamentNotification().title(messages.title).body(body).info().send()
        }

        return
    }

    prompt.prompt()

    await prompt.userChoice

    window.pointOfSaleInstallPrompt = null

    window.dispatchEvent(new CustomEvent('point-of-sale:installed'))
}

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/pos/service-worker.js', { scope: '/pos/' }).catch(() => {})
    })
}

const stylesheetsReady = (frameDocument) => {
    const links = [...frameDocument.querySelectorAll('link[rel="stylesheet"]')]

    if (! links.length) {
        return Promise.resolve()
    }

    return Promise.race([
        Promise.all(links.map((link) => link.sheet
            ? Promise.resolve()
            : new Promise((resolve) => {
                link.addEventListener('load', resolve, { once: true })
                link.addEventListener('error', resolve, { once: true })
            }))),
        new Promise((resolve) => window.setTimeout(resolve, 3000)),
    ])
}

const absoluteUrls = (cssText, base) => cssText.replace(
    /url\((['"]?)([^'")]+)\1\)/g,
    (match, quote, path) => (path.startsWith('data:') ? match : `url("${new URL(path, base).href}")`),
)

const copyStylesheets = (frameDocument) => {
    for (const sheet of document.styleSheets) {
        if (sheet.disabled) {
            continue
        }

        let rules = null

        try {
            rules = sheet.cssRules
        } catch {
            rules = null
        }

        if (! rules) {
            if (sheet.ownerNode) {
                frameDocument.head.appendChild(sheet.ownerNode.cloneNode(true))
            }

            continue
        }

        const base = sheet.href ?? document.baseURI

        const cssText = [...rules].map((rule) => absoluteUrls(rule.cssText, base)).join('\n')

        const media = sheet.media?.mediaText

        const style = frameDocument.createElement('style')

        style.textContent = media ? `@media ${media}{${cssText}}` : cssText

        frameDocument.head.appendChild(style)
    }
}

window.pointOfSaleReceipt = {
    async print(target) {
        const source = typeof target === 'string' ? document.querySelector(target) : target

        if (! source) {
            return
        }

        const receipt = source.cloneNode(true)

        receipt.classList.remove('hidden')

        const frame = document.createElement('iframe')

        frame.setAttribute('aria-hidden', 'true')
        frame.setAttribute('title', 'receipt')
        frame.style.cssText = 'position:fixed;inset-block-start:0;inset-inline-start:-10000px;width:80mm;height:297mm;border:0'

        document.body.appendChild(frame)

        const frameDocument = frame.contentDocument

        const dir = document.documentElement.dir || 'ltr'
        const lang = document.documentElement.lang || 'en'

        frameDocument.open()
        frameDocument.write(`<!doctype html><html dir="${dir}" lang="${lang}"><head><meta charset="utf-8"></head><body></body></html>`)
        frameDocument.close()

        copyStylesheets(frameDocument)

        const overrides = frameDocument.createElement('style')

        overrides.textContent = 'html,body{margin:0;padding:0;background:#fff;color:#000;color-scheme:light}.pos-receipt{display:block;margin:0;color:#000;background:#fff}.pos-receipt *{color:inherit;background:transparent;border-color:currentColor}'

        frameDocument.head.appendChild(overrides)
        frameDocument.body.appendChild(receipt)

        await stylesheetsReady(frameDocument)

        await frameDocument.fonts?.ready

        await new Promise((resolve) => frame.contentWindow.requestAnimationFrame(resolve))

        const page = frameDocument.createElement('style')

        const height = Math.ceil(frameDocument.body.scrollHeight * 25.4 / 96) + 8

        page.textContent = `@page{size:80mm ${height}mm;margin:0}`

        frameDocument.head.appendChild(page)

        frame.contentWindow.focus()
        frame.contentWindow.print()

        window.setTimeout(() => frame.remove(), 1000)
    },
}

let navigatedWithinPanel = false

document.addEventListener('livewire:navigating', () => {
    navigatedWithinPanel = true
})

document.addEventListener('livewire:navigated', () => {
    if (! navigatedWithinPanel || ! document.getElementById('pos-status-slot')) {
        return
    }

    window.Livewire?.dispatch('refresh-topbar')
})
