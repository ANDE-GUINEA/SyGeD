<?php

namespace App\Filament\Resources\DecretResource\Pages;

use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\DecretResource;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class ShowDecret extends ViewRecord implements HasShieldPermissions
{
    protected static string $resource = DecretResource::class;

    protected static string $view = 'filament.resources.decret-resource.pages.show-decret';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'retourner',
            'valider',
            'soumettre'
        ];
    }
}