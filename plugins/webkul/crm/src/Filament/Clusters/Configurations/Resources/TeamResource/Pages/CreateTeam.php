<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages;

use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Crm\Filament\Concerns\ConfirmsTeamMoves;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Crm\Filament\Concerns\ManagesCRMTeam;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource\Pages\CreateTeam as BaseCreateTeam;

class CreateTeam extends BaseCreateTeam
{
    use ConfirmsTeamMoves, HasCRMClusterNavigation, ManagesCRMTeam;

    protected static string $resource = TeamResource::class;

    protected bool $isCreatingAnother = false;

    public function create(bool $another = false): void
    {
        $this->isCreatingAnother = $another;

        parent::create($another);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($this->confirmsTeamMove(array_column($this->data['crmMembers'] ?? [], 'user_id'), arguments: ['another' => $this->isCreatingAnother])) {
            $this->halt();
        }

        return parent::mutateFormDataBeforeCreate($data);
    }

    protected function afterTeamMoveConfirmed(array $arguments): void
    {
        $this->create((bool) ($arguments['another'] ?? false));
    }
}
