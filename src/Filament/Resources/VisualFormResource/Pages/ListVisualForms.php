<?php

namespace Coolsam\VisualForms\Filament\Resources\VisualFormResource\Pages;

use Coolsam\VisualForms\Filament\Resources\VisualFormResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVisualForms extends ListRecords
{
    protected static string $resource = VisualFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
