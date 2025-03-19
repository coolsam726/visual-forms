<?php

namespace Coolsam\VisualForms\ComponentTypes;

class Radio extends CheckboxList
{
    public function getOptionName(): string
    {
        return __('Radio');
    }

    /**
     * @throws \Exception
     */
    public function makeComponent(): \Filament\Forms\Components\Radio
    {
        if (! $record = $this->getRecord()) {
            throw new \Exception('Record not found');
        }

        $component = \Filament\Forms\Components\Radio::make($record->getAttribute('name'));

        $this->configureComponent($component);

        return $component;
    }

    protected function getMainSchemaFields(): array
    {
        return [
            ...parent::getMainSchemaFields(),
            \Filament\Forms\Components\Checkbox::make('boolean')->inline()->label(__('Boolean'))->default(false),
        ];
    }
}
