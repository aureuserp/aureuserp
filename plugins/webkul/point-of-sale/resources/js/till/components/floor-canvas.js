import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'

const MIN_ZOOM = 0.2

const MAX_ZOOM = 3

const MAX_FIT = 1.5

export function useFloorCanvas(container, floor, tables, { margin = 40 } = {}) {
    const viewport = ref({ width: 0, height: 0 })

    const image = ref({ width: 0, height: 0 })

    const zoom = ref(null)

    const held = ref(null)

    let observer = null

    onMounted(() => {
        observer = new ResizeObserver(([entry]) => {
            viewport.value = {
                width: entry.contentRect.width,
                height: entry.contentRect.height,
            }
        })

        if (container.value) {
            observer.observe(container.value)
        }
    })

    watch(container, (element, previous) => {
        if (!observer) {
            return
        }

        if (previous) {
            observer.unobserve(previous)
        }

        if (element) {
            observer.observe(element)
        }
    }, { flush: 'post' })

    onBeforeUnmount(() => observer?.disconnect())

    watch(() => floor.value?.background_image, (url) => {
        image.value = { width: 0, height: 0 }

        if (!url) {
            return
        }

        const loader = new Image()

        loader.onload = () => {
            if (floor.value?.background_image === url) {
                image.value = { width: loader.naturalWidth, height: loader.naturalHeight }
            }
        }

        loader.src = url
    }, { immediate: true })

    watch(() => floor.value?.id, () => {
        zoom.value = null
    })

    const plan = computed(() => {
        const list = tables.value ?? []

        return {
            width: Math.max(image.value.width, 400, ...list.map((table) => table.position_h + table.width + margin)),
            height: Math.max(image.value.height, 300, ...list.map((table) => table.position_v + table.height + margin)),
        }
    })

    const fitScale = computed(() => {
        const { width, height } = viewport.value

        if (!width || !height) {
            return 1
        }

        return Math.min(width / plan.value.width, height / plan.value.height, MAX_FIT)
    })

    const scale = computed(() => held.value ?? zoom.value ?? fitScale.value)

    const frameStyle = computed(() => ({
        width: `${plan.value.width * scale.value}px`,
        height: `${plan.value.height * scale.value}px`,
    }))

    const planStyle = computed(() => {
        const style = {
            width: `${plan.value.width}px`,
            height: `${plan.value.height}px`,
            transform: `scale(${scale.value})`,
        }

        if (floor.value?.background_image && image.value.width) {
            style.backgroundImage = `url("${floor.value.background_image}")`
            style.backgroundSize = `${image.value.width}px ${image.value.height}px`
            style.backgroundRepeat = 'no-repeat'
            style.backgroundPosition = 'left top'
        }

        return style
    })

    function zoomIn() {
        zoom.value = Math.min(scale.value * 1.25, MAX_ZOOM)
    }

    function zoomOut() {
        zoom.value = Math.max(scale.value / 1.25, MIN_ZOOM)
    }

    function fit() {
        zoom.value = null
    }

    function hold() {
        held.value = scale.value
    }

    function release() {
        held.value = null
    }

    return { scale, frameStyle, planStyle, zoomIn, zoomOut, fit, hold, release }
}
