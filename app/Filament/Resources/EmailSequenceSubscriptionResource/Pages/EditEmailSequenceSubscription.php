<?php

namespace App\Filament\Resources\EmailSequenceSubscriptionResource\Pages;

use App\Filament\Resources\EmailSequenceSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmailSequenceSubscription extends EditRecord
{
    protected static string $resource = EmailSequenceSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
