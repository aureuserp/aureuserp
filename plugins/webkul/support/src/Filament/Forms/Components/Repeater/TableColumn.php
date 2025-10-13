<?php

namespace Webkul\Support\Filament\Forms\Components\Repeater;

use Closure;
use LogicException;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Concerns\HasName;
use Webkul\Support\Concerns\CanBeHidden;
use Filament\Tables\Columns\Concerns\CanBeToggled;
use Filament\Support\Components\Component;
use Filament\Support\Concerns\CanWrapHeader;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasWidth;

class TableColumn extends Component
{
    use CanBeHidden;
    use CanBeToggled;
    use HasName;
    use CanWrapHeader;
    use HasAlignment;
    use HasWidth;
    use HasLabel;

    protected string $evaluationIdentifier = 'column';

    protected bool | Closure $isHeaderLabelHidden = false;

    protected bool | Closure $isMarkedAsRequired = false;

    final public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string | Closure $name): static
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

    public function hiddenHeaderLabel(bool | Closure $condition = true): static
    {
        $this->isHeaderLabelHidden = $condition;

        return $this;
    }

    public function isHeaderLabelHidden(): bool
    {
        return (bool) $this->evaluate($this->isHeaderLabelHidden);
    }

    public function markAsRequired(bool | Closure $condition = true): static
    {
        $this->isMarkedAsRequired = $condition;

        return $this;
    }

    public function isMarkedAsRequired(): bool
    {
        return (bool) $this->evaluate($this->isMarkedAsRequired);
    }
}
