<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Utils;

abstract class Field extends Component
{
    public function isLayout(): bool
    {
        return false;
    }

    public function hasChildren(): bool
    {
        return false;
    }

    public function getSpecificBasicSchema(): array
    {
        return [
            \Filament\Forms\Components\Fieldset::make(__('Basic Details'))
                ->columns(['md' => 2, 'xl' => 3])
                ->schema([
                    \Filament\Forms\Components\TextInput::make('placeholder')->label(__('Placeholder'))->autocapitalize(),
                    \Filament\Forms\Components\TextInput::make('hint')->label(__('Hint')),
                    \Filament\Forms\Components\Select::make('hintIcon')->label(__('Hint icon'))->options(Utils::getHeroicons())->searchable(),
                    \Filament\Forms\Components\Textarea::make('helperText')->columnSpanFull()->label(__('Helper Text')),
                    \Filament\Forms\Components\ToggleButtons::make('autocapitalize')->boolean()->inline()->default(false)->label(__('Autocapitalize')),
                    \Filament\Forms\Components\ToggleButtons::make('autocomplete')->boolean()->inline()->default(false)->label(__('Autocomplete')),
                    \Filament\Forms\Components\ToggleButtons::make('inline')->inline()->boolean()->default(false),
                    \Filament\Forms\Components\ToggleButtons::make('inlineLabel')->inline()->boolean()->default(false),
                ]),
        ];
    }

    public function getSpecificValidationSchema(): array
    {
        return [
            \Filament\Forms\Components\Checkbox::make('required')->label(__('Required'))->default(false),
            \Filament\Forms\Components\Checkbox::make('unique')->label(__('Unique'))->default(false),
        ];
    }
}
