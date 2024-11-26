<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Country;
use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmployeeStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $id = Country::where('country_code', 'ID')->withCount('employees')->first();
        $sgp = Country::where('country_code', 'SGP')->withCount('employees')->first();
        return [
            Stat::make('All Employees', Employee::all()->count()),
            Stat::make('ID Employees', $id ? $id->employees_count : 0),
            Stat::make('SGP Employees', $sgp ? $sgp->employees_count : 0),
        ];
    }
}
