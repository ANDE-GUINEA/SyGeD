<?php

namespace App\Filament\Resources\ArreteResource\Pages;

use App\Filament\Resources\ArreteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArrete extends EditRecord
{
    protected static string $resource = ArreteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
