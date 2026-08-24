<?php

use Webkul\Account\Exceptions\InvalidTaxFormulaException;
use Webkul\Account\Services\TaxFormulaEvaluator;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

function evaluateFormula(?string $formula, array $variables = []): float
{
    return (new TaxFormulaEvaluator)->evaluate($formula, $variables);
}

it('evaluates arithmetic against the tax variables', function (string $formula, float $expected) {
    expect(evaluateFormula($formula, [
        'price_unit'     => 100.0,
        'quantity'       => 3.0,
        'price_subtotal' => 300.0,
    ]))->toBe($expected);
})->with([
    ['price_unit * quantity * 0.18', 54.0],
    ['price_subtotal * 0.05', 15.0],
    ['(price_unit + 20) * quantity / 10', 36.0],
    ['-price_unit', -100.0],
    ['price_unit - -50', 150.0],
    ['  2 + 3 * 4  ', 14.0],
    ['(2 + 3) * 4', 20.0],
]);

it('caps and floors the amount with min and max', function (string $formula, float $expected) {
    expect(evaluateFormula($formula, [
        'price_unit'     => 100.0,
        'quantity'       => 3.0,
        'price_subtotal' => 300.0,
    ]))->toBe($expected);
})->with([
    ['min(price_subtotal * 0.18, 20)', 20.0],
    ['min(price_subtotal * 0.18, 500)', 54.0],
    ['max(price_subtotal * 0.18, 100)', 100.0],
    ['max(price_subtotal * 0.01, 1)', 3.0],
    ['min(10, 20, 5, 30)', 5.0],
    ['max(1, 2) * max(3, 4)', 8.0],
    ['min(price_unit, quantity) + 1', 4.0],
    ['min((2 + 3) * 2, 100)', 10.0],
    ['-min(5, 10)', -5.0],
]);

it('rejects calls that are not min or max', function (string $formula) {
    expect(fn () => evaluateFormula($formula, ['price_unit' => 1.0]))
        ->toThrow(InvalidTaxFormulaException::class);
})->with([
    'abs'                => ['abs(price_unit)'],
    'round'              => ['round(price_unit, 2)'],
    'exec'               => ['exec("ls")'],
    'variable as call'   => ['price_unit(2)'],
    'no arguments'       => ['min()'],
    'trailing comma'     => ['min(1, )'],
    'unclosed call'      => ['min(1, 2'],
    'comma outside call' => ['1, 2'],
]);

it('treats missing variables as zero', function () {
    expect(evaluateFormula('price_unit * quantity'))->toBe(0.0);
});

it('returns zero instead of dividing by zero', function () {
    expect(evaluateFormula('price_unit / quantity', ['price_unit' => 100.0, 'quantity' => 0.0]))->toBe(0.0);
});

it('rejects anything that is not plain arithmetic', function (?string $formula) {
    expect(fn () => evaluateFormula($formula, ['price_unit' => 1.0]))
        ->toThrow(InvalidTaxFormulaException::class);
})->with([
    'empty'            => [''],
    'null'             => [null],
    'function call'    => ['abs(price_unit)'],
    'php call'         => ['exec("ls")'],
    'unknown variable' => ['price_unit * margin'],
    'assignment'       => ['price_unit = 5'],
    'string literal'   => ['"price_unit"'],
    'variable variable'=> ['$price_unit'],
    'unclosed paren'   => ['(price_unit * 2'],
    'trailing operator'=> ['price_unit *'],
    'double operator'  => ['price_unit * * 2'],
    'stray paren'      => ['price_unit) * 2'],
    'empty parens'     => ['price_unit * ()'],
    'semicolon'        => ['price_unit; exec("ls")'],
]);

it('validates a formula without needing real values', function () {
    expect(fn () => (new TaxFormulaEvaluator)->validate('price_subtotal * 0.21'))->not->toThrow(InvalidTaxFormulaException::class);

    expect(fn () => (new TaxFormulaEvaluator)->validate('price_subtotal * rate'))->toThrow(InvalidTaxFormulaException::class);
});
