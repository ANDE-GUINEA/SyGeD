<?php

namespace App\Filament\Resources\TypeResource\Pages;

use App\Filament\Resources\TypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTypes extends ManageRecords
{
    protected static string $resource = TypeResource::class;
    protected static ?string $title = 'LISTE DES TYPES DE DECRET';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                // ->createAnother(false)
                ->label('ENREGISTRER UN TYPE DE DECRET')
                ->stickyModalHeader()
                ->modalHeading('ENREGISTREMENT D\'UN TYPE DE DECRET')
                ->stickyModalFooter()
                ->closeModalByClickingAway(false)
                ->slideOver(),
        ];
    }
}