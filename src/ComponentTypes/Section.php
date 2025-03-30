<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Filament\Forms;
use Filament\Forms\Get;
use Filament\Support\Enums\IconSize;

class Section extends Layout
{
    public function getOptionName(): string
    {
        return __('Section');
    }

    public function letThereBe(string $name): Forms\Components\Section
    {
        return Forms\Components\Section::make($name);
    }

    public function getSpecificBasicSchema(): array
    {
        return [
            \Filament\Forms\Components\TextInput::make('icon')->label(__('Icon'))->live(debounce: 1000)->placeholder(__('heroicon-o-clipboard-list')),
            \Filament\Forms\Components\Radio::make('iconSize')->label(__('Icon Size'))->options([
                IconSize::Small->value => __('Small'),
                IconSize::Medium->value => __('Medium'),
                IconSize::Large->value => __('Large'),
            ])->default(IconSize::Medium->value)->inline()->hidden(fn (Get $get) => ! $get('icon')),
            \Filament\Forms\Components\Checkbox::make('collapsible')->label(__('Collapsible'))->live()->default(false),
            \Filament\Forms\Components\Checkbox::make('collapsed')->label(__('Collapsed'))->live()->default(false)
                ->hidden(fn (Get $get) => ! $get('collapsible')),
            \Filament\Forms\Components\Checkbox::make('disabled')->label(__('Disabled'))->default(false),
        ];
    }
}
