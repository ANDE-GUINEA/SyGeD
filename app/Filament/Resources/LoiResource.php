<?php

namespace App\Filament\Resources;

use App\Models\Loi;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\LoiResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\LoiResource\RelationManagers;

class LoiResource extends Resource
{
    protected static ?string $model = Loi::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationLabel = 'LOIS ET REGLEMENTS';
    protected static ?string $title = 'LOIS/REGLEMENTS';
    // protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'SYGED';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Grid::make(1)
                    ->schema([
                        TextInput::make('number_loi')
                            ->label('NUMERO DE LA LOI'),
                        TextInput::make('title')
                            ->required()
                            ->label('TITRE DE LOI/REGLEMENT'),
                        RichEditor::make('text')
                            ->label('TEXTE DE LOI/REGLEMENT '),
                        FileUpload::make('document')
                            ->required()
                            ->label('DOCUMENT DE LOI/REGLEMENT')
                            ->enableOpen()
                            // ->maxSize(1024)
                            ->directory('loi_files')
                            ->preserveFilenames()
                            ->enableDownload(),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('user.name')
                //     // ->searchable()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true)
                //     ->label('Date de création')
                //     ->label('Utilisateur'),
                TextColumn::make('number_loi')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('LOI NUMERO'),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('TITRE DE LOI/REGLEMENT'),
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
                    Tables\Actions\ViewAction::make()
                        ->stickyModalHeader()
                        ->stickyModalFooter()
                        ->closeModalByClickingAway(true)
                        ->slideOver(),
                    Tables\Actions\EditAction::make()
                        ->stickyModalHeader()
                        ->stickyModalFooter()
                        ->closeModalByClickingAway(true)
                        ->slideOver(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])
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
                    ->label('ENREGISTRER LOI/REGLEMENT')
                    ->stickyModalHeader()
                    ->modalHeading('ENREGISTREMENT D\'UNE LOI OU D\'UN REGLEMENT')
                    ->stickyModalFooter()
                    ->closeModalByClickingAway(false)
                    ->slideOver(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLois::route('/'),
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