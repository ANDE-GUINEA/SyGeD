<?php

namespace App\Filament\Resources\ArreteResource\Pages;

use App\Filament\Resources\ArreteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArretes extends ListRecords
{
    protected static string $resource = ArreteResource::class;
    protected static ?string $title = 'Liste des Arretes';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->size('lg')
                ->label('PREPARER UN PROJET D\'ARRETE'),
        ];
    }
}
