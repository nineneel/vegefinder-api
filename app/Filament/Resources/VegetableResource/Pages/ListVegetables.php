<?php

namespace App\Filament\Resources\VegetableResource\Pages;

use App\Filament\Resources\VegetableResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVegetables extends ListRecords
{
    protected static string $resource = VegetableResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
