<?php

namespace Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource\Pages;

use Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVisualFormComponent extends CreateRecord
{
    protected static string $resource = VisualFormComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
