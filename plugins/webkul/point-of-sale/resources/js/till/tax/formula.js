export const FORMULA_VARIABLES = ['price_unit', 'quantity', 'price_subtotal']

export const FORMULA_FUNCTIONS = ['min', 'max']

const TOKEN_PATTERN = /\s*(\d+(?:\.\d+)?|[A-Za-z_][A-Za-z0-9_]*|[-+*/(),])/y

export class InvalidTaxFormulaError extends Error {}

function tokenize(formula) {
    const source = String(formula ?? '').trim()

    if (source === '') {
        throw new InvalidTaxFormulaError('empty formula')
    }

    const tokens = []

    let offset = 0

    while (offset < source.length) {
        TOKEN_PATTERN.lastIndex = offset

        const matches = TOKEN_PATTERN.exec(source)

        if (!matches) {
            const remaining = source.slice(offset).replace(/^\s+/, '')

            if (remaining === '') {
                break
            }

            throw new InvalidTaxFormulaError(`invalid character ${remaining[0]}`)
        }

        tokens.push(matches[1])

        offset = TOKEN_PATTERN.lastIndex
    }

    return tokens
}

function parseExpression(tokens, state, variables) {
    let value = parseTerm(tokens, state, variables)

    while (tokens[state.position] === '+' || tokens[state.position] === '-') {
        const operator = tokens[state.position++]

        const operand = parseTerm(tokens, state, variables)

        value = operator === '+' ? value + operand : value - operand
    }

    return value
}

function parseTerm(tokens, state, variables) {
    let value = parseFactor(tokens, state, variables)

    while (tokens[state.position] === '*' || tokens[state.position] === '/') {
        const operator = tokens[state.position++]

        const operand = parseFactor(tokens, state, variables)

        if (operator === '*') {
            value *= operand

            continue
        }

        value = operand === 0.0 ? 0.0 : value / operand
    }

    return value
}

function parseFactor(tokens, state, variables) {
    const token = state.position < tokens.length ? tokens[state.position] : null

    if (token === null) {
        throw new InvalidTaxFormulaError('unexpected end of formula')
    }

    if (token === '+' || token === '-') {
        state.position++

        const value = parseFactor(tokens, state, variables)

        return token === '-' ? -value : value
    }

    if (token === '(') {
        state.position++

        const value = parseExpression(tokens, state, variables)

        if (tokens[state.position] !== ')') {
            throw new InvalidTaxFormulaError('unclosed parenthesis')
        }

        state.position++

        return value
    }

    if (/^\d+(\.\d+)?$/.test(token)) {
        state.position++

        return parseFloat(token)
    }

    if (tokens[state.position + 1] === '(' && /^[A-Za-z_]/.test(token)) {
        return parseFunctionCall(tokens, state, variables)
    }

    if (FORMULA_VARIABLES.includes(token)) {
        state.position++

        return Number(variables[token] ?? 0.0)
    }

    if ([')', ',', '*', '/'].includes(token)) {
        throw new InvalidTaxFormulaError(`unexpected token ${token}`)
    }

    throw new InvalidTaxFormulaError(`unknown variable ${token}`)
}

function parseFunctionCall(tokens, state, variables) {
    const name = tokens[state.position]

    if (!FORMULA_FUNCTIONS.includes(name)) {
        throw new InvalidTaxFormulaError(`unknown function ${name}`)
    }

    state.position += 2

    const args = [parseExpression(tokens, state, variables)]

    while (tokens[state.position] === ',') {
        state.position++

        args.push(parseExpression(tokens, state, variables))
    }

    if (tokens[state.position] !== ')') {
        throw new InvalidTaxFormulaError('unclosed parenthesis')
    }

    state.position++

    return name === 'min' ? Math.min(...args) : Math.max(...args)
}

export function evaluateFormula(formula, variables = {}) {
    const tokens = tokenize(formula)

    const state = { position: 0 }

    const amount = parseExpression(tokens, state, variables)

    if (state.position < tokens.length) {
        throw new InvalidTaxFormulaError(`unexpected token ${tokens[state.position]}`)
    }

    return amount
}
