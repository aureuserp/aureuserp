<?php

namespace Webkul\Support\Filament\Summarizers;

use Closure;
use Filament\Schemas\Components\Component;
use Filament\Support\Concerns\HasAlignment;
use Illuminate\Support\Collection;

abstract class Summarizer extends Component
{
    use HasAlignment;

    protected string $evaluationIdentifier = 'summarizer';

    protected ?Closure $using = null;

    protected ?string $label = null;

    protected bool|Closure $isNumeric = false;

    protected string|Closure|null $id = null;

    protected Collection $items;

    final public function __construct()
    {
        $this->setUp();
    }

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        //
    }

    public function using(?Closure $callback): static
    {
        $this->using = $callback;

        return $this;
    }

    public function label(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function numeric(bool|Closure $condition = true): static
    {
        $this->isNumeric = $condition;

        return $this;
    }

    public function isNumeric(): bool
    {
        return (bool) $this->evaluate($this->isNumeric);
    }

    public function getLabel(): ?string
    {
        return $this->label ?? $this->getDefaultLabel();
    }

    abstract public function getDefaultLabel(): ?string;

    public function summarize(Collection $items, string $columnName): int|float|string|null
    {
        $this->items = $items;

        if ($this->using) {
            $value = $this->evaluate($this->using, [
                'items'      => $items,
                'columnName' => $columnName,
            ]);

            return is_scalar($value) ? $value : null;
        }

        return $this->calculateSummary($items, $columnName);
    }

    abstract protected function calculateSummary(Collection $items, string $columnName): int|float|string|null;

    public function id(string|Closure|null $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id ?? spl_object_hash($this);
    }
}
