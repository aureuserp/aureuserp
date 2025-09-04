<?php

namespace Webkul\Project\Enums;

use Filament\Support\Contracts;

enum TaskState: string implements Contracts\HasColor, Contracts\HasIcon, Contracts\HasLabel
{
    case IN_PROGRESS = 'in_progress';
    case CHANGE_REQUESTED = 'change_requested';
    case APPROVED = 'approved';
    case CANCELLED = 'cancelled';
    case DONE = 'done';

    public function getLabel(): string
    {
        return self::options()[$this->value];
    }

    public function getIcon(): ?string
    {
        return self::icons()[$this->value] ?? null;
    }

    public function getColor(): ?string
    {
        return self::colors()[$this->value] ?? null;
    }

    public static function options(): array
    {
        return [
            self::IN_PROGRESS->value      => __('projects::enums/task-state.in-progress'),
            self::CHANGE_REQUESTED->value => __('projects::enums/task-state.change-requested'),
            self::APPROVED->value         => __('projects::enums/task-state.approved'),
            self::CANCELLED->value        => __('projects::enums/task-state.cancelled'),
            self::DONE->value             => __('projects::enums/task-state.done'),
        ];
    }

    public static function icons(): array
    {
        return [
            self::IN_PROGRESS->value      => 'heroicon-m-play-circle',
            self::CHANGE_REQUESTED->value => 'heroicon-s-exclamation-circle',
            self::APPROVED->value         => 'heroicon-o-check-circle',
            self::CANCELLED->value        => 'heroicon-s-x-circle',
            self::DONE->value             => 'heroicon-c-check-circle',
        ];
    }

    public static function colors(): array
    {
        return [
            self::IN_PROGRESS->value      => 'gray',
            self::CHANGE_REQUESTED->value => 'warning',
            self::APPROVED->value         => 'success',
            self::CANCELLED->value        => 'danger',
            self::DONE->value             => 'success',
        ];
    }
}
