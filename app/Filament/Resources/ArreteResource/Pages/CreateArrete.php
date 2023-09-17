<?php

namespace App\Filament\Resources\ArreteResource\Pages;

use App\Filament\Resources\ArreteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateArrete extends CreateRecord
{
    protected static string $resource = ArreteResource::class;
    protected static ?string $title = 'Préparation d\'un projet d\'Arrete';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // dd($data);
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        // generate a pin based on 2 * 7 digits + a random character
        $pin = mt_rand(1000000, 9999999) . mt_rand(1000000, 9999999) . $characters[rand(0, strlen($characters) - 1)];

        // shuffle the result
        $string = str_shuffle($pin);

        $code = substr($string, 0, 5);

        $data['code'] = $code;
        $data['inbox_id'] = auth()->user()->departement->inbox->id;

        return $data;
    }
}
