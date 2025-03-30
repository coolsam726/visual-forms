<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Concerns\HasOptions;

class CheckboxList extends Field
{
    use HasOptions;

    public function getOptionName(): string
    {
        return __('Checkbox List');
    }

    public function letThereBe(string $name): \Filament\Forms\Components\CheckboxList
    {
        return \Filament\Forms\Components\CheckboxList::make($name);
    }
}
