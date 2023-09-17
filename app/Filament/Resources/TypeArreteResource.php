<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\TypeArrete;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TypeArreteResource\Pages;
use App\Filament\Resources\TypeArreteResource\RelationManagers;

class TypeArreteResource extends Resource
{
    protected static ?string $model = TypeArrete::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $navigationLabel = 'TYPE D\' ARRETE';
    protected static ?string $pollingInterval = '10s';
    // protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Settings';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Grid::make(1)
                    ->schema([
                        TextInput::make('title')
                            ->label('LE NOM DE TYPE DE DECRET')
                            ->required(),
                        RichEditor::make('description')
                            ->label('DESCRIPTION'),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->tooltip(fn (Model $record): string => " {$record->description}")->html()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('NOM DU TYPE DE DECRET'),
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
                Tables\Actions\EditAction::make()
                    // ->label('ENREGISTRER UN TYPE DE DECRET')
                    ->stickyModalHeader()
                    ->modalHeading('MODIFICATION')
                    ->stickyModalFooter()
                    ->closeModalByClickingAway(false)
                    ->slideOver(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('ENREGISTRER UN TYPE DE ARRETE')
                    ->stickyModalHeader()
                    ->modalHeading('ENREGISTREMENT D\'UN TYPE D\'ARRETE')
                    ->stickyModalFooter()
                    ->closeModalByClickingAway(false)
                    ->slideOver(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTypeArretes::route('/'),
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
