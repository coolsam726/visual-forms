<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Utils;
use Illuminate\Support\Collection;

class Toggle extends Checkbox
{
    public function getOptionName(): string
    {
        return 'Toggle';
    }

    public function letThereBe(string $name): \Filament\Forms\Components\Toggle
    {
        return new \Filament\Forms\Components\Toggle($name);
    }

    public function getSpecificBasicSchema(): array
    {
        return [
            ...parent::getSpecificBasicSchema(),

            \Filament\Forms\Components\Fieldset::make(__('Toggle Icons & Colors'))->schema([
                \Filament\Forms\Components\Select::make('onIcon')->label(__('On Icon'))
                    ->options(Utils::getHeroicons())->searchable(),
                \Filament\Forms\Components\Select::make('offIcon')->label(__('Off Icon'))
                    ->options(Utils::getHeroicons())->searchable(),
                \Filament\Forms\Components\ToggleButtons::make('onColor')->label(__('On Color'))
                    ->options([
                        'primary' => __('Primary'),
                        'info' => __('Info'),
                        'success' => __('Success'),
                        'danger' => __('Danger'),
                        'warning' => __('Warning'),
                        'gray' => __('Gray'),
                    ])
                    ->colors($this->getColors()->mapWithKeys(fn ($color) => [$color => $color]))
                    ->inline(),
                \Filament\Forms\Components\ToggleButtons::make('offColor')->label(__('Off Color'))
                    ->options([
                        'primary' => __('Primary'),
                        'info' => __('Info'),
                        'success' => __('Success'),
                        'danger' => __('Danger'),
                        'warning' => __('Warning'),
                        'gray' => __('Gray'),
                    ])->colors($this->getColors()->mapWithKeys(fn ($color) => [$color => $color]))->inline(),
            ]),
        ];
    }

    protected function getColors(): Collection
    {
        return collect(Utils::getAppColors())->keys();
    }

    public function configureComponent(&$component, bool $editable): void
    {
        parent::configureComponent($component, $editable);

        $props = $this->getProps();
        if (filled($props->get('onIcon')) && method_exists($component, 'onIcon')) {
            $component->onIcon($props->get('onIcon'));
        }
        if (filled($props->get('offIcon')) && method_exists($component, 'offIcon')) {
            $component->offIcon($props->get('offIcon'));
        }
        if (filled($props->get('onColor')) && method_exists($component, 'onColor')) {
            $component->onColor($props->get('onColor'));
        }
        if (filled($props->get('offColor')) && method_exists($component, 'offColor')) {
            $component->offColor($props->get('offColor'));
        }
    }
}
