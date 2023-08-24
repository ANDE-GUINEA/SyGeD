<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Inbox;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Departement;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DepartementResource\Pages;
use App\Filament\Resources\DepartementResource\RelationManagers;

class DepartementResource extends Resource
{
    protected static ?string $model = Departement::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $navigationLabel = 'DEPARTEMENTS';
    // protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'SETTINGS';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->label('Nom département'),
                            ]),
                    ])
                    ->columns(1),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    // ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Date de création')
                    ->label('Utilisateur'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('Nom departement'),
                TextColumn::make('created_at')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Date de création')
                    ->dateTime('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->mutateFormDataUsing(function (array $data): array {

                            // dd($data);

                            return $data;
                        })
                        ->beforeFormFilled(function () {
                            // Runs before the form fields are populated from the database.
                        })
                        ->afterFormFilled(function () {
                            // Runs after the form fields are populated from the database.
                        })
                        ->beforeFormValidated(function () {
                            // Runs before the form fields are validated when the form is saved.
                        })
                        ->afterFormValidated(function () {
                            // Runs after the form fields are validated when the form is saved.
                        })
                        ->before(function () {
                            // Runs before the form fields are saved to the database.
                        })
                        ->after(function () {
                            // Runs after the form fields are saved to the database.

                        }),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),

                ]),

            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                // FilamentExportBulkAction::make('EXPORTER')
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDepartements::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}