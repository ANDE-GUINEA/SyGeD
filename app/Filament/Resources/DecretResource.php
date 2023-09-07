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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BooleanColumn;
use App\Filament\Resources\DecretResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\DecretResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class DecretResource extends Resource
{
    protected static ?string $model = Decret::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'MES DECRETS';
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
            Section::make('INFORMATIONS')
                ->description('')
                ->collapsible()
                ->compact()
                ->schema([
                    Select::make('type_id')
                        ->relationship('type', 'title')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Merci de selectionner le type de decret.')
                        ->label('TYPES DE DECRET'),
                    TextInput::make('objet')
                        ->required()
                        ->helperText('Merci de renseigner le libelle du decret.')
                        ->extraAttributes(['class' => 'uppercase'])
                        ->dehydrateStateUsing(fn (string $state): string => ucwords($state))
                        ->live()
                        // ->hint('Documentation? What documentation?!')
                        ->label('LIBELLE DU DECRET'),
                    Grid::make(1)->schema([
                        Textarea::make('content')
                            ->label('DESCRIPTION')
                            ->helperText('Merci de donner une brève description en 500 caractères maximum')
                            ->minLength(10)
                            ->maxLength(1024)
                            // ->limit(50)
                            // ->tooltip(function (TextColumn $column): ?string {
                            //     $state = $column->getState();

                            //     if (strlen($state) <= $column->getCharacterLimit()) {
                            //         return null;
                            //     }

                            //     // Only render the tooltip if the column content exceeds the length limit.
                            //     return $state;
                            // })
                            ->autosize(),
                    ]), FileUpload::make('visa')
                        ->required()
                        ->label('VISA DU DECRET')
                        // ->label('CONTENU PUBLIC DU DECRET(Exposé de motif, corps du decret, décision du conseil des ministres, etc.)')
                        ->enableOpen()
                        // ->multiple()
                        ->maxSize(1024)
                        ->directory('decrets_files')
                        ->preserveFilenames()
                        ->enableDownload(),
                    FileUpload::make('corps')
                        ->required()
                        ->label('CORPS DU DECRET')
                        // ->label('CONTENU PUBLIC DU DECRET(Exposé de motif, corps du decret, décision du conseil des ministres, etc.)')
                        ->enableOpen()
                        // ->multiple()
                        ->maxSize(1024)
                        ->directory('decrets_files')
                        ->preserveFilenames()
                        ->enableDownload(),
                    // FileUpload::make('documentPrivate')
                    //     ->required()
                    //     ->multiple()
                    //     ->label('CONTENU CONFIDENTIEL DU DECRET (Liste des personnes proposées, CV, Diplômes, ) ')
                    //     ->enableOpen()
                    //     ->maxSize(1024)
                    //     ->directory('decrets_files')
                    //     ->preserveFilenames()
                    //     ->enableDownload(),
                ])
                ->columns(2),

            // Section::make('Heading')
            //     ->description('')
            //     ->collapsible()
            //     ->compact()
            //     ->schema([
            //         FileUpload::make('visa')
            //             ->required()
            //             ->label('VISA')
            //             // ->label('CONTENU PUBLIC DU DECRET(Exposé de motif, corps du decret, décision du conseil des ministres, etc.)')
            //             ->enableOpen()
            //             // ->multiple()
            //             ->maxSize(1024)
            //             ->directory('decrets_files')
            //             ->preserveFilenames()
            //             ->enableDownload(),
            //         FileUpload::make('corps')
            //             ->required()
            //             ->label('CORPS')
            //             // ->label('CONTENU PUBLIC DU DECRET(Exposé de motif, corps du decret, décision du conseil des ministres, etc.)')
            //             ->enableOpen()
            //             // ->multiple()
            //             ->maxSize(1024)
            //             ->directory('decrets_files')
            //             ->preserveFilenames()
            //             ->enableDownload(),
            //         // FileUpload::make('documentPrivate')
            //         //     ->required()
            //         //     ->multiple()
            //         //     ->label('CONTENU CONFIDENTIEL DU DECRET (Liste des personnes proposées, CV, Diplômes, ) ')
            //         //     ->enableOpen()
            //         //     ->maxSize(1024)
            //         //     ->directory('decrets_files')
            //         //     ->preserveFilenames()
            //         //     ->enableDownload(),
            //     ])
            //     ->columns(2),
            Section::make('AUTRES DOCUMENTS')
                ->description('')
                ->collapsible()
                ->compact()
                ->schema([
                    FileUpload::make('documentPublic')
                        // ->required()
                        ->multiple()
                        ->label('PUBLICS')
                        ->enableOpen()
                        ->maxSize(1024)
                        ->directory('public_files')
                        ->preserveFilenames()
                        ->helperText('Exposé de motif, décision du conseil des ministres, etc. ')
                        ->enableDownload(),
                    FileUpload::make('documentPrivate')
                        // ->required()
                        ->multiple()
                        ->helperText('Liste des personnes proposées, CV, Diplômes, ')
                        ->label('CONFIDENTIELS (accessibles uniquement par la Présidence et la Primature)')
                        ->enableOpen()
                        ->maxSize(1024)
                        ->directory('confidential_files')
                        ->preserveFilenames()
                        ->enableDownload(),
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
                    ->label('Date de soumission')
                    ->dateTime('d/m/Y'),

                TextColumn::make('objet')
                    ->searchable()
                    ->sortable()
                    ->tooltip(fn (Model $record): string => " {$record->content}")
                    ->html()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('LIBELLE'),
                TextColumn::make('type.title')
                    ->searchable()
                    ->sortable()
                    ->tooltip(fn (Model $record): string => " {$record->type->description}")
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
                        'primary' => static fn ($state): bool => $state === 'Examen Presidence',
                        'danger' => static fn ($state): bool => $state === 'Retour SGG',

                        'danger' => static fn ($state): bool => $state === 'Retour Primature',
                        'danger' => static fn ($state): bool => $state === 'Retour Presidence',
                        'success' => static fn ($state): bool => $state === 'Signé par la Presidence',
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
                IconColumn::make('okPRG')
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('VALIDER PRESIDENCE ?'),
                TextColumn::make('content')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('CONTENU DU DECRET'),
                TextColumn::make('created_at')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Date de création')
                    ->dateTime('d/m/Y'),
            ])
            ->poll('3600s')->striped()->deferLoading()
            ->filters([Tables\Filters\TrashedFilter::make(), DateRangeFilter::make('created_at')->label('Filtrer par la date de création')])
            ->actions([Tables\Actions\ActionGroup::make([Tables\Actions\ViewAction::make(), Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make(), Tables\Actions\ForceDeleteAction::make(), Tables\Actions\RestoreAction::make()])])
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
                    ->label('PREPARER UN PROJET DE DECRET'),
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
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
