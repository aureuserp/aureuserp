<?php

namespace Webkul\Account\Services;

use Webkul\Account\Exceptions\InvalidTaxFormulaException;

/**
 * Evaluates the custom formula of a tax using AmountType::CODE.
 *
 * Only arithmetic is supported: numbers, the variables listed in self::VARIABLES,
 * the functions listed in self::FUNCTIONS, the operators + - * / and parentheses.
 * Anything else is rejected while parsing, so a formula is never handed to the
 * PHP evaluator.
 */
class TaxFormulaEvaluator
{
    /**
     * The variables a formula is allowed to reference.
     */
    public const VARIABLES = [
        'price_unit',
        'quantity',
        'price_subtotal',
    ];

    /**
     * The functions a formula is allowed to call, each taking one or more
     * arguments. Capping a tax is the reason they exist.
     */
    public const FUNCTIONS = [
        'min',
        'max',
    ];

    /**
     * A number, an identifier, an operator, a parenthesis or an argument
     * separator, anchored at the current offset so that anything else is
     * reported as an invalid character.
     */
    protected const TOKEN_PATTERN = '/\G\s*(\d+(?:\.\d+)?|[A-Za-z_][A-Za-z0-9_]*|[-+*\/(),])/';

    /**
     * @param  array<string, float|int|null>  $variables
     *
     * @throws InvalidTaxFormulaException
     */
    public function evaluate(?string $formula, array $variables = []): float
    {
        $tokens = $this->tokenize($formula);

        $position = 0;

        $amount = $this->parseExpression($tokens, $position, $variables);

        if ($position < count($tokens)) {
            throw new InvalidTaxFormulaException(__('accounts::system.tax-formula.unexpected-token', [
                'token' => $tokens[$position],
            ]));
        }

        return $amount;
    }

    /**
     * Parses the formula against dummy values so that it can be rejected while
     * saving the tax rather than while invoicing.
     *
     * @throws InvalidTaxFormulaException
     */
    public function validate(?string $formula): void
    {
        $this->evaluate($formula, array_fill_keys(self::VARIABLES, 1.0));
    }

    /**
     * @return list<string>
     *
     * @throws InvalidTaxFormulaException
     */
    protected function tokenize(?string $formula): array
    {
        $formula = trim((string) $formula);

        if ($formula === '') {
            throw new InvalidTaxFormulaException(__('accounts::system.tax-formula.empty'));
        }

        $tokens = [];

        $offset = 0;

        $length = strlen($formula);

        while ($offset < $length) {
            if (! preg_match(self::TOKEN_PATTERN, $formula, $matches, PREG_OFFSET_CAPTURE, $offset)) {
                $remaining = ltrim(substr($formula, $offset));

                if ($remaining === '') {
                    break;
                }

                throw new InvalidTaxFormulaException(__('accounts::system.tax-formula.invalid-character', [
                    'character' => mb_substr($remaining, 0, 1),
                ]));
            }

            $tokens[] = $matches[1][0];

            $offset = $matches[1][1] + strlen($matches[1][0]);
        }

        return $tokens;
    }

    /**
     * @param  list<string>  $tokens
     * @param  array<string, float|int|null>  $variables
     */
    protected function parseExpression(array $tokens, int &$position, array $variables): float
    {
        $value = $this->parseTerm($tokens, $position, $variables);

        while (in_array($tokens[$position] ?? null, ['+', '-'], true)) {
            $operator = $tokens[$position++];

            $operand = $this->parseTerm($tokens, $position, $variables);

            $value = $operator === '+' ? $value + $operand : $value - $operand;
        }

        return $value;
    }

    /**
     * @param  list<string>  $tokens
     * @param  array<string, float|int|null>  $variables
     */
    protected function parseTerm(array $tokens, int &$position, array $variables): float
    {
        $value = $this->parseFactor($tokens, $position, $variables);

        while (in_array($tokens[$position] ?? null, ['*', '/'], true)) {
            $operator = $tokens[$position++];

            $operand = $this->parseFactor($tokens, $position, $variables);

            if ($operator === '*') {
                $value *= $operand;

                continue;
            }

            // A tax that cannot divide must not break invoicing.
            $value = $operand == 0.0 ? 0.0 : $value / $operand;
        }

        return $value;
    }

    /**
     * @param  list<string>  $tokens
     * @param  array<string, float|int|null>  $variables
     *
     * @throws InvalidTaxFormulaException
     */
    protected function parseFactor(array $tokens, int &$position, array $variables): float
    {
        $token = $tokens[$position] ?? null;

        if ($token === null) {
            throw new InvalidTaxFormulaException(__('accounts::system.tax-formula.unexpected-end'));
        }

        if ($token === '+' || $token === '-') {
            $position++;

            $value = $this->parseFactor($tokens, $position, $variables);

            return $token === '-' ? -$value : $value;
        }

        if ($token === '(') {
            $position++;

            $value = $this->parseExpression($tokens, $position, $variables);

            if (($tokens[$position] ?? null) !== ')') {
                throw new InvalidTaxFormulaException(__('accounts::system.tax-formula.unclosed-parenthesis'));
            }

            $position++;

            return $value;
        }

        if (is_numeric($token)) {
            $position++;

            return (float) $token;
        }

        // An identifier followed by an opening parenthesis is a call, so an
        // unknown name is reported as a function rather than as a variable.
        if (($tokens[$position + 1] ?? null) === '(' && preg_match('/^[A-Za-z_]/', $token)) {
            return $this->parseFunctionCall($tokens, $position, $variables);
        }

        if (in_array($token, self::VARIABLES, true)) {
            $position++;

            return (float) ($variables[$token] ?? 0.0);
        }

        if (in_array($token, [')', ',', '*', '/'], true)) {
            throw new InvalidTaxFormulaException(__('accounts::system.tax-formula.unexpected-token', [
                'token' => $token,
            ]));
        }

        throw new InvalidTaxFormulaException(__('accounts::system.tax-formula.unknown-variable', [
            'variable'  => $token,
            'variables' => implode(', ', self::VARIABLES),
        ]));
    }

    /**
     * @param  list<string>  $tokens
     * @param  array<string, float|int|null>  $variables
     *
     * @throws InvalidTaxFormulaException
     */
    protected function parseFunctionCall(array $tokens, int &$position, array $variables): float
    {
        $function = $tokens[$position];

        if (! in_array($function, self::FUNCTIONS, true)) {
            throw new InvalidTaxFormulaException(__('accounts::system.tax-formula.unknown-function', [
                'function'  => $function,
                'functions' => implode(', ', self::FUNCTIONS),
            ]));
        }

        // Skip the name and the opening parenthesis.
        $position += 2;

        $arguments = [$this->parseExpression($tokens, $position, $variables)];

        while (($tokens[$position] ?? null) === ',') {
            $position++;

            $arguments[] = $this->parseExpression($tokens, $position, $variables);
        }

        if (($tokens[$position] ?? null) !== ')') {
            throw new InvalidTaxFormulaException(__('accounts::system.tax-formula.unclosed-parenthesis'));
        }

        $position++;

        return $function === 'min' ? min($arguments) : max($arguments);
    }
}
