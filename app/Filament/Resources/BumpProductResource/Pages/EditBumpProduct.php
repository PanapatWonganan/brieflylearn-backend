<?php

namespace App\Filament\Resources\BumpProductResource\Pages;

use App\Filament\Resources\BumpProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBumpProduct extends EditRecord
{
    protected static string $resource = BumpProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
