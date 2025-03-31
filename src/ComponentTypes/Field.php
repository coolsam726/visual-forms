<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Utils;
use Filament\Forms\Get;

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

    public function getSpecificValidationSchema(): array
    {
        return [
            \Filament\Forms\Components\Fieldset::make(__('Basic Validation'))
                ->columns([
                    'default' => 3,
                    'md' => 3,
                    'xl' => 4,
                    '2xl' => 6,
                ])
                ->statePath('props')
                ->schema(function (Get $get) {
                    if (! $get('component_type')) {
                        return [];
                    }
                    if (! $get('name')) {
                        return [];
                    }
                    $component = (Utils::instantiateClass($get('component_type'))?->letThereBe($get('name')));
                    if (! $component) {
                        return [];
                    }

                    return [
                        \Filament\Forms\Components\Checkbox::make('required')->live()->default(true)->visible(fn (
                        ) => method_exists($component, 'required')),
                        \Filament\Forms\Components\Checkbox::make('unique')->live()->default(false)->visible(fn (
                        ) => method_exists($component, 'unique')),
                        \Filament\Forms\Components\Checkbox::make('readOnly')->label(__('Read Only'))->live()->default(false)->visible(fn (
                        ) => method_exists($component, 'readOnly')),
                        \Filament\Forms\Components\Checkbox::make('disabled')->label(__('Disabled'))->live()->default(false)->visible(fn (
                        ) => method_exists($component, 'disabled')),
                        \Filament\Forms\Components\Checkbox::make('email')->label(__('Email'))->live()->default(false)->visible(fn (
                        ) => method_exists($component, 'email')),
                        \Filament\Forms\Components\Checkbox::make('numeric')->label(__('Numeric'))->visible(fn (
                        ) => ! $get('email') && method_exists($component, 'numeric'))->default(false)->live(),
                        \Filament\Forms\Components\Checkbox::make('integer')->label(__('Integer'))->live()->default(false)->visible(fn (
                        ) => ! $get('email') && method_exists($component, 'integer')),
                        \Filament\Forms\Components\Checkbox::make('password')->label(__('Password'))->live()->default(false)->visible(fn (
                        ) => method_exists($component, 'password')),
                        \Filament\Forms\Components\Checkbox::make('revealable')->label(__('Revealable'))->default(false)->visible(fn (
                        ) => $get('password') && method_exists($component, 'revealable')),
                        \Filament\Forms\Components\Checkbox::make('tel')->label(__('Telephone'))->live()->default(false)->visible(fn (
                        ) => method_exists($component, 'tel')),
                        \Filament\Forms\Components\Checkbox::make('url')->label(__('URL'))->live()->default(false)->visible(fn (
                        ) => method_exists($component, 'url')),
                    ];
                }),
            \Filament\Forms\Components\Fieldset::make(__('Text Properties'))
                ->statePath('props')
                ->columns([
                    'default' => 3,
                    'md' => 3,
                    'xl' => 4,
                    '2xl' => 5,
                ])
                ->schema(function (Get $get) {
                    if (! $get('component_type')) {
                        return [];
                    }
                    if (! $get('name')) {
                        return [];
                    }
                    $component = (Utils::instantiateClass($get('component_type'))?->letThereBe($get('name')));
                    if (! $component) {
                        return [];
                    }

                    return [
                        \Filament\Forms\Components\TextInput::make('length')->numeric()->live()->label(__('Length'))
                            ->visible(fn () => method_exists($component, 'length')),
                        \Filament\Forms\Components\TextInput::make('minLength')->label(__('Minimum Length'))
                            ->integer()
                            ->live()
                            ->disabled(fn (Get $get) => (bool) $get('length'))
                            ->visible(fn () => method_exists($component, 'minLength')),
                        \Filament\Forms\Components\TextInput::make('maxLength')->label(__('Maximum Length'))
                            ->integer()
                            ->live()
                            ->gte(fn (Get $get) => $get('minLength'))
                            ->disabled(fn (Get $get) => (bool) $get('length'))
                            ->visible(fn () => method_exists($component, 'minLength')),
                        \Filament\Forms\Components\TextInput::make('same')->label(__('Same As'))
                            ->helperText(__('Enter a field in this form to compare to.'))
                            ->placeholder(__('statePath e.g confirm_password'))
                            ->live(onBlur: true)->visible(fn () => method_exists($component, 'same')),
                        \Filament\Forms\Components\TextInput::make('different')->label(__('Different From'))
                            ->helperText(__('Enter a field in this form to compare to.'))
                            ->placeholder(__('statePath e.g confirm_password'))
                            ->live(onBlur: true)->visible(fn () => method_exists($component, 'different')),
                    ];
                }),
            \Filament\Forms\Components\Fieldset::make(__('Comparisons'))
                ->statePath('props')
                ->columns(['md' => 2, 'xl' => 3])
                ->schema(function (Get $get) {
                    if (! $get('component_type')) {
                        return [];
                    }
                    if (! $get('name')) {
                        return [];
                    }
                    $component = (Utils::instantiateClass($get('component_type'))?->letThereBe($get('name')));
                    if (! $component) {
                        return [];
                    }

                    return [
                        \Filament\Forms\Components\TextInput::make('gt')->label(__('Greater Than'))
                            ->helperText(__('Enter a field in this form to compare to.'))
                            ->prefix('>')
                            ->placeholder(__('e.g purchase_price'))
                            ->live(onBlur: true)->visible(fn () => method_exists($component, 'gt')),
                        \Filament\Forms\Components\TextInput::make('lt')->label(__('Less Than'))
                            ->prefix('<')
                            ->helperText(__('Enter a field in this form to compare to.'))
                            ->placeholder(__('e.g selling_price'))
                            ->live(onBlur: true)->visible(fn () => method_exists($component, 'lt')),
                        \Filament\Forms\Components\TextInput::make('gte')
                            ->prefix('>=')
                            ->label(__('Greater than or Equal to'))
                            ->helperText(__('Enter a field in this form to compare to.'))
                            ->placeholder(__('e.g selling_price'))->live(onBlur: true)->visible(fn (
                            ) => method_exists($component, 'gte')),
                        \Filament\Forms\Components\TextInput::make('lte')
                            ->prefix('<=')
                            ->label(__('Less than or Equal to'))
                            ->visible(fn () => method_exists($component, 'lte'))
                            ->helperText(__('Enter a field in this form to compare to.'))
                            ->placeholder(__('e.g purchase_price'))->live(onBlur: true),
                        \Filament\Forms\Components\TextInput::make('minValue')->label(__('Minimum Value'))
                            ->integer()
                            ->live()
                            ->visible(fn (Get $get) => $get('numeric') && method_exists($component, 'minValue')),
                        \Filament\Forms\Components\TextInput::make('maxValue')->label(__('Maximum Value'))
                            ->integer()
                            ->live()
                            ->visible(fn (Get $get) => $get('numeric') && method_exists($component, 'maxValue')),
                        \Filament\Forms\Components\TextInput::make('step')->label(__('Step'))
                            ->numeric()
                            ->live()
                            ->visible(fn (Get $get) => $get('numeric') && method_exists($component, 'step')),
                    ];
                }),
            \Filament\Forms\Components\Fieldset::make(__('Date-Time Validation'))
                ->statePath('props')
                ->visible(function (Get $get) {
                    if (! ($get('component_type') && $get('name'))) {
                        return false;
                    }
                    $component = (Utils::instantiateClass($get('component_type'))?->letThereBe($get('name')));

                    // show if this extends DateTimePicker class or is instance of DateTimePicker
                    return $component instanceof \Filament\Forms\Components\DateTimePicker;
                })
                ->columns(['md' => 2])
                ->schema(function (Get $get) {
                    if (! $get('component_type')) {
                        return [];
                    }
                    if (! $get('name')) {
                        return [];
                    }
                    $component = (Utils::instantiateClass($get('component_type'))?->letThereBe($get('name')));
                    if (! $component) {
                        return [];
                    }

                    return [
                        \Filament\Forms\Components\DateTimePicker::make('maxDate')->label(__('Max Date'))
                            ->seconds(false)->native(false)
                            ->prefixIcon('heroicon-o-calendar')->inlinePrefix()
                            ->visible(fn (Get $get) => method_exists($component, 'maxDate')),
                        \Filament\Forms\Components\DateTimePicker::make('minDate')->label(__('Min Date'))
                            ->seconds(false)->native(false)
                            ->prefixIcon('heroicon-o-calendar')->inlinePrefix()
                            ->visible(fn (Get $get) => method_exists($component, 'minDate')),
                    ];
                }),
        ];
    }
}
