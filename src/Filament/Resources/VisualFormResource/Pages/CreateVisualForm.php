<?php

namespace Coolsam\VisualForms\Filament\Resources\VisualFormResource\Pages;

use Coolsam\VisualForms\Filament\Resources\VisualFormResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVisualForm extends CreateRecord
{
    public static function getResource(): string
    {
        return \Config::get('visual-forms.resources.visual-form', VisualFormResource::class);
    }

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
