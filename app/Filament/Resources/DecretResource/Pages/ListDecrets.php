<?php

namespace App\Filament\Resources\DecretResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\DecretResource;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\MarkdownEditor;

class ListDecrets extends ListRecords
{
    protected static string $resource = DecretResource::class;
    protected static ?string $title = 'LISTE DES PROJETS DE DECRET';
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->size('lg')
                ->createAnother(false)
                ->label('PREPARER UN PROJET DE DECRET'),

            // Action::make('create')
            // ->steps([
            //     Step::make('Name')
            //     ->description('Give the category a unique name')
            //     ->schema([
            //         TextInput::make('name')
            //             ->required()
            //             ->live()
            //             ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
            //         TextInput::make('slug')
            //             ->disabled()
            //             ->required()
            //             ->unique(Category::class, 'slug'),
            //     ])
            //     ->columns(2),
            //     Step::make('Description')
            //     ->description('Add some extra details')
            //     ->schema([
            //         MarkdownEditor::make('description'),
            //     ]),
            //     Step::make('Visibility')
            //     ->description('Control who can view it')
            //     ->schema([
            //         Toggle::make('is_visible')
            //             ->label('Visible to customers.')
            //             ->default(true),
            //     ]),
            // ])
        ];
    }
}