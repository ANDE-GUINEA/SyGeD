<?php

namespace App\Filament\Resources\DepartementResource\Pages;

use App\Models\User;
use App\Models\Inbox;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\DepartementResource;
use App\Models\Departement;

class ManageDepartements extends ManageRecords
{
    protected static string $resource = DepartementResource::class;

    protected static ?string $title = 'LISTE DEPARTMENTS';
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('AJOUTER UN DEPARTMENT')
                ->mutateFormDataUsing(function (array $data): array {
                    return $data;
                })
                ->afterFormFilled(function () {
                    // Runs after the form fields are populated with their default values.
                })
                ->beforeFormValidated(function () {
                    // Runs before the form fields are validated when the form is submitted.
                })
                ->afterFormValidated(function () {
                    // Runs after the form fields are validated when the form is submitted.
                })
                ->before(function () {
                    // Runs before the form fields are saved to the database.
                })
                ->after(function () {
                    $departement = Departement::latest()->first();

                    // Runs after the form fields are saved to the database.
                    Inbox::create([
                        'departement_id' => $departement->id,
                        'name' => $departement->name,
                    ]);
                })->stickyModalHeader()
                ->stickyModalFooter()
                ->closeModalByClickingAway(false)
                ->slideOver(),
        ];
    }
}