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
    public function makeComponent(bool $editable = false)
    {
        if (! $record = $this->getRecord()) {
            throw new \Exception('Record not found');
        }

        $component = \Filament\Forms\Components\Radio::make($record->getAttribute('name'));

        $this->configureComponent($component);

        $this->makeEditableAction($component, $editable);

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
