<?php

namespace App\Filament\Resources\DossierResource\Pages;

use App\Filament\Resources\DossierResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDossiers extends ManageRecords
{
    protected static string $resource = DossierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('ENREGISTRER DES DOCUMENTS')
                ->stickyModalHeader()
                ->modalHeading('ENREGISTREMENT D\'UN DOCUMENT')
                ->stickyModalFooter()
                ->closeModalByClickingAway(false)
                ->slideOver(),
        ];
    }
}
