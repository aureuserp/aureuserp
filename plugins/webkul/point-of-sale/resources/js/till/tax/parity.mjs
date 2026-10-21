import { computeAll } from './computer.js'
import { floatRound } from './float.js'

function readStdin() {
    return new Promise((resolve, reject) => {
        let buffer = ''

        process.stdin.setEncoding('utf8')
        process.stdin.on('data', (chunk) => {
            buffer += chunk
        })
        process.stdin.on('end', () => resolve(buffer))
        process.stdin.on('error', reject)
    })
}

function hydrate(taxes, byId) {
    return taxes.map((tax) => ({
        ...tax,
        children: (tax.children_tax_ids ?? []).map((id) => byId.get(id)).filter(Boolean),
    }))
}

async function main() {
    const payload = JSON.parse(await readStdin())

    const results = payload.map((scenario) => {
        if (scenario.float_round) {
            const { value, precision_rounding, precision_digits, rounding_method } = scenario.float_round

            return {
                value: floatRound(value, {
                    precisionRounding: precision_rounding ?? null,
                    precisionDigits: precision_digits ?? null,
                    roundingMethod: rounding_method ?? 'HALF-UP',
                }),
            }
        }

        const byId = new Map()

        for (const tax of scenario.taxes) {
            byId.set(tax.id, { ...tax })
        }

        for (const tax of byId.values()) {
            tax.children = (tax.children_tax_ids ?? []).map((id) => byId.get(id)).filter(Boolean)
        }

        const roots = scenario.root_tax_ids
            ? scenario.root_tax_ids.map((id) => byId.get(id)).filter(Boolean)
            : hydrate(scenario.taxes, byId)

        try {
            const outcome = computeAll({
                taxes: roots,
                priceUnit: scenario.price_unit,
                quantity: scenario.quantity ?? 1,
                discount: scenario.discount ?? 0,
                currencyRounding: scenario.currency_rounding ?? 0.01,
                roundingMethod: scenario.rounding_method ?? 'round_per_line',
                handlePriceInclude: scenario.handle_price_include ?? true,
                forcePriceInclude: scenario.force_price_include ?? false,
            })

            return {
                total_excluded: outcome.total_excluded,
                total_included: outcome.total_included,
                total_tax: outcome.total_tax,
                taxes: outcome.taxes.map((tax) => ({
                    id: tax.id,
                    amount: tax.amount,
                    base: tax.base,
                })),
            }
        } catch (error) {
            return { error: error.message }
        }
    })

    process.stdout.write(JSON.stringify(results))
}

main().catch((error) => {
    process.stdout.write(JSON.stringify({ fatal: error.message, stack: error.stack }))
    process.exitCode = 1
})
