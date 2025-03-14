<?php

namespace Coolsam\VisualForms\ComponentTypes;

class Section extends Component
{
    public function getOptionName(): string
    {
        return __('Section');
    }

    public function isLayout(): bool
    {
        return true;
    }

    public function hasChildren(): bool
    {
        return true;
    }

    public function makeComponent(): \Filament\Forms\Components\Section
    {
        $record = $this->getRecord();
        if (! $record) {
            throw new \Exception('Record not found');
        }
        $component = \Filament\Forms\Components\Section::make($record->getAttribute('label'));

        if ($record->getAttribute('description')) {
            $component->description($record->getAttribute('description'));
        }

        $this->makeColumns($component);
        $component->schema($this->makeChildren());
        return $component;
    }

    public function getMainSchema(): array
    {
        return $this->extendCommonSchema([]);
    }

    public function getValidationSchema(): array
    {
        return $this->extendValidationSchema();
    }
}
