<?php

namespace App\Filament\Resources\AvatarResource\Pages;

use App\Filament\Resources\AvatarResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAvatar extends EditRecord
{
    protected static string $resource = AvatarResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
