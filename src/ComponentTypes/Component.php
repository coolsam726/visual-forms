<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Coolsam\VisualForms\Facades\VisualForms;
use Coolsam\VisualForms\Models\VisualFormComponent;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Get;
use Filament\Forms\Set;

abstract class Component
{
    public function __construct(private readonly ?VisualFormComponent $record = null) {}

    abstract public function getOptionName(): string;

    abstract public function getSupportedProps(): array;

    abstract public function isLayout(): bool;

    abstract public function hasChildren(): bool;

    public function getProps(): array
    {
        $component = $this->record;
        $supported = $this->getSupportedProps();
        $props = [];
        if ($component) {
            $componentProps = collect($component->getAttribute('props'));
            foreach ($supported as $prop) {
                $props[$prop] = $componentProps->get($prop);
            }
        }

        return $props;
    }

    public function getRecord(): ?VisualFormComponent
    {
        return $this->record;
    }

    abstract public function makeComponent();

    /**
     * You can pass in an array of Tabs or simply an array of Components together with the tab label if you prefer all components to be in a single tab.
     *
     * @param  Tab[]|\Filament\Forms\Components\Component[]  $tabs
     */
    protected function extendCommonSchema(array $tabs = [], ?string $tabLabel = null): array
    {
        $isTabs = false;
        // if all elements are instances of tab, then tab = true
        foreach ($tabs as $element) {
            if (! ($element instanceof Tab)) {
                $isTabs = false;

                break;
            }
            $isTabs = true;
        }

        return [
            \Filament\Forms\Components\Tabs::make()
                ->schema([
                    Tab::make(__('Common Details'))->schema([
                        \Filament\Forms\Components\TextInput::make('name')->label(__('Name'))
                            ->hint(__('e.g first_name'))
                            ->required()
                            ->live(debounce: 500)
                            ->afterStateUpdated(function ($state, Set $set) {
                                $set(
                                    'label',
                                    str($state)->camel()->snake()->title()->replace('_', ' ')->toString()
                                );
                                $set('column_span', [
                                    ['key' => 'default', 'value' => 1],
                                    ['key' => 'sm', 'value' => null],
                                    ['key' => 'md', 'value' => null],
                                    ['key' => 'lg', 'value' => null],
                                    ['key' => 'xl', 'value' => null],
                                    ['key' => '2xl', 'value' => null],
                                ]);
                                $set('columns', [
                                    ['key' => 'default', 'value' => 2],
                                    ['key' => 'sm', 'value' => null],
                                    ['key' => 'md', 'value' => null],
                                    ['key' => 'lg', 'value' => null],
                                    ['key' => 'xl', 'value' => null],
                                    ['key' => '2xl', 'value' => null],
                                ]);
                                $set('column_start', [
                                    ['key' => 'default', 'value' => 1],
                                    ['key' => 'sm', 'value' => null],
                                    ['key' => 'md', 'value' => null],
                                    ['key' => 'lg', 'value' => null],
                                    ['key' => 'xl', 'value' => null],
                                    ['key' => '2xl', 'value' => null],
                                ]);
                            }),
                        \Filament\Forms\Components\TextInput::make('label')->label(__('Label'))
                            ->required(fn (Get $get) => $get('first_name') !== null)
                            ->hint(__('e.g First Name')),
                        \Filament\Forms\Components\Textarea::make('description')->columnSpanFull()->label(__('Description'))->default(''),
                        \Filament\Forms\Components\Checkbox::make('is_active')->default(true),
                    ]),
                    Tab::make(__('Columns & Width'))->schema([
                        \Filament\Forms\Components\Checkbox::make('column_span_full')
                            ->live()
                            ->required()
                            ->default(false)
                            ->label(__('Column Span Full Width')),
                        TableRepeater::make('columns')
                            ->deletable(false)
                            ->addable(false)
                            ->headers([
                                Header::make('key')->label(__('Screen Size')),
                                Header::make('value')->label(__('Value')),
                            ])->schema([
                                \Filament\Forms\Components\Select::make('key')
                                    ->options([
                                        'default' => __('Default'),
                                        'sm' => __('Small'),
                                        'md' => __('Medium'),
                                        'lg' => __('Large'),
                                        'xl' => __('Extra Large'),
                                        '2xl' => __('2x Extra Large'),
                                    ]),
                                \Filament\Forms\Components\TextInput::make('value')
                                    ->default(2)
                                    ->integer()
                                    ->type('number')
                                    ->minValue(1)
                                    ->maxValue(12),
                            ])
                            ->columnSpanFull()
                            ->label(__('Columns')),
                        TableRepeater::make('column_start')
                            ->hidden(fn (Get $get) => $get('column_span_full'))
                            ->deletable(false)
                            ->addable(false)
                            ->headers([
                                Header::make('key')->label(__('Screen Size')),
                                Header::make('value')->label(__('Value')),
                            ])->schema([
                                \Filament\Forms\Components\Select::make('key')
                                    ->options([
                                        'default' => __('Default'),
                                        'sm' => __('Small'),
                                        'md' => __('Medium'),
                                        'lg' => __('Large'),
                                        'xl' => __('Extra Large'),
                                        '2xl' => __('2x Extra Large'),
                                    ]),
                                \Filament\Forms\Components\TextInput::make('value')
                                    ->default(1)
                                    ->integer()
                                    ->type('number')
                                    ->minValue(1)
                                    ->maxValue(12),
                            ])
                            ->deletable(false)
                            ->addable(false)
                            ->columnSpanFull()
                            ->label(__('Columns'))
                            ->helperText(__('Applicable only for layout components')),
                        TableRepeater::make('column_span')
                            ->columnSpanFull()
                            ->hidden(fn (Get $get) => $get('column_span_full'))
                            ->headers([
                                Header::make('key')->label(__('Screen Size')),
                                Header::make('value')->label(__('Value')),
                            ])
                            ->schema([
                                \Filament\Forms\Components\Select::make('key')
                                    ->searchable()
                                    ->live()
                                    ->options(fn (Get $get) => collect([
                                        'default' => __('Default'),
                                        'sm' => __('Small'),
                                        'md' => __('Medium'),
                                        'lg' => __('Large'),
                                        'xl' => __('Extra Large'),
                                        '2xl' => __('2x Extra Large'),
                                    ])),
                                \Filament\Forms\Components\TextInput::make('value')
                                    ->default(1)
                                    ->integer()
                                    ->type('number')
                                    ->minValue(1)
                                    ->maxValue(12),
                            ])
                            ->label(__('Column Span'))
                            ->helperText(__('Applicable only for layout components')),

                    ]),
                    ...($isTabs ? $tabs : [Tab::make($tabLabel ?? 'Specific Field Details')->schema($tabs)]),
                ]),
        ];
    }

    abstract public function getMainSchema(): array;

    abstract public function getValidationSchema(): array;

    /**
     * @param  \Filament\Forms\Components\Component[]  $schema
     * @return \Filament\Forms\Components\Component[]
     */
    protected function extendValidationSchema(array $schema = []): array
    {
        $options = VisualForms::getValidationRules();
        return [
            ...$schema,
            \Filament\Forms\Components\Fieldset::make(__('Extra Validation Rules'))->schema([
                TableRepeater::make('validation_rules')
                    ->headers([
                        Header::make('key')->label(__('Rule')),
                        Header::make('value')->label(__('Value (optional)')),
                    ])
                    ->schema([
                        \Filament\Forms\Components\Select::make('key')
                            ->options($options)
                            ->searchable()
                            ->live()
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('value')
                            ->placeholder(__('Optional'))
                            ->live(),
                    ])
                    ->columnSpanFull(),
            ]),
        ];
    }

    public function makeRules()
    {
        if ($this->isLayout()) {
            return [];
        }
        if (! $this->getRecord()) {
            return [];
        }
        $rules = $this->record->getAttribute('validation_rules') ?? [];
        if (! count($rules)) {
            return $rules;
        }

        return $rules->mapWithKeys(fn (
            $rule
        ) => [$rule['key'] => $rule['key'] ? "{$rule['key']}:{$rule['value']}" : $rule['value']])->values()->toArray();
    }
}
