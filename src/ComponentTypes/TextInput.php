<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Utils;
use Exception;
use Filament\Forms\Get;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;

class TextInput extends Field
{
    public function getOptionName(): string
    {
        return __('Text Input');
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
                ->schema([
                    \Filament\Forms\Components\Checkbox::make('required')->live()->default(true),
                    \Filament\Forms\Components\Checkbox::make('unique')->live()->default(false),
                    \Filament\Forms\Components\Checkbox::make('readOnly')->label(__('Read Only'))->live()->default(false),
                    \Filament\Forms\Components\Checkbox::make('disabled')->label(__('Disabled'))->live()->default(false),
                    \Filament\Forms\Components\Checkbox::make('email')->label(__('Email'))->live()->default(false),
                    \Filament\Forms\Components\Checkbox::make('numeric')->label(__('Numeric'))
                        ->hidden(fn (Get $get) => $get('email'))->default(false)->live(),
                    \Filament\Forms\Components\Checkbox::make('integer')->label(__('Integer'))->live()
                        ->hidden(fn (Get $get) => $get('email'))->default(false)->live(),
                    \Filament\Forms\Components\Checkbox::make('password')->label(__('Password'))->live()->default(false),
                    \Filament\Forms\Components\Checkbox::make('revealable')->label(__('Revealable'))->default(false)
                        ->visible(fn (Get $get) => $get('password')),
                    \Filament\Forms\Components\Checkbox::make('tel')->label(__('Telephone'))->live()->default(false),
                    \Filament\Forms\Components\Checkbox::make('url')->label(__('URL'))->live()->default(false),
                ]),
            \Filament\Forms\Components\Fieldset::make(__('Text Properties'))
                ->statePath('props')
                ->columns([
                    'default' => 3,
                    'md' => 3,
                    'xl' => 4,
                    '2xl' => 5,
                ])
                ->schema([
                    \Filament\Forms\Components\TextInput::make('length')->numeric()->live()->label(__('Length'))
                        ->hidden(fn (Get $get) => $get('numeric')),
                    \Filament\Forms\Components\TextInput::make('minLength')->label(__('Minimum Length'))
                        ->integer()
                        ->live()
                        ->disabled(fn (Get $get) => (bool) $get('length'))
                        ->hidden(fn (Get $get) => $get('numeric')),
                    \Filament\Forms\Components\TextInput::make('maxLength')->label(__('Maximum Length'))
                        ->integer()
                        ->live()
                        ->gte(fn (Get $get) => $get('minLength'))
                        ->disabled(fn (Get $get) => (bool) $get('length'))
                        ->hidden(fn (Get $get) => $get('numeric')),
                    \Filament\Forms\Components\TextInput::make('same')->label(__('Same as'))
                        ->helperText(__('Enter a field in this form to compare to.'))
                        ->placeholder(__('e.g confirm_password'))
                        ->live(onBlur: true),
                    \Filament\Forms\Components\TextInput::make('mask')->label(__('Mask'))
                        ->live(),
                ]),
            \Filament\Forms\Components\Fieldset::make(__('Numeric Comparisons'))
                ->statePath('props')
                ->columns(['md' => 2, 'xl' => 3])
                ->hidden(fn (Get $get) => ! $get('props.numeric'))
                ->schema([
                    \Filament\Forms\Components\TextInput::make('gt')->label(__('Greater Than'))
                        ->helperText(__('Enter a field in this form to compare to.'))
                        ->prefix('>')
                        ->placeholder(__('e.g purchase_price'))
                        ->live(onBlur: true),
                    \Filament\Forms\Components\TextInput::make('lt')->label(__('Less Than'))
                        ->prefix('<')
                        ->helperText(__('Enter a field in this form to compare to.'))
                        ->placeholder(__('e.g selling_price'))
                        ->live(onBlur: true),
                    \Filament\Forms\Components\TextInput::make('gte')
                        ->prefix('>=')
                        ->label(__('Greater than or Equal to'))
                        ->helperText(__('Enter a field in this form to compare to.'))
                        ->placeholder(__('e.g selling_price'))->live(onBlur: true),
                    \Filament\Forms\Components\TextInput::make('lte')
                        ->prefix('<=')
                        ->label(__('Less than or Equal to'))
                        ->helperText(__('Enter a field in this form to compare to.'))
                        ->placeholder(__('e.g purchase_price'))->live(onBlur: true),
                    \Filament\Forms\Components\TextInput::make('minValue')->label(__('Minimum Value'))
                        ->integer()
                        ->live()
                        ->visible(fn (Get $get) => $get('numeric')),
                    \Filament\Forms\Components\TextInput::make('maxValue')->label(__('Maximum Value'))
                        ->integer()
                        ->live()
                        ->hidden(fn (Get $get) => $get('email') || ! $get('numeric')),
                    \Filament\Forms\Components\TextInput::make('step')->label(__('Step'))
                        ->numeric()
                        ->live()
                        ->hidden(fn (Get $get) => ! $get('numeric')),
                ]),
        ];
    }

    public function letThereBe(string $name): \Filament\Forms\Components\TextInput
    {
        return \Filament\Forms\Components\TextInput::make($name);
    }
}
