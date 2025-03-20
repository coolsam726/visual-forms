<?php

namespace Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource\Pages;

use Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource;
use Coolsam\VisualForms\Filament\Resources\VisualFormResource;
use Coolsam\VisualForms\Models\VisualFormComponent;
use Coolsam\VisualForms\Utils;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditVisualFormComponent extends EditRecord
{
    public static function getResource(): string
    {
        return \Config::get('visual-forms.resources.visual-form-component', VisualFormComponentResource::class);
    }

    public function getHeading(): string | Htmlable
    {
        $componentInstance = Utils::instantiateClass($this->record->getAttribute('component_type'));

        return 'Edit [' . $componentInstance->getOptionName() . '] Component';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return $this->record->getAttribute('label');
    }

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
