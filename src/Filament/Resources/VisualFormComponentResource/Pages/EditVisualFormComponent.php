<?php

namespace Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource\Pages;

use Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource;
use Coolsam\VisualForms\Filament\Resources\VisualFormResource;
use Coolsam\VisualForms\Models\VisualFormComponent;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVisualFormComponent extends EditRecord
{
    protected static string $resource = VisualFormComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('parent-form')->label(__('Parent Form'))
                ->icon('heroicon-o-arrow-turn-left-up')
                ->color('success')
                ->url(fn (VisualFormComponent $record) => VisualFormResource::getUrl('edit', ['record' => $record->getAttribute('form_id')])),
            Action::make('parent')->label(__('Parent Component'))
                ->visible(fn (VisualFormComponent $record) => $record->getAttribute('parent_id') !== null)
                ->icon('heroicon-o-arrow-left')
                ->url(fn (VisualFormComponent $record) => VisualFormComponentResource::getUrl('edit', ['record' => $record->getAttribute('parent_id')])),
            DeleteAction::make(),
        ];
    }
}
