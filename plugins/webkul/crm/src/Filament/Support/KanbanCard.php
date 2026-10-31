<?php

namespace Webkul\Crm\Filament\Support;

use Webkul\Security\Models\User;

class KanbanCard
{
    public ?string $url = null;

    public ?string $tooltip = null;

    public ?string $titleTooltip = null;

    public ?array $icon = null;

    public ?array $badge = null;

    public ?array $avatar = null;

    public array $menu = [];

    public array $lines = [];

    public array $tags = [];

    public array $start = [];

    public array $end = [];

    public function __construct(public string $title) {}

    public static function make(string $title): static
    {
        return new static($title);
    }

    public function url(?string $url, ?string $tooltip = null): static
    {
        $this->url = $url;
        $this->tooltip = $tooltip;

        return $this;
    }

    public function titleTooltip(?string $tooltip): static
    {
        $this->titleTooltip = $tooltip;

        return $this;
    }

    public function icon(string $icon, ?string $color = null, ?string $tooltip = null): static
    {
        $this->icon = ['icon' => $icon, 'color' => $color, 'tooltip' => $tooltip];

        return $this;
    }

    public function badge(?string $label, ?string $color = null): static
    {
        $this->badge = filled($label) ? ['label' => $label, 'color' => $color ?? 'gray'] : null;

        return $this;
    }

    public function avatar(?string $url, string $icon): static
    {
        $this->avatar = ['url' => $url, 'icon' => $icon];

        return $this;
    }

    public function menuItem(string $label, string $icon, ?string $url = null, ?string $click = null, ?string $color = null): static
    {
        $this->menu[] = ['label' => $label, 'icon' => $icon, 'url' => $url, 'click' => $click, 'color' => $color];

        return $this;
    }

    public function line(?string $text, ?string $icon = null, ?string $tooltip = null, bool $strong = false, ?string $suffix = null, bool $small = false): static
    {
        if (filled($text)) {
            $this->lines[] = ['text' => $text, 'icon' => $icon, 'tooltip' => $tooltip, 'strong' => $strong, 'suffix' => $suffix, 'small' => $small];
        }

        return $this;
    }

    public function tags(iterable $tags, ?string $tooltip = null): static
    {
        foreach ($tags as $tag) {
            $this->tags[] = ['name' => $tag->name, 'color' => $tag->color, 'tooltip' => $tooltip];
        }

        return $this;
    }

    public function start(?array ...$items): static
    {
        $this->start = [...$this->start, ...array_filter($items)];

        return $this;
    }

    public function end(?array ...$items): static
    {
        $this->end = [...$this->end, ...array_filter($items)];

        return $this;
    }

    public static function stars(string $recordId, int $value, array $labels, bool $editable, string $label, ?string $current = null): array
    {
        return ['type' => 'stars', 'record' => $recordId, 'value' => $value, 'labels' => $labels, 'editable' => $editable, 'label' => $label, 'tooltip' => $label.': '.$current];
    }

    public static function button(string $icon, string $click, string $label, ?string $tooltip = null, ?string $color = null): array
    {
        return ['type' => 'button', 'icon' => $icon, 'click' => $click, 'label' => $label, 'tooltip' => $tooltip ?? $label, 'color' => $color];
    }

    public static function text(string $text, ?string $icon = null, ?string $tooltip = null, ?string $color = null): array
    {
        return ['type' => 'text', 'text' => $text, 'icon' => $icon, 'tooltip' => $tooltip, 'color' => $color];
    }

    public static function amount(string $amount, ?string $date, string $tooltip, ?string $dateTooltip = null): array
    {
        return ['type' => 'amount', 'amount' => $amount, 'date' => $date, 'tooltip' => $tooltip, 'dateTooltip' => $dateTooltip];
    }

    public static function dot(?string $color, string $tooltip): ?array
    {
        return filled($color) ? ['type' => 'dot', 'color' => $color, 'tooltip' => $tooltip] : null;
    }

    public static function status(?string $label, string $color): ?array
    {
        return filled($label) ? ['type' => 'status', 'label' => $label, 'color' => $color] : null;
    }

    public static function user(?User $user, string $tooltip): ?array
    {
        if (! $user) {
            return null;
        }

        return [
            'type'     => 'user',
            'name'     => $user->name,
            'avatar'   => $user->avatar_url,
            'initials' => collect(explode(' ', $user->name))->filter()->take(2)->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))->implode(''),
            'tooltip'  => $tooltip.': '.$user->name,
        ];
    }
}
