@php
    use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;

    $member = $getRecord();
    $user = $member->user;
    $assigned = $member->leadsAssignedLastMonth();
    $max = max(0, (int) $member->assignment_max);
    $ratio = $max > 0 ? min(1, $assigned / $max) : 0;
    $arc = 125.66;
    $ruleBased = TeamResource::usesRuleBasedAssignment();
@endphp

<div class="crm-contact-card crm-member-card">
    <span class="crm-contact-avatar crm-contact-avatar-lg">
        @if ($user?->avatar_url)
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" loading="lazy" />
        @else
            <x-filament::icon icon="heroicon-o-user" class="crm-contact-avatar-icon" />
        @endif
    </span>

    <div class="crm-contact-card-body crm-member-card-body">
        <div class="min-w-0 flex-1">
            <p class="crm-contact-card-name">{{ $user?->name }}</p>

            @if ($user?->email)
                <p class="crm-contact-card-text flex items-center gap-1.5">
                    <x-filament::icon icon="heroicon-s-envelope" class="h-3.5 w-3.5 shrink-0 text-gray-400" />
                    <span class="truncate">{{ $user->email }}</span>
                </p>
            @endif

            <div class="mt-1.5 flex flex-wrap gap-1">
                @if (! $member->is_active)
                    <x-filament::badge size="sm" color="gray">{{ __('crm::filament/clusters/configurations/resources/team.pages.members.inactive') }}</x-filament::badge>
                @endif

                @if ($member->assignment_optout && $ruleBased)
                    <x-filament::badge size="sm" color="warning">{{ __('crm::filament/clusters/configurations/resources/team.pages.members.skipped') }}</x-filament::badge>
                @endif

                @if (filled($member->assignment_domain) && $ruleBased)
                    <x-filament::badge size="sm" color="info">{{ trans_choice('crm::filament/clusters/configurations/resources/team.pages.members.rules', count($member->assignment_domain['conditions'] ?? []), ['count' => count($member->assignment_domain['conditions'] ?? [])]) }}</x-filament::badge>
                @endif
            </div>
        </div>

        @if ($ruleBased)
            <div class="crm-member-card-gauge" title="{{ __('crm::filament/clusters/configurations/resources/team.pages.members.gauge', ['assigned' => $assigned, 'max' => $max]) }}">
                <span class="crm-member-card-gauge-label">{{ __('crm::filament/clusters/configurations/resources/team.pages.members.leads-30-days') }}</span>

                <svg viewBox="0 0 100 56" aria-hidden="true">
                    <path d="M10 50 A40 40 0 0 1 90 50" class="crm-member-card-gauge-track" />
                    <path d="M10 50 A40 40 0 0 1 90 50" class="crm-member-card-gauge-value" style="stroke-dasharray: {{ round($ratio * $arc, 2) }} {{ $arc }}" />
                </svg>

                <span class="crm-member-card-gauge-count">{{ $assigned }}<span>/{{ $max }}</span></span>
            </div>
        @endif
    </div>
</div>
