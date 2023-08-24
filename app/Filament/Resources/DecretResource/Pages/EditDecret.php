<?php

namespace App\Filament\Resources\DecretResource\Pages;

use App\Filament\Resources\DecretResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDecret extends EditRecord
{
    protected static string $resource = DecretResource::class;

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
