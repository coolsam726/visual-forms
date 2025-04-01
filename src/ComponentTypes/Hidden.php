<?php

namespace Coolsam\VisualForms\ComponentTypes;

class Hidden extends  Field {
    public function getOptionName(): string
    {
        return __('Hidden');
    }

    public function letThereBe(string $name): \Filament\Forms\Components\Hidden
    {
        return \Filament\Forms\Components\Hidden::make($name);
    }
}
