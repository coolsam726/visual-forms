<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Utils;
use Filament\Forms\Get;
use Filament\Support\Enums\IconSize;

class Section extends Component
{
    public function getOptionName(): string
    {
        return __('Section');
    }

    public function isLayout(): bool
    {
        return true;
    }

    public function hasChildren(): bool
    {
        return true;
    }

    public function makeComponent(): \Filament\Forms\Components\Section
    {
        $record = $this->getRecord();
        if (! $record) {
            throw new \Exception('Record not found');
        }
        $component = \Filament\Forms\Components\Section::make($record->getAttribute('label'));

        if ($record->getAttribute('description')) {
            $component->description($record->getAttribute('description'));
        }

        $this->makeColumns($component);
        $this->makeStatePath($component);
        $props = $this->getProps();
        if ($props->isNotEmpty()) {
            if ($props->get('icon')) {
                $component->icon($props->get('icon'));
                $component->iconSize($props->get('iconSize') ?? IconSize::Medium->value);
            }
            $component->collapsible(Utils::getBool($props->get('collapsible')));
            $component->collapsed(Utils::getBool($props->get('collapsed')));
            $component->disabled(Utils::getBool($props->get('disabled')));
            if ($statePath = $props->get('statePath')) {
                $component->statePath($statePath);
            }
        }

        $component->schema($this->makeChildren());

        return $component;
    }

    public function getMainSchema(): array
    {
        return $this->extendCommonSchema([
            \Filament\Forms\Components\Fieldset::make(__('Section Details'))
                ->statePath('props')->schema([
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
                ])->columns([
                    'md' => 2, 'xl' => 4,
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
