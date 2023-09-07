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
        if (auth()->user()->worker) {
            if (auth()->user()->worker->name === 'PRG' || auth()->user()->worker->name === 'SGG' || auth()->user()->worker->name === 'PRIMATURE') {

                $returner = auth()->user()->validations->where('type', 'retourner')->count();
                $valider = auth()->user()->validations->where('type', 'valider')->count();
                $totalSoumis = $returner;
                $Dvalider = $valider;
                $Dtitle = "VALIDES";
                $title1 = 'RETOURNES';

                $total = Validation::where('user_id', auth()->user()->id)
                    ->get()
                    ->count();
                $titleTotal = 'REÇUS';
            } else {

                # code...
                $titleTotal = "TOTAL";
                $totalSoumis = Decret::whereNotNull('submit_at')
                    ->get()
                    ->count();
                $title1 = 'SOUMIS';
                $Dvalider = Decret::where([['okSGG', true], ['okPRIMATURE', true], ['okPRG', true]])
                    ->get()
                    ->count();
                $Dtitle = "SIGNES";

                $total = Decret::all()->count();
            }
        } else {
            $totalSoumis = Decret::whereNotNull('submit_at')
                ->get()
                ->count();
            $title1 = 'TOTAL';

            $Dvalider = Decret::where([['okSGG', true], ['okPRIMATURE', true], ['okPRG', true]])
                ->get()
                ->count();
            $Dtitle = "SOUMIS";
            $titleTotal = "SIGNES";
            $total = Decret::all()->count();
        }

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
