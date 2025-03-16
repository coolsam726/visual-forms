<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Coolsam\VisualForms\Facades\VisualForms;
use Coolsam\VisualForms\Models\VisualFormComponent;
use Coolsam\VisualForms\Utils;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

abstract class Component
{
    public function __construct(private readonly ?VisualFormComponent $record = null) {}

    abstract public function getOptionName(): string;

    abstract public function isLayout(): bool;

    abstract public function hasChildren(): bool;

    public function getProps(): Collection
    {
        return collect($this->getRecord()->getAttribute('props'));
    }

    public function getRecord(): ?VisualFormComponent
    {
        return $this->record;
    }

    abstract public function makeComponent();

    /**
     * You can pass in an array of Tabs or simply an array of Components together with the tab label if you prefer all components to be in a single tab.
     */
    protected function extendCommonSchema(array $components = [], ?string $fieldsetLabel = null): array
    {
        $isFieldsets = false;
        foreach ($components as $item) {
            if (! ($item instanceof \Filament\Forms\Components\Fieldset)) {
                $isFieldsets = false;

                break;
            }
            $isFieldsets = true;
        }

        return [
            \Filament\Forms\Components\Fieldset::make(__('Common Details'))->schema([
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
                \Filament\Forms\Components\TextInput::make('state_path')->label(__('State Path'))
                    ->hint(__('e.g users'))
                    ->helperText(__('Leave blank to use the default state path'))
                    ->live(debounce: 500),
                \Filament\Forms\Components\Textarea::make('description')->columnSpanFull()->label(__('Description'))->default(''),
                \Filament\Forms\Components\Checkbox::make('is_active')->default(true),
            ]),
            ...($isFieldsets ? $components : [\Filament\Forms\Components\Fieldset::make($fieldsetLabel ?? 'Specific Field Details')->schema($components)]),
        ];
    }

    abstract public function getMainSchema(): array;

    abstract public function getValidationSchema(): array;

    abstract public function getColumnsSchema(): array;

    /**
     * @param  \Filament\Forms\Components\Component[]  $schema
     * @return \Filament\Forms\Components\Component[]
     */
    protected function extendValidationSchema(array $schema = []): array
    {
        $options = VisualForms::getValidationRules();

        return [
            ...$schema,
            \Filament\Forms\Components\Fieldset::make(__('Custom Validation Rules'))->schema([
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

    protected function extendColumnsSchema(array $schema = []): array
    {
        return [
            ...$schema,
            ...[
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

            ],
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

    protected function prepareColumns(): Collection
    {
        $columns = $this->getRecord()->getAttribute('columns');
        if (! $columns) {
            return collect([]);
        }

        return collect($columns)->mapWithKeys(fn ($column) => [$column['key'] => intval($column['value'])]);
    }

    protected function prepareColumnStart(): Collection
    {
        $props = collect($this->getRecord()->getAttribute('props'));
        if (Utils::getBool($props->get('column_span_full'))) {
            return collect([]);
        }
        $columns = $this->getRecord()->getAttribute('column_start');
        if (! $columns) {
            return collect([]);
        }

        return collect($columns)->mapWithKeys(fn ($column) => [$column['key'] => intval($column['value'])]);
    }

    protected function prepareColumnSpan(): Collection
    {
        $props = collect($this->getRecord()->getAttribute('props'));
        if (Utils::getBool($props->get('column_span_full'))) {
            return collect([]);
        }
        $columns = $this->getRecord()->getAttribute('column_span');
        if (! $columns) {
            return collect();
        }

        return collect($columns)->mapWithKeys(fn ($column) => [$column['key'] => intval($column['value'])]);
    }

    protected function prepareValidationRules(): Collection
    {
        $rules = $this->getRecord()->getAttribute('validation_rules');
        if (! $rules) {
            return collect();
        }

        return collect($rules)->map(fn ($rule) => $rule['value'] ? "{$rule['key']}:{$rule['value']}" : $rule['key']);
    }

    protected function makeColumns(&$control)
    {
        $record = $this->getRecord();
        // Columns
        $columns = $this->prepareColumns();
        if ($columns->isNotEmpty()) {
            $control->columns($columns->toArray());
        }

        if (Utils::getBool($record->getAttribute('column_span_full'))) {
            $control->columnSpanFull();
        } else {
            $columnStart = $this->prepareColumnStart();
            if ($columnStart->isNotEmpty()) {
                $control->columnStart($columnStart);
            }
            $colspan = $this->prepareColumnSpan();
            if ($colspan->isNotEmpty()) {
                $control->columnSpan($colspan->toArray());
            }
        }

        return $control;
    }

    protected function makeChildren(): array
    {
        $record = $this->getRecord();
        if (! $record->getAttribute('is_active')) {
            return [];
        }
        if (Utils::instantiateClass($record->getAttribute('component_type'))->hasChildren()) {
            $children = $record->children()->where('is_active', '=', true)->get();
            $schema = [];
            foreach ($children as $child) {
                $component = Utils::instantiateClass($child->getAttribute('component_type'), ['record' => $child]);
                $schema[] = $component->makeComponent();
            }

            return $schema;
        } else {
            return [];
        }
    }

    public function makeUnique(mixed &$component): mixed
    {
        if (! $this->getRecord()) {
            return $component;
        }
        if ($this->isLayout()) {
            return $component;
        }

        if ($this->getProps()->get('unique')) {
            $rule = Rule::unique(\Config::get('visual-forms.tables.visual_form_entries'), 'payload->' . $this->getRecord()->getAttribute('name'));
            if ($this->getRecord()->getAttribute('id')) {
                $rule = $rule->ignore($this->getRecord()->getAttribute('id'));
            }
            $component->rule($rule);
        }
        return $component;
    }
}
