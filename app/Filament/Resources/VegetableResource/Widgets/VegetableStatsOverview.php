<?php

namespace App\Filament\Resources\VegetableResource\Widgets;

use App\Models\Avatar;
use App\Models\Type;
use App\Models\TypeGroup;
use App\Models\Vegetable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class VegetableStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Vegetable', Vegetable::all()->count())
                ->description('Total of Vegetable')
                ->color('success'),
            Card::make('Type', Type::all()->count())
                ->description('Total of Type')
                ->color('primary'),
            Card::make('Type Group', TypeGroup::all()->count())
                ->description('Total of Type')
                ->color('danger'),
            Card::make('Avatar', Avatar::all()->count())
                ->description('Total of Avatar')
                ->color('secondary'),
        ];
    }
}
