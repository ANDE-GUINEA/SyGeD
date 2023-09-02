<?php

namespace App\Filament\Resources\DecretResource\Widgets;

use App\Models\Decret;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class DecretChart extends ApexChartWidget
// ChartWidget
{
    protected static string $color = 'info';

    // protected function getData(): array
    // {
    //     $data = Trend::model(Decret::class)
    //         ->between(
    //             start: now()->startOfMonth(),
    //             end: now()->endOfMonth(),
    //         )
    //         ->perMonth()
    //         ->count();

    //     return [
    //         'datasets' => [
    //             [
    //                 'label' => 'GRAPHIQUE DES DECRETS',
    //                 'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
    //             ],
    //         ],
    //         'labels' => $data->map(fn (TrendValue $value) => $value->date),
    //     ];
    // }

    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'DecretChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'GRAPHIQUE DES DECRETS';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    // protected function getFormSchema(): array
    // {
    //     return [

    //         TextInput::make('objet')
    //             ->default('My Chart'),

    //         DatePicker::make('date_start')
    //             ->default('2023-01-01'),

    //         DatePicker::make('date_end')
    //             ->default('2023-12-31')

    //     ];
    // }
    protected function getOptions(): array
    {
        // $title = $this->filterFormData['objet'];
        // $dateStart = $this->filterFormData['date_start'];
        // $dateEnd = $this->filterFormData['date_end'];
        $data = Trend::model(Decret::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'GRAPHIQUE DES DECRETS',
                    'data' =>
                    $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'xaxis' => [
                'categories' =>
                $data->map(fn (TrendValue $value) => $value->date),
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'colors' => ['#6366f1'],
        ];
    }


    protected function getType(): string
    {
        return 'line';
    }
}