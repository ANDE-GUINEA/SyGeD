<?php

namespace App\Filament\Resources\DecretResource\Widgets;

use Filament\Tables;
use App\Models\Decret;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class LatestDecret extends BaseWidget
{
    protected static ?string $heading = 'LISTE DES DECRETS SOUMIS';
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Decret::whereNotNull('submit_at')->latest()
            )
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
                    ->toggleable(isToggledHiddenByDefault: true)
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
                TextColumn::make('type.title')
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
                        'primary' => static fn ($state): bool => $state === 'Examen Presidence',
                        'danger' => static fn ($state): bool => $state === 'Retour SGG',
                        'danger' => static fn ($state): bool => $state === 'Retour Primature',
                        'danger' => static fn ($state): bool => $state === 'Retour Presidence',
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
                IconColumn::make('okPRG')
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('VALIDER PRESIDENCE ?'),
                TextColumn::make('content')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('CONTENU'),
            ])->striped()
            ->filters([Tables\Filters\TrashedFilter::make(), DateRangeFilter::make('created_at')->label('Filtrer par la date de création')])
            // ->contentGrid([
            //     'md' => 2,
            //     'xl' => 3,
            // ])
            ->bulkActions([
                ExportBulkAction::make()
                    ->label('Exporter en Excel'),
            ]);
    }
}
