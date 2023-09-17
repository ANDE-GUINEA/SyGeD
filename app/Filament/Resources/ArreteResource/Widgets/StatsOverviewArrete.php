<?php

namespace App\Filament\Resources\ArreteResource\Widgets;

use App\Models\Arrete;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverviewArrete extends BaseWidget
{
    protected static bool $isLazy = true;
    protected function getStats(): array
    {
        if (auth()->user()->worker) {
            if (auth()->user()->worker->name === 'SGG' || auth()->user()->worker->name === 'PRIMATURE') {

                $returner = auth()->user()->validations->where('type', 'retourner')->where('arrete_id' != '')->count();
                $valider = auth()->user()->validations->where('type', 'valider')->where('arrete_id' != '')->count();
                $totalSoumis = $returner;
                $Dvalider = $valider;
                $Dtitle = "TOTAL DES ARRETES VALIDES";
                $title1 = 'TOTAL DES ARRETES RETOURNES';

                // $total = Validation::where('user_id', auth()->user()->id)
                //     ->get()
                //     ->count();
                $total = auth()->user()->departement->inbox->arretes->count();
                // $titleTotal = 'TOTAL';
                // $titleTotal = 'TOTAL REÇU';
                $titleTotal = "TOTAL DES ARRETES EN BASE";
            } else {
                # code...
                $titleTotal = "TOTAL DES ARRETES EN BASE";
                $totalSoumis = Arrete::whereNotNull('submit_at')
                    ->get()
                    ->count();
                $title1 = 'TOTAL DES ARRETES SOUMIS';
                $Dvalider = Arrete::where([['okSGG', true], ['okPRIMATURE', true]])
                    ->get()
                    ->count();
                $Dtitle = "TOTAL DES ARRETES SIGNES";

                $total = Arrete::all()->count();
            }
        } else {
            $totalSoumis = Arrete::whereNotNull('submit_at')
                ->get()
                ->count();
            $title1 = 'TOTAL DES ARRETES EN BASE';

            $Dvalider = Arrete::where([['okSGG', true], ['okPRIMATURE', true],])
                ->get()
                ->count();
            $Dtitle = "TOTAL DES ARRETES SIGNES";
            $titleTotal = "TOTAL DES ARRETES EN BASE";
            $total = Arrete::all()->count();
        }

        return [
            Stat::make("$titleTotal", "$total")
                ->descriptionIcon('heroicon-m-folder-open')
                ->description('ARRETES')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => '$dispatch("setStatusFilter", "processed")',
                ])

                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary'),
            Stat::make("$title1", "$totalSoumis")
                ->description('ARRETES')
                ->color('primary')
                ->descriptionIcon('heroicon-m-folder-open')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary'),
            Stat::make("$Dtitle", "$Dvalider")
                ->description('ARRETES')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('primary')
                ->descriptionIcon('heroicon-m-folder-open'),
        ];
    }
}
