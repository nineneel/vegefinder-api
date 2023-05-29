<?php

namespace App\Filament\Resources\AvatarResource\Pages;

use App\Filament\Resources\AvatarResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAvatar extends CreateRecord
{
    protected static string $resource = AvatarResource::class;
}
