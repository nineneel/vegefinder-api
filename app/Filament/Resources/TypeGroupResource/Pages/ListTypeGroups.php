<?php

namespace App\Filament\Resources\TypeGroupResource\Pages;

use App\Filament\Resources\TypeGroupResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTypeGroups extends ListRecords
{
    protected static string $resource = TypeGroupResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
