<?php

namespace App\Filament\Widgets;

use App\Models\Decret;
use App\Models\Validation;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected static bool $isLazy = true;

    protected function w()
    {
        if (auth()->user()->worker->name === 'PRG' || auth()->user()->worker->name === 'SGG' || auth()->user()->worker->name === 'PRIMATURE') {
            return Stat::make("title", "222")
                ->description('DECRETS')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary')
                ->descriptionIcon('heroicon-m-folder-open');
        }
    }
    protected function getStats(): array
    {
        $total = Decret::all()->count();
        $totalSoumis = Decret::whereNotNull('submit_at')->get()->count();
        $Dvalider = Decret::where([
            ['okSGG', true],
            ['okPRIMATURE', true],
            ['okPRG', true]
        ])->get()->count();

        return [
            Stat::make("$titleTotal", "$total")
                ->description('DECRETS')
                ->color('primary')
                ->descriptionIcon('heroicon-m-folder-open')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary'),
            Stat::make("$title1", "$totalSoumis")
                ->description('DECRETS')
                ->color('primary')
                ->descriptionIcon('heroicon-m-folder-open')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary'),
            Stat::make("$Dtitle", "$Dvalider")
                ->description('DECRETS')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary')
                ->descriptionIcon('heroicon-m-folder-open'),

        ];
    }
}