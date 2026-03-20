<?php

namespace Webkul\Support\Filament\Forms\Components\Repeater;

use Closure;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Concerns\HasName;
use Filament\Support\Components\Component;
use Filament\Support\Concerns\CanWrapHeader;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasWidth;
use Filament\Tables\Columns\Concerns\BelongsToGroup;
use Filament\Tables\Columns\Concerns\CanBeToggled;
use LogicException;
use Webkul\Support\Filament\Concerns\CanBeHidden;
use Webkul\Support\Filament\Concerns\CanBeSummarized;

class TableColumn extends Component
{
    use BelongsToGroup;
    use CanBeHidden;
    use CanBeSummarized;
    use CanBeToggled;
    use CanWrapHeader;
    use HasAlignment;
    use HasLabel;
    use HasName;
    use HasWidth;

    protected string $evaluationIdentifier = 'column';

    protected bool|Closure $isHeaderLabelHidden = false;

    protected bool|Closure $isMarkedAsRequired = false;

    protected bool|Closure $isResizable = false;

    protected int|string|Closure|null $minWidth = null;

    protected int|string|Closure|null $maxWidth = null;

    final public function __construct(string $name)
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

    public function isHeaderLabelHidden(): bool
    {
        return (bool) $this->evaluate($this->isHeaderLabelHidden);
    }

    public function markAsRequired(bool|Closure $condition = true): static
    {
        $this->isMarkedAsRequired = $condition;

        return $this;
    }

    public function isMarkedAsRequired(): bool
    {
        return (bool) $this->evaluate($this->isMarkedAsRequired);
    }

    public function resizable(bool|Closure $condition = true, int|string|Closure|null $minWidth = null, int|string|Closure|null $maxWidth = null): static
    {
        $this->isResizable = $condition;

        $this->minWidth = $minWidth;

        $this->maxWidth = $maxWidth;

        return $this;
    }

    public function isResizable(): bool
    {
        return (bool) $this->evaluate($this->isResizable);
    }

    public function getMinWidth(): ?string
    {
        $minWidth = $this->evaluate($this->minWidth);

        if ($minWidth === null) {
            return null;
        }

        if (is_int($minWidth)) {
            return $minWidth.'px';
        }

        return $minWidth !== null ? (string) $minWidth : null;
    }

    public function getMaxWidth(): ?string
    {
        $maxWidth = $this->evaluate($this->maxWidth);

        if ($maxWidth === null) {
            return null;
        }

        if (is_int($maxWidth)) {
            return $maxWidth.'px';
        }

        return $maxWidth !== null ? (string) $maxWidth : null;
    }

    public function wrapHeader(bool|Closure $condition = false): static
    {
        $this->canHeaderWrap = $condition;

        return $this;
    }
}
