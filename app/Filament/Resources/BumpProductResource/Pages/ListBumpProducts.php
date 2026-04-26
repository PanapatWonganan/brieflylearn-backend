<?php

namespace App\Filament\Resources\BumpProductResource\Pages;

use App\Filament\Resources\BumpProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBumpProducts extends ListRecords
{
    protected static string $resource = BumpProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
