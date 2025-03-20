<?php

namespace Coolsam\VisualForms\Filament\Resources\VisualFormResource\Pages;

use Coolsam\VisualForms\Filament\Resources\VisualFormResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVisualForms extends ListRecords
{
    public static function getResource(): string
    {
        return \Config::get('visual-forms.resources.visual-form', VisualFormResource::class);
    }


    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
