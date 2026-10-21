import { floatRound } from './float.js'
import { evaluateFormula, InvalidTaxFormulaError } from './formula.js'

const AMOUNT_TYPE = {
    FIXED: 'fixed',
    GROUP: 'group',
    PERCENT: 'percent',
    DIVISION: 'division',
    CODE: 'code',
}

function sortTaxes(taxes) {
    return [...taxes].sort((a, b) => {
        const sortA = a.sort ?? 0
        const sortB = b.sort ?? 0

        if (sortA !== sortB) {
            return sortA - sortB
        }

        return a.id - b.id
    })
}

function batchAmountTotal(batch) {
    return batch.reduce((carry, tax) => carry + Number(tax.amount ?? 0), 0) / 100.0
}

function evalFormula(tax, rawBase, context) {
    if (!tax.formula) {
        return null
    }

    try {
        return evaluateFormula(tax.formula, {
            price_unit: context.price_unit,
            quantity: context.quantity,
            price_subtotal: rawBase,
        })
    } catch (error) {
        if (error instanceof InvalidTaxFormulaError) {
            return null
        }

        throw error
    }
}

function evalTaxAmountFixedAmount(tax, batch, rawBase, context) {
    if (tax.amount_type === AMOUNT_TYPE.CODE) {
        return evalFormula(tax, rawBase, context)
    }

    if (tax.amount_type === AMOUNT_TYPE.FIXED) {
        const sign = context.price_unit < 0.0 ? -1 : 1

        return sign * context.quantity * Number(tax.amount ?? 0)
    }

    return null
}

function evalTaxAmountPriceIncluded(tax, batch, rawBase) {
    if (tax.amount_type === AMOUNT_TYPE.PERCENT) {
        const totalPercentage = batchAmountTotal(batch)

        const toPriceExcludedFactor = totalPercentage !== -1 ? 1 / (1 + totalPercentage) : 0.0

        return rawBase * toPriceExcludedFactor * Number(tax.amount ?? 0) / 100.0
    }

    if (tax.amount_type === AMOUNT_TYPE.DIVISION) {
        return rawBase * Number(tax.amount ?? 0) / 100.0
    }

    return null
}

function evalTaxAmountPriceExcluded(tax, batch, rawBase) {
    if (tax.amount_type === AMOUNT_TYPE.PERCENT) {
        return rawBase * Number(tax.amount ?? 0) / 100.0
    }

    if (tax.amount_type === AMOUNT_TYPE.DIVISION) {
        const totalPercentage = batchAmountTotal(batch)

        const inclBaseMultiplicator = totalPercentage === 1.0 ? 1.0 : 1 - totalPercentage

        return rawBase * Number(tax.amount ?? 0) / 100.0 / inclBaseMultiplicator
    }

    return null
}

function sharesBatch(tax, reference, priceIncludes, isBaseAffected, specialMode) {
    if (tax.amount_type !== reference.amount_type) {
        return false
    }

    if (!specialMode && priceIncludes.get(tax.id) !== priceIncludes.get(reference.id)) {
        return false
    }

    if (Boolean(tax.include_base_amount) !== Boolean(reference.include_base_amount)) {
        return false
    }

    return !tax.include_base_amount || !isBaseAffected
}

function batchTaxes(sortedTaxes, specialMode) {
    const priceIncludes = new Map(sortedTaxes.map((tax) => [tax.id, Boolean(tax.price_include)]))

    const batchPerTax = new Map()

    let batch = []

    let isBaseAffected = false

    const flush = () => {
        for (const batched of batch) {
            batchPerTax.set(batched.id, batch)
        }

        batch = []
    }

    for (const tax of [...sortedTaxes].reverse()) {
        if (batch.length > 0 && !sharesBatch(tax, batch[0], priceIncludes, isBaseAffected, specialMode)) {
            flush()
        }

        isBaseAffected = Boolean(tax.is_base_affected)

        batch.push(tax)
    }

    flush()

    return batchPerTax
}

export function flattenTaxGroups(taxes, specialMode = false) {
    const sorted = []

    const groupPerTax = new Map()

    for (const tax of sortTaxes(taxes)) {
        if (tax.amount_type !== AMOUNT_TYPE.GROUP) {
            sorted.push(tax)

            continue
        }

        for (const child of sortTaxes(tax.children ?? [])) {
            sorted.push(child)

            groupPerTax.set(child.id, tax)
        }
    }

    return {
        batch_per_tax: batchTaxes(sorted, specialMode),
        group_per_tax: groupPerTax,
        sorted_taxes: sorted,
    }
}

function initialTaxState(tax, context, specialMode) {
    let priceInclude

    if (specialMode === 'total_included') {
        priceInclude = true
    } else if (specialMode === 'total_excluded') {
        priceInclude = false
    } else {
        priceInclude = Boolean(tax.price_include)
    }

    return {
        ...context,
        tax,
        price_include: priceInclude,
        extra_base_for_tax: 0.0,
        extra_base_for_base: 0.0,
    }
}

function isResolved(taxData) {
    return taxData.tax_amount !== undefined && taxData.tax_amount !== null
}

export function propagateBaseAdjustments(taxes, tax, taxesData, specialMode = false) {
    const batchIds = new Set(taxesData.get(tax.id).batch.map((batched) => batched.id))

    const before = function* () {
        for (const candidate of taxes) {
            if (batchIds.has(candidate.id)) {
                break
            }

            yield candidate
        }
    }

    const after = function* () {
        for (const candidate of [...taxes].reverse()) {
            if (batchIds.has(candidate.id)) {
                break
            }

            yield candidate
        }
    }

    const shift = (otherTax, sign) => {
        const current = taxesData.get(tax.id)

        if (!isResolved(current)) {
            return
        }

        const taxAmount = current.tax_amount

        const other = taxesData.get(otherTax.id)

        if (!isResolved(other)) {
            other.extra_base_for_tax += sign * taxAmount
        }

        other.extra_base_for_base += sign * taxAmount
    }

    if (tax.price_include) {
        if (specialMode === false || specialMode === 'total_included') {
            if (!tax.include_base_amount) {
                for (const otherTax of after()) {
                    if (otherTax.price_include) {
                        shift(otherTax, -1)
                    }
                }
            }

            for (const otherTax of before()) {
                shift(otherTax, -1)
            }

            return
        }

        for (const otherTax of after()) {
            if (!otherTax.price_include || tax.include_base_amount) {
                shift(otherTax, 1)
            }
        }

        return
    }

    if (specialMode === false || specialMode === 'total_excluded') {
        if (!tax.include_base_amount) {
            return
        }

        for (const otherTax of after()) {
            if (otherTax.is_base_affected) {
                shift(otherTax, 1)
            }
        }

        return
    }

    if (!tax.include_base_amount) {
        for (const otherTax of after()) {
            shift(otherTax, -1)
        }
    }

    for (const otherTax of before()) {
        shift(otherTax, -1)
    }
}

function resolveTaxAmount({
    resolver,
    tax,
    taxesData,
    reverseChargeData,
    manualTaxAmounts,
    rawBase,
    context,
    sortedTaxes,
    precisionRounding,
    roundingMethod,
    specialMode,
}) {
    const taxData = taxesData.get(tax.id)

    if ('tax_amount' in taxData) {
        return
    }

    let taxAmount

    const manual = manualTaxAmounts ? manualTaxAmounts[String(tax.id)] : null

    if (manual) {
        taxAmount = manual.tax_amount_currency
    } else {
        taxAmount = resolver(tax, taxData.batch, rawBase + taxData.extra_base_for_tax, context)
    }

    if (taxAmount === null || taxAmount === undefined) {
        return
    }

    taxData.tax_amount = roundingMethod === 'round_per_line'
        ? floatRound(taxAmount, { precisionRounding })
        : taxAmount

    if (tax.has_negative_factor) {
        reverseChargeData.get(tax.id).tax_amount = -taxData.tax_amount
    }

    propagateBaseAdjustments(sortedTaxes, tax, taxesData, specialMode)
}

function resolveBaseAmounts(sortedTaxes, taxesData, reverseChargeData, rawBase, manualTaxAmounts, specialMode) {
    for (const tax of [...sortedTaxes].reverse()) {
        const taxData = taxesData.get(tax.id)

        if (!('tax_amount' in taxData)) {
            continue
        }

        const manual = manualTaxAmounts ? manualTaxAmounts[String(tax.id)] : null

        let base

        if (manual && manual.base_amount_currency !== undefined) {
            base = manual.base_amount_currency
        } else {
            let batchTaxAmount = 0

            for (const otherTax of taxData.batch) {
                batchTaxAmount += taxesData.get(otherTax.id).tax_amount ?? 0
            }

            base = rawBase + taxData.extra_base_for_base

            if (taxData.price_include && (specialMode === false || specialMode === 'total_included')) {
                base -= batchTaxAmount
            }
        }

        taxData.base = base

        if (tax.has_negative_factor) {
            reverseChargeData.get(tax.id).base = base
        }
    }
}

function summarize(taxesData, reverseChargeData, batching, rawBase) {
    const computed = []

    for (const taxData of taxesData.values()) {
        if (!('tax_amount' in taxData)) {
            continue
        }

        computed.push(taxData)

        if (taxData.tax.has_negative_factor) {
            computed.push(reverseChargeData.get(taxData.tax.id))
        }
    }

    let totalExcluded
    let totalIncluded

    if (computed.length > 0) {
        totalExcluded = computed[0].base

        totalIncluded = totalExcluded + computed.reduce((carry, entry) => carry + (entry.tax_amount ?? 0), 0)
    } else {
        totalExcluded = rawBase
        totalIncluded = rawBase
    }

    return {
        total_excluded: totalExcluded,
        total_included: totalIncluded,
        taxes_data: computed.map((taxData) => ({
            tax: taxData.tax,
            group: batching.group_per_tax.get(taxData.tax.id) ?? null,
            batch: batching.batch_per_tax.get(taxData.tax.id),
            tax_amount: taxData.tax_amount,
            base_amount: taxData.base,
            is_reverse_charge: taxData.is_reverse_charge ?? false,
        })),
    }
}

export function computeTaxes({
    taxes,
    priceUnit,
    quantity,
    precisionRounding = 0.01,
    roundingMethod = 'round_per_line',
    product = null,
    specialMode = false,
    manualTaxAmounts = null,
}) {
    const batching = flattenTaxGroups(taxes, specialMode)

    const sortedTaxes = batching.sorted_taxes

    const taxesData = new Map()

    const reverseChargeData = new Map()

    for (const tax of sortedTaxes) {
        const state = initialTaxState(tax, {
            group: batching.group_per_tax.get(tax.id) ?? null,
            batch: batching.batch_per_tax.get(tax.id),
        }, specialMode)

        taxesData.set(tax.id, state)

        if (tax.has_negative_factor) {
            reverseChargeData.set(tax.id, { ...state, is_reverse_charge: true })
        }
    }

    let rawBase = quantity * priceUnit

    if (roundingMethod === 'round_per_line') {
        rawBase = floatRound(rawBase, { precisionRounding })
    }

    const context = {
        product,
        price_unit: priceUnit,
        quantity,
        raw_base: rawBase,
        special_mode: specialMode,
        precision_rounding: precisionRounding,
    }

    const run = (resolver, tax) => resolveTaxAmount({
        resolver,
        tax,
        taxesData,
        reverseChargeData,
        manualTaxAmounts,
        rawBase,
        context,
        sortedTaxes,
        precisionRounding,
        roundingMethod,
        specialMode,
    })

    for (const tax of [...sortedTaxes].reverse()) {
        run(evalTaxAmountFixedAmount, tax)
    }

    for (const tax of [...sortedTaxes].reverse()) {
        if (taxesData.get(tax.id).price_include) {
            run(evalTaxAmountPriceIncluded, tax)
        }
    }

    for (const tax of sortedTaxes) {
        if (!taxesData.get(tax.id).price_include) {
            run(evalTaxAmountPriceExcluded, tax)
        }
    }

    resolveBaseAmounts(sortedTaxes, taxesData, reverseChargeData, rawBase, manualTaxAmounts, specialMode)

    return summarize(taxesData, reverseChargeData, batching, rawBase)
}

export function computeAll({
    taxes,
    priceUnit,
    quantity = 1,
    discount = 0,
    currencyRounding = 0.01,
    roundingMethod = 'round_per_line',
    product = null,
    handlePriceInclude = true,
    forcePriceInclude = false,
}) {
    let specialMode = false

    if (forcePriceInclude) {
        specialMode = 'total_included'
    } else if (!handlePriceInclude) {
        specialMode = 'total_excluded'
    }

    const computation = computeTaxes({
        taxes,
        priceUnit: priceUnit * (1 - discount / 100),
        quantity,
        precisionRounding: currencyRounding,
        roundingMethod,
        product,
        specialMode,
    })

    return {
        total_excluded: computation.total_excluded,
        total_included: computation.total_included,
        total_tax: computation.total_included - computation.total_excluded,
        taxes: computation.taxes_data.map((taxData) => ({
            id: taxData.tax.id,
            name: taxData.tax.name,
            sort: taxData.tax.sort,
            amount: taxData.tax_amount,
            base: taxData.base_amount,
            price_include: Boolean(taxData.tax.price_include),
            is_reverse_charge: taxData.is_reverse_charge,
            group_id: taxData.group ? taxData.group.id : null,
        })),
    }
}
