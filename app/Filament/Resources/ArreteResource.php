<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Arrete;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\TypeArrete;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BooleanColumn;
use App\Filament\Resources\ArreteResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\ArreteResource\RelationManagers;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class ArreteResource extends Resource
{
    protected static ?string $model = Arrete::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'Arretés';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 2;
    // protected static ?string $navigationGroup = 'SYGED';
    protected static ?string $pollingInterval = '10s';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {

        return $form->schema([

            Card::make()
                ->schema([
                    Select::make('type_arrete_id')
                        ->relationship('typearrete', 'title')
                        ->searchable()
                        ->preload()
                        ->required()
                        // ->disabled()
                        ->default(TypeArrete::where('title', 'ARRETE DE NOMINATION')->first()->id)
                        ->helperText('Merci de selectionner le type de decret.')
                        ->label('TYPES DE ARRETE'),
                    TextInput::make('objet')
                        ->required()
                        ->helperText('Merci de renseigner le libelle du decret.')
                        ->label('LIBELLE DU ARRETE'),
                    Grid::make(1)->schema([
                        Textarea::make('content')
                            ->label('L\'OBJET DE L\'ARRETE')
                            ->required()
                            ->helperText('')
                            ->minLength(10)
                            // ->maxLength(1024)
                            // ->limit(1024)
                            ->autosize(),
                    ]),
                    FileUpload::make('motif')
                        ->required()
                        ->label('EXPOSE DE MOTIF / RAPPORT ')
                        ->enableOpen()
                        ->maxSize(1024)
                        ->directory('exposes de motif')
                        ->preserveFilenames()
                        ->enableDownload(),
                    FileUpload::make('references')
                        // ->required()
                        ->helperText(
                            'DOCUMENTS DE REFERENCE RELATIF A L\'EXPOSE DE MOTIF (Vous avez la possibilité de mettre plusieurs documents).',
                        )
                        ->label('DOCUMENTS DE REFERENCE ')
                        ->enableOpen()
                        ->multiple()
                        // ->maxSize(1024)
                        ->directory('document de reference')
                        ->preserveFilenames()
                        ->enableDownload(),
                    FileUpload::make('visa')
                        ->required()
                        ->label('VISA DU ARRETE')
                        ->enableOpen()
                        // ->multiple()
                        ->maxSize(1024)
                        ->directory('visas')
                        ->preserveFilenames()
                        ->enableDownload(),
                    FileUpload::make('corps')
                        ->required()
                        ->label('CORPS DU ARRETE')
                        ->enableOpen()
                        // ->multiple()
                        // ->maxSize(1024)
                        ->directory('corps')
                        ->preserveFilenames()
                        ->enableDownload(),
                    // FileUpload::make('confidential')
                    //     ->required()
                    //     ->multiple()
                    //     ->label('DOCUMENTS CONFIDENTIELS')
                    //     ->helperText('Liste des personnes proposées, CV, Diplômes etc... (accessibles uniquement par la Présidence et la Primature)')
                    //     ->enableOpen()
                    //     ->maxSize(1024)
                    //     ->directory('confidentiels')
                    //     ->preserveFilenames()
                    //     ->enableDownload(),
                    Section::make('AUTRES DOCUMENTS')
                        ->description('')
                        ->collapsible()
                        ->compact()
                        ->schema([
                            FileUpload::make('autres')
                                // ->required()
                                ->multiple()
                                ->label('DOCUMENTS')
                                ->enableOpen()
                                ->maxSize(1024)
                                ->directory('autres')
                                ->preserveFilenames()
                                ->helperText('Vous avez la possibilité de mettre plusieurs documents.')
                                ->enableDownload(),
                        ])
                        ->columns(1),
                ])
                ->columns(2),


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
                TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->tooltip(fn (Model $record): string => " {$record->content}")
                    ->html()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('CODE'),
                TextColumn::make('created_at')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('DATE DE CREATION')
                    ->dateTime('d/m/Y'),
                TextColumn::make('submit_at')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('DATE DE SOUMISSION')
                    ->dateTime('d/m/Y'),

                TextColumn::make('objet')
                    ->searchable()
                    ->sortable()
                    ->tooltip(fn (Model $record): string => " {$record->content}")
                    ->html()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('LIBELLE'),
                TextColumn::make('typearrete.title')
                    ->searchable()
                    ->sortable()
                    ->tooltip(fn (Model $record): string => " {$record->content}")
                    ->html()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('TYPE'),
                TextColumn::make('init')
                    ->searchable()
                    ->sortable()
                    ->tooltip(fn (Model $record): string => " {$record->content}")
                    ->html()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('PORTEUR'),
                TextColumn::make('inbox.name')
                    ->searchable()
                    ->sortable()
                    ->tooltip(fn (Model $record): string => " {$record->content}")
                    ->html()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('POSITION'),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => static fn ($state): bool => $state === 'En Elaboration',
                        'primary' => static fn ($state): bool => $state === 'Examen SGG',
                        'primary' => static fn ($state): bool => $state === 'Examen Primature',
                        // 'primary' => static fn ($state): bool => $state === 'Examen Presidence',
                        'danger' => static fn ($state): bool => $state === 'Retour SGG',
                        'danger' => static fn ($state): bool => $state === 'Retour Primature',
                        'danger' => static fn ($state): bool => $state === 'En Attente Signature',
                        'success' => static fn ($state): bool => $state === 'Signé',
                    ])
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable()
                    ->label('STATUT'),
                BooleanColumn::make('okSGG')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('VALIDER SGG ?'),
                IconColumn::make('okPRIMATURE')
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('VALIDER PRIMATURE ?'),
                IconColumn::make('Signé')
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('SIGNE ?'),
                IconColumn::make('Publié')
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('Publié ?'),
                TextColumn::make('content')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('OBJET DU L\'ARRETE'),
            ])
            ->poll('3600s')
            ->striped()
            ->deferLoading()
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                DateRangeFilter::make('created_at')
                    ->label('Filtrer par la date de création')
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make(), Tables\Actions\ForceDeleteAction::make(), Tables\Actions\RestoreAction::make()
                ])
            ])
            ->bulkActions([
                ExportBulkAction::make()
                    ->label('Exporter en Excel'),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                // FilamentExportBulkAction::make('EXPORTER')
            ])
            // ->contentGrid([
            //     'md' => 2,
            //     'xl' => 3,
            // ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->size('lg')
                    ->label('PREPARER UN PROJET D\'ARRETE'),
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
            'index' => Pages\ListArretes::route('/'),
            'create' => Pages\CreateArrete::route('/create'),
            'view' => Pages\ViewArrete::route('/{record}'),
            'edit' => Pages\EditArrete::route('/{record}/edit'),
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
