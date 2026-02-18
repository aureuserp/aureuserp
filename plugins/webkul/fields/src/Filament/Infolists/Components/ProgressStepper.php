<?php

namespace Webkul\Field\Filament\Infolists\Components;

use BackedEnum;
use Closure;
use Filament\Infolists\Components\Entry;

class ProgressStepper extends Entry
{
    protected string $view = 'fields::filament.infolists.components.progress-stepper';

    protected mixed $options = [];

    protected mixed $isInline = false;

    public function options(array|Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->evaluate($this->options) ?? [];
    }

    public function inline(bool|Closure $inline = true): static
    {
        $this->isInline = $inline;

        return $this;
    }

    public function isInline(): bool
    {
        return $this->evaluate($this->isInline);
    }

    public function getColor(string $value): string
    {
        $state = $this->getState();

        if ($state instanceof BackedEnum) {
            $state = $state->value;
        }

        return ((string) $state === (string) $value) ? 'primary' : 'gray';
    }
}
