window.filamentChartJsGlobalPlugins ??= []

const resolveFontFamily = (chart) => chart.options?.font?.family || 'sans-serif'

const themeColor = (name, fallback) => {
    const value = getComputedStyle(document.documentElement).getPropertyValue(name).trim()

    return value || fallback
}

const dashboardChartCenterText = {
    id: 'dashboardChartCenterText',
    afterDraw(chart) {
        const config = chart.config?.options?.plugins?.centerText

        if (! config || ! config.text) {
            return
        }

        const { ctx, chartArea } = chart

        if (! chartArea) {
            return
        }

        const centerX = (chartArea.left + chartArea.right) / 2
        const centerY = (chartArea.top + chartArea.bottom) / 2
        const fontFamily = resolveFontFamily(chart)

        ctx.save()
        ctx.textAlign = 'center'
        ctx.textBaseline = 'middle'

        ctx.fillStyle = getComputedStyle(chart.canvas).color || themeColor('--gray-950', 'oklch(0.21 0.03 264)')
        ctx.font = `700 22px ${fontFamily}`
        ctx.fillText(config.text, centerX, centerY - 9)

        if (config.label) {
            ctx.fillStyle = config.labelColor || themeColor('--gray-500', 'oklch(0.55 0.02 264)')
            ctx.font = `500 12px ${fontFamily}`
            ctx.fillText(config.label, centerX, centerY + 14)
        }

        ctx.restore()
    },
}

const dashboardChartTooltipPercentage = {
    id: 'dashboardChartTooltipPercentage',
    beforeInit(chart) {
        const tooltip = chart.config?.options?.plugins?.tooltip

        if (! tooltip || ! tooltip.percentage) {
            return
        }

        tooltip.callbacks = tooltip.callbacks || {}

        tooltip.callbacks.label = (context) => {
            const values = context.dataset?.data || []
            const total = values.reduce((carry, value) => carry + (Number(value) || 0), 0)
            const current = Number(context.parsed) || 0
            const percentage = total > 0 ? (current / total) * 100 : 0

            return `${percentage.toFixed(2)}%`
        }
    },
}

const dashboardChartHoverGrow = {
    id: 'dashboardChartHoverGrow',
    afterUpdate(chart) {
        if (! chart.config?.options?.plugins?.hoverGrow) {
            return
        }

        chart.data.datasets.forEach((dataset, datasetIndex) => {
            const meta = chart.getDatasetMeta(datasetIndex)

            if (! meta || ! meta.data) {
                return
            }

            meta.data.forEach((arc) => {
                if (arc && typeof arc.outerRadius === 'number') {
                    arc.$baseOuterRadius = arc.outerRadius
                }
            })
        })
    },
    beforeDraw(chart) {
        const config = chart.config?.options?.plugins?.hoverGrow

        if (! config) {
            return
        }

        const grow = (typeof config === 'object' && config.grow) ? config.grow : 12
        const speed = (typeof config === 'object' && config.speed) ? config.speed : 0.16
        const active = chart.getActiveElements ? chart.getActiveElements() : []
        const activeKeys = new Set(active.map((item) => `${item.datasetIndex}:${item.index}`))

        let animating = false

        chart.data.datasets.forEach((dataset, datasetIndex) => {
            const meta = chart.getDatasetMeta(datasetIndex)

            if (! meta || ! meta.data) {
                return
            }

            meta.data.forEach((arc, index) => {
                if (! arc || typeof arc.outerRadius !== 'number') {
                    return
                }

                if (arc.$baseOuterRadius == null) {
                    arc.$baseOuterRadius = arc.outerRadius
                }

                const target = activeKeys.has(`${datasetIndex}:${index}`) ? grow : 0
                const current = arc.$growCurrent ?? 0
                let next = current + (target - current) * speed

                if (Math.abs(target - next) < 0.15) {
                    next = target
                } else {
                    animating = true
                }

                arc.$growCurrent = next
                arc.outerRadius = arc.$baseOuterRadius + next
            })
        })

        if (animating && ! chart.$growRaf) {
            chart.$growRaf = requestAnimationFrame(() => {
                chart.$growRaf = null

                if (chart.canvas && chart.ctx) {
                    chart.draw()
                }
            })
        }
    },
}

const dashboardChartEntrance = {
    id: 'dashboardChartEntrance',
    beforeInit(chart) {
        const config = chart.config?.options?.plugins?.entrance

        if (! config) {
            return
        }

        const type = typeof config === 'string' ? config : config.type
        const easing = (typeof config === 'object' && config.easing) ? config.easing : 'easeOutQuart'
        const duration = (typeof config === 'object' && config.duration) ? config.duration : 800
        const stagger = (typeof config === 'object' && config.stagger) ? config.stagger : 60

        if (type === 'progressive-line') {
            chart.options.animation = { duration, easing }

            chart.options.animations = Object.assign({}, chart.options.animations, {
                x: {
                    type: 'number',
                    easing,
                    duration,
                    from: NaN,
                    delay(ctx) {
                        if (ctx.type !== 'data' || ctx.xStarted) {
                            return 0
                        }

                        ctx.xStarted = true

                        return ctx.index * stagger
                    },
                },
                y: {
                    type: 'number',
                    easing,
                    duration,
                    from: (ctx) => ctx.chart.scales.y?.getPixelForValue(0),
                    delay(ctx) {
                        if (ctx.type !== 'data' || ctx.yStarted) {
                            return 0
                        }

                        ctx.yStarted = true

                        return ctx.index * stagger
                    },
                },
            })

            return
        }

        if (type === 'rising-bars') {
            chart.options.animation = {
                duration,
                easing,
                delay(ctx) {
                    if (ctx.type === 'data' && ctx.mode === 'default') {
                        return ctx.dataIndex * stagger
                    }

                    return 0
                },
            }

            return
        }

        if (type === 'sweep') {
            chart.options.animation = {
                duration,
                easing,
                animateRotate: true,
                animateScale: false,
            }
        }
    },
}

const dashboardChartLineGradient = {
    id: 'dashboardChartLineGradient',
    afterLayout(chart) {
        if (! chart.config?.options?.plugins?.lineGradient) {
            return
        }

        chart.data.datasets.forEach((dataset) => {
            dataset.$borderKey = null
            dataset.$fillKey = null
        })
    },
    beforeDatasetsDraw(chart) {
        const config = chart.config?.options?.plugins?.lineGradient

        if (! config) {
            return
        }

        const { ctx, chartArea } = chart

        if (! chartArea) {
            return
        }

        const clamp = (value) => Math.min(Math.max(value, 0), 1)
        const width = Math.round(chartArea.right - chartArea.left)
        const height = Math.round(chartArea.bottom - chartArea.top)

        if (width <= 0 || height <= 0) {
            return
        }

        const vertical = config.borderDirection === 'vertical'

        chart.data.datasets.forEach((dataset) => {
            if (Array.isArray(config.border) && config.border.length) {
                const borderKey = vertical ? `v:${height}:${chartArea.top}` : `h:${width}:${chartArea.left}`

                if (! dataset.$borderGradient || dataset.$borderKey !== borderKey) {
                    const gradient = vertical
                        ? ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom)
                        : ctx.createLinearGradient(chartArea.left, 0, chartArea.right, 0)

                    config.border.forEach((stop) => gradient.addColorStop(clamp(stop.stop), stop.color))

                    dataset.$borderGradient = gradient
                    dataset.$borderKey = borderKey
                }

                dataset.borderColor = dataset.$borderGradient
            }

            if (Array.isArray(config.fill) && config.fill.length) {
                if (! dataset.$fillGradient || dataset.$fillKey !== `${height}:${chartArea.top}`) {
                    const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom)

                    config.fill.forEach((stop) => gradient.addColorStop(clamp(stop.stop), stop.color))

                    dataset.$fillGradient = gradient
                    dataset.$fillKey = `${height}:${chartArea.top}`
                }

                dataset.backgroundColor = dataset.$fillGradient
            }
        })
    },
}

window.filamentChartJsGlobalPlugins.push(
    dashboardChartCenterText,
    dashboardChartTooltipPercentage,
    dashboardChartHoverGrow,
    dashboardChartEntrance,
    dashboardChartLineGradient,
)
