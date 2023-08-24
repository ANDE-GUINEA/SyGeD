<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Decret;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BooleanColumn;
use App\Filament\Resources\DecretResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DecretResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class DecretResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Decret::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationLabel = 'DECRETS';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'SYGED';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'retourne',
            'valider',
            'soumettre',
            'delete_any',
            'force_delete',
            'force_delete_any',
        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextInput::make('objet')
                                    ->required()
                                    ->label('OBJET DU DECRET'),
                                RichEditor::make('content')
                                    ->label('CONTENUT'),
                                FileUpload::make('documents')
                                    ->required()
                                    ->label('DOCUMENT DU DECRET')
                                    ->enableOpen()
                                    // ->maxSize(1024)
                                    ->directory('decrets_files')
                                    ->preserveFilenames()
                                    ->enableDownload(),
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
                    ->label('Utilisateur'),
                TextColumn::make('init')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('NOM DU DEPARTMENT'),
                TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('CODE'),
                TextColumn::make('objet')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('OBJET DU DECRET'),
                BooleanColumn::make('okSGG')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: TRUE)
                    ->label('VALIDATION SGG'),
                IconColumn::make('okPRIMATURE')
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: TRUE)
                    ->label('VALIDATION PRIMATURE'),
                IconColumn::make('okPRG')
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: TRUE)
                    ->label('VALIDATION PRG'),
                BadgeColumn::make('status')
                    ->colors([
                        'secondary' => static fn ($state): bool => $state === 'En attente',
                        'primary' => static fn ($state): bool => $state === 'En cours',
                        'success' => static fn ($state): bool => $state === 'Approuvé',
                        'success' => static fn ($state): bool => $state === 'Publié',
                    ])
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable()
                    ->label('STATUT'),
                TextColumn::make('content')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: TRUE)
                    ->label('CONTENU DU DECRET'),
                TextColumn::make('created_at')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Date de création')
                    ->dateTime('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                DateRangeFilter::make('created_at')
                    ->label('Filtrer par la date de création'),
            ])
            ->actions([
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
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->size('lg')
                    ->label('REDIGER UN PROJET DE DECRET'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDecrets::route('/'),
            'create' => Pages\CreateDecret::route('/create'),
            'view' => Pages\ViewDecret::route('/{record}'),
            'edit' => Pages\EditDecret::route('/{record}/edit'),
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
