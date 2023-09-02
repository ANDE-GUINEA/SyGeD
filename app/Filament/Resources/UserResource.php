<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MultiSelect;
use App\Filament\Resources\UserResource\Pages;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $title = 'LISTE UTILISATEURS';
    protected static ?int $navigationSort = 9;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationLabel(): string
    {
        return trans('filament-user::user.resource.label');
    }

    public static function getPluralLabel(): string
    {
        return trans('filament-user::user.resource.label');
    }

    public static function getLabel(): string
    {
        return trans('filament-user::user.resource.single');
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-user.group');
    }

    public function getTitle(): string
    {
        return trans('filament-user::user.resource.title.resource');
    }

    public static function form(Form $form): Form
    {
        $rows = [
            Card::make()
                ->schema([
                    Grid::make(1)
                        ->schema([
                            TextInput::make('name')->required()->label('NOM COMPLET'),
                        ]),

                    Grid::make()
                        ->schema([
                            TextInput::make('email')
                                ->suffix('@gouvernement.gov.gn')
                                // ->email()
                                ->required()->label('E-MAIL'),

                            TextInput::make('password')->label('MOT DE PASSE')
                                ->password()
                                ->default(12345678)
                                ->maxLength(255)
                                ->required()
                                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                                ->dehydrated(fn (?string $state): bool => filled($state))
                                ->required(fn (string $operation): bool => $operation === 'create')
                            // ->dehydrateStateUsing(static function ($state)
                            // use ($form) {
                            //     if (!empty($state)) {
                            //         return Hash::make($state);
                            //     }

                            //     $user = User::find($form->getColumns());
                            //     if ($user) {
                            //         return $user->password;
                            //     }
                            // }),
                        ]),
                    Grid::make(3)
                        ->schema([
                            Select::make('departement_id')
                                ->relationship('departement', 'name')
                                ->searchable()
                                ->preload()
                                ->label('DEPARTEMENT'),
                            Select::make('worker_id')
                                ->relationship('worker', 'name')
                                ->searchable()
                                ->preload()
                                ->label('WORKER'),
                            TextInput::make('fonction')->label('FONCTION'),
                        ]),
                ])
                ->columns(),
        ];

        if (config('filament-user.shield')) {
            $rows[] = MultiSelect::make('roles')->relationship('roles', 'name')
                ->searchable()
                ->preload()
                ->label(trans('filament-user::user.resource.roles'));
        }

        $form->schema($rows);

        return $form;
    }

    public static function table(Table $table): Table
    {
        $table
            ->columns([
                TextColumn::make('id')->sortable()->label(trans('filament-user::user.resource.id')),
                TextColumn::make('departement.name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: TRUE)
                    ->label('DEPARTEMENT'),
                TextColumn::make('worker.name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: TRUE)
                    ->label('WORKER'),
                TextColumn::make('fonction')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: TRUE)
                    ->label('FONCTION'),
                TextColumn::make('name')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable()
                    ->searchable()
                    ->label(trans('filament-user::user.resource.name')),
                TextColumn::make('email')->sortable()->searchable()->label(trans('filament-user::user.resource.email')),
                ToggleColumn::make('IsAdmin')->sortable()->searchable()->label('IsAdmin ?'),
                ToggleColumn::make('IsWorker')->sortable()->searchable()->label('IsWork ?'),
                TextColumn::make('created_at')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Date de création')
                    ->dateTime('d/m/Y'),

            ])
            ->filters([
                Tables\Filters\Filter::make('verified')
                    ->label(trans('filament-user::user.resource.verified'))
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at')),
                // Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make('unverified')
                    ->label(trans('filament-user::user.resource.unverified'))
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at')),
            ])->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
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

        // if (config('filament-user.impersonate')) {
        //     $table->prependActions([
        //         Impersonate::make('impersonate'),
        //     ]);
        // }

        return $table;
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
