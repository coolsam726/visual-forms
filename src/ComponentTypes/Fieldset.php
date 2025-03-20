<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Utils;

class Fieldset extends Component
{
    public function getOptionName(): string
    {
        return __('Fieldset');
    }

    public function isLayout(): bool
    {
        return true;
    }

    public function hasChildren(): bool
    {
        return true;
    }

    public function makeComponent()
    {
        $record = $this->getRecord();
        if (! $record) {
            throw new \Exception('Record not found');
        }
        $component = \Filament\Forms\Components\Fieldset::make($record->getAttribute('label'));
        $this->makeColumns($component);
        if ($this->getProps()->isNotEmpty()) {
            $component->disabled(Utils::getBool($this->getProps()->get('disabled')));
        }
        $this->makeStatePath($component);
        $component->schema($this->makeChildren());

        return $component;
    }

    public function getMainSchema(): array
    {
        return $this->extendCommonSchema([
            \Filament\Forms\Components\Fieldset::make(__('Fieldset Details'))->statePath('props')
                ->schema([
                    \Filament\Forms\Components\Checkbox::make('disabled')->label(__('Disabled'))->default(false),
                ]),
        ]);
    }

    public function getValidationSchema(): array
    {
        return $this->extendValidationSchema();
    }

    public function getColumnsSchema(): array
    {
        return $this->extendColumnsSchema();
    }
}
