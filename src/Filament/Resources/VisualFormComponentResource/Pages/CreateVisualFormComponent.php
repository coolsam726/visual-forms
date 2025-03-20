<?php

namespace Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource\Pages;

use Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVisualFormComponent extends CreateRecord
{
    public static function getResource(): string
    {
        return \Config::get('visual-forms.resources.visual-form-component', VisualFormComponentResource::class);
    }

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
