<?php

namespace App\Filament\Resources\LoiResource\Pages;

use App\Filament\Resources\LoiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLois extends ManageRecords
{
    protected static string $resource = LoiResource::class;
    protected static ?string $title = 'LOIS/REGLEMENTS';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('ENREGISTRER LOI/REGLEMENT')
                ->modalHeading('ENREGISTREMENT D\'UNE LOI OU D\'UN REGLEMENT')
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->closeModalByClickingAway(false)
                ->slideOver(),
        ];
    }
}
