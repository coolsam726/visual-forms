<?php

namespace Coolsam\VisualForms\ComponentTypes;

class Select extends CheckboxList
{
    public function getOptionName(): string
    {
        return __('Select');
    }

    /**
     * @throws \Exception
     */
    public function makeComponent(): \Filament\Forms\Components\Select
    {
        $record = $this->getRecord();
        if (! $record) {
            throw new \Exception('Record not found');
        }
        $component = \Filament\Forms\Components\Select::make($record->getAttribute('name'));
        $this->configureComponent($component);

        return $component;
    }

    protected function configureComponent(&$component): void
    {
        parent::configureComponent($component);
        $this->makeAffixes($component);
    }

    public function getMainSchema(): array
    {
        $schema = parent::getMainSchema();
        return array_merge($schema, $this->affixesSchema());
    }
}
