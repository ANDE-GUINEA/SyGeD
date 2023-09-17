<?php

namespace App\Filament\Resources\TypeArreteResource\Pages;

use App\Filament\Resources\TypeArreteResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTypeArretes extends ManageRecords
{
    protected static string $resource = TypeArreteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
