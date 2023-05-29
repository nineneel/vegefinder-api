<?php

namespace App\Filament\Resources\TypeGroupResource\Pages;

use App\Filament\Resources\TypeGroupResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTypeGroup extends EditRecord
{
    protected static string $resource = TypeGroupResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
