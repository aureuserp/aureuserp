export function floatCheckPrecision(precisionDigits = null, precisionRounding = null) {
    if (precisionRounding !== null && precisionDigits === null) {
        if (precisionRounding <= 0) {
            throw new Error(`precision_rounding must be positive, got ${precisionRounding}`)
        }

        return precisionRounding
    }

    if (precisionDigits !== null && precisionRounding === null) {
        if (precisionDigits !== Math.floor(precisionDigits)) {
            throw new Error(`precision_digits must be a non-negative integer, got ${precisionDigits}`)
        }

        if (precisionDigits < 0) {
            throw new Error(`precision_digits must be a non-negative integer, got ${precisionDigits}`)
        }

        return Math.pow(10, -precisionDigits)
    }

    throw new Error('exactly one of precision_digits and precision_rounding must be specified')
}

export function floatRound(value, { precisionDigits = null, precisionRounding = null, roundingMethod = 'HALF-UP' } = {}) {
    const roundingFactor = floatCheckPrecision(precisionDigits, precisionRounding)

    if (roundingFactor === 0 || value === 0) {
        return 0.0
    }

    const scaled = value / roundingFactor
    const method = String(roundingMethod).toUpperCase()

    let rounded

    switch (method) {
        case 'HALF-UP':
            rounded = scaled > 0 ? Math.floor(scaled + 0.5) : Math.ceil(scaled - 0.5)
            break

        case 'HALF-DOWN':
            rounded = scaled > 0 ? Math.ceil(scaled - 0.5) : Math.floor(scaled + 0.5)
            break

        case 'HALF-EVEN': {
            const floored = Math.floor(scaled)
            const diff = Math.abs(scaled - floored)

            if (diff === 0.5) {
                rounded = floored % 2 === 0 ? floored : floored + (scaled > 0 ? 1 : -1)
            } else {
                rounded = Math.round(scaled)
            }
            break
        }

        case 'UP':
            rounded = scaled > 0 ? Math.ceil(scaled) : Math.floor(scaled)
            break

        case 'DOWN':
            rounded = scaled > 0 ? Math.floor(scaled) : Math.ceil(scaled)
            break

        default:
            throw new Error(`Unknown rounding method: ${roundingMethod}`)
    }

    return rounded * roundingFactor
}

export function floatIsZero(value, { precisionDigits = null, precisionRounding = null } = {}) {
    const epsilon = floatCheckPrecision(precisionDigits, precisionRounding)

    if (value === 0.0) {
        return true
    }

    return Math.abs(floatRound(value, { precisionRounding: epsilon })) < epsilon
}

export function floatCompare(value1, value2, { precisionDigits = null, precisionRounding = null } = {}) {
    const roundingFactor = floatCheckPrecision(precisionDigits, precisionRounding)

    if (value1 === value2) {
        return 0
    }

    const rounded1 = floatRound(value1, { precisionRounding: roundingFactor })
    const rounded2 = floatRound(value2, { precisionRounding: roundingFactor })

    const delta = rounded1 - rounded2

    if (floatIsZero(delta, { precisionRounding: roundingFactor })) {
        return 0
    }

    return delta < 0.0 ? -1 : 1
}
