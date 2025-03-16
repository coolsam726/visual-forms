<?php

namespace Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource\Pages;

use Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVisualFormComponents extends ListRecords
{
    protected static string $resource = VisualFormComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
