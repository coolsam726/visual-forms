<?php

namespace Coolsam\VisualForms\ComponentTypes;

abstract class Layout extends Component
{
    public function isLayout(): bool
    {
        return true;
    }

    public function hasChildren(): bool
    {
        return true;
    }

    public function getSpecificBasicSchema(): array
    {
        return [
            \Filament\Forms\Components\Textarea::make('description')->label(__('Description')),
        ];
    }

    public function getSpecificValidationSchema(): array
    {
        return [];
    }
}
