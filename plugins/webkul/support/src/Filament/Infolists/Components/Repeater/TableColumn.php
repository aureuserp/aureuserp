<?php

namespace Webkul\Support\Filament\Infolists\Components\Repeater;

use Closure;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Concerns\HasName;
use Filament\Support\Components\Component;
use Filament\Support\Concerns\CanWrapHeader;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasWidth;
use Filament\Tables\Columns\Concerns\CanBeToggled;
use Illuminate\Contracts\Support\Htmlable;
use LogicException;
use Webkul\Support\Concerns\CanBeHidden;

class TableColumn extends Component
{
    use CanBeHidden;
    use CanBeToggled;
    use CanWrapHeader;
    use HasAlignment;
    use HasLabel;
    use HasName;
    use HasWidth;

    protected string $evaluationIdentifier = 'column';

    protected bool|Closure $isHeaderLabelHidden = false;

    public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string|Closure $name): static
    {
        $columnClass = static::class;

        $name ??= static::getDefaultName();

        if (blank($name)) {
            throw new LogicException("Column of class [$columnClass] must have a unique name, passed to the [make()] method.");
        }

        $static = app($columnClass, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public function hiddenHeaderLabel(bool|Closure $condition = true): static
    {
        $this->isHeaderLabelHidden = $condition;

        return $this;
    }

    public function getLabel(): string|Htmlable|null
    {
        return $this->evaluate($this->label);
    }

    public function isHeaderLabelHidden(): bool
    {
        return (bool) $this->evaluate($this->isHeaderLabelHidden);
    }
}
