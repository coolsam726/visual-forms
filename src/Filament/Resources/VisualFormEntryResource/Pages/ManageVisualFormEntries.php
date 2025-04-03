<?php

namespace Coolsam\VisualForms\Filament\Resources\VisualFormEntryResource\Pages;

use Coolsam\VisualForms\Filament\Resources\VisualFormEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageVisualFormEntries extends ManageRecords
{

    public static function getResource(): string
    {
        return \Config::get('visual-forms.resources.visual-form-entry.resource', VisualFormEntryResource::class);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
