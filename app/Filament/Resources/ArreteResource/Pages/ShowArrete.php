<?php

namespace App\Filament\Resources\ArreteResource\Pages;

use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\ArreteResource;

class ShowArrete extends ViewRecord
{
    protected static string $resource = ArreteResource::class;

    protected static string $view = 'filament.resources.arrete-resource.pages.show-arrete';
}
