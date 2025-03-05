<?php

namespace Coolsam\VisualForms\Filament\Resources\VisualFormResource\Pages;

use Coolsam\VisualForms\Filament\Resources\VisualFormResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVisualForm extends CreateRecord
{
    protected static string $resource = VisualFormResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
