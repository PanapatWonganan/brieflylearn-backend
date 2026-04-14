<?php

namespace App\Filament\Resources\EmailSequenceSubscriptionResource\Pages;

use App\Filament\Resources\EmailSequenceSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmailSequenceSubscriptions extends ListRecords
{
    protected static string $resource = EmailSequenceSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
