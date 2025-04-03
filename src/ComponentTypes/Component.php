<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Config;
use Coolsam\VisualForms\Concerns\HasOptions;
use Coolsam\VisualForms\Facades\VisualForms;
use Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource;
use Coolsam\VisualForms\Models\VisualFormComponent;
use Coolsam\VisualForms\Utils;
use Exception;
use Filament\Forms;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Enums\IconSize;
use Filament\Support\RawJs;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;

abstract class Component
{
    public function __construct(private readonly ?VisualFormComponent $record = null)
    {
        // Check if it has the HasOptions trait
    }

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

    /**
     * Creates a new Component without any further arguments.
     */
    abstract public function letThereBe(string $name): Forms\Components\Component;

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
            ...($isFieldsets ? $components : $components),
        ];
    }

    public static function getFullSchema(): array
    {
        return [
            Forms\Components\Tabs::make()->schema([
                Forms\Components\Tabs\Tab::make(__('Component Type'))->schema([
                    Forms\Components\Select::make('component_type')
                        ->required()
                        ->live()
                        ->searchable()
                        ->options(VisualForms::getComponentTypeOptions()),
                    \Filament\Forms\Components\TextInput::make('name')->label(__('Name'))
                        ->hint(__('e.g star_name'))
                        ->required()
                        ->visible(fn (Get $get) => $get('component_type'))
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, Set $set, Get $get, $record) {
                            if (! $state) {
                                return;
                            }
                            $set(
                                'label',
                                str($state)->camel()->snake()->title()->replace('_', ' ')->toString()
                            );
                            $class = Utils::instantiateClass($get('component_type'));
                            if ($class && ! ($class->isLayout())) {
                                $set('state_path', $get('name'));
                            }
                            if (! $record) {
                                $set('column_span_full', false);
                                $set('is_active', true);
                                $set('column_span', [
                                    ['key' => 'default', 'value' => 1],
                                    ['key' => 'xs', 'value' => 1],
                                    ['key' => 'sm', 'value' => null],
                                    ['key' => 'md', 'value' => null],
                                    ['key' => 'lg', 'value' => null],
                                    ['key' => 'xl', 'value' => null],
                                    ['key' => '2xl', 'value' => null],
                                ]);
                                $set('columns', [
                                    ['key' => 'default', 'value' => 1],
                                    ['key' => 'xs', 'value' => null],
                                    ['key' => 'sm', 'value' => null],
                                    ['key' => 'md', 'value' => 2],
                                    ['key' => 'lg', 'value' => null],
                                    ['key' => 'xl', 'value' => null],
                                    ['key' => '2xl', 'value' => null],
                                ]);
                                $set('column_start', [
                                    ['key' => 'default', 'value' => null],
                                    ['key' => 'xs', 'value' => null],
                                    ['key' => 'sm', 'value' => null],
                                    ['key' => 'md', 'value' => null],
                                    ['key' => 'lg', 'value' => null],
                                    ['key' => 'xl', 'value' => null],
                                    ['key' => '2xl', 'value' => null],
                                ]);
                            }
                        }),
                    Forms\Components\Select::make('parent_id')->label(__('Parent Component'))
                        ->live()
                        ->searchable()
                        ->visible(fn ($record, $state) => $record?->getAttribute('id') || $state)
                        ->options(Utils::getEligibleParentComponents()->toArray()),

                    Forms\Components\Section::make(__('More details (optional)'))
                        ->collapsed()
                        ->visible(fn ($record, $state) => $record?->getAttribute('id') || $state)
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('label')->label(__('Label'))
                                ->required(fn (Get $get) => $get('name') !== null)
                                ->hint(__('e.g Star Name')),
                            \Filament\Forms\Components\TextInput::make('state_path')->label(__('State Path'))
                                ->hint(__('e.g biodata.first_name'))
                                ->helperText(__('For layouts, setting this will nest the data under that key. For inputs, the default statePath is the component\'s name. Leave blank to use the default state path. Use dots to nest data.'))
                                ->live(onBlur: true),
                            \Filament\Forms\Components\Textarea::make('description')
                                ->columnSpanFull()
                                ->label(__('Description'))->default(''),
                            \Filament\Forms\Components\ToggleButtons::make('is_active')->boolean()->default(true),
                        ]),
                ]),
                Forms\Components\Tabs\Tab::make(__('Component Details'))
                    ->statePath('props')
                    ->visible(fn (Get $get, $record) => $get('component_type') && $get('name') && $record?->getKey())
                    ->schema(fn (Forms\Get $get) => ! $get('component_type') ? [] :
                        Utils::instantiateClass($get('component_type'))->getMainSchema()),
                Forms\Components\Tabs\Tab::make(__('Configure Options'))
                    ->lazy()
                    ->visible(fn (Forms\Get $get, $record) => $record?->getKey()
                        && $get('component_type')
                        && $get('name')
                        && Utils::instantiateClass($get('component_type'))->hasOptions())
                    ->schema(fn (
                        Forms\Get $get
                    ) => $get('component_type') && Utils::instantiateClass($get('component_type'))->hasOptions() ?
                        Utils::instantiateClass($get('component_type'))->extendOptionsSchema() : []),
                Forms\Components\Tabs\Tab::make(__('Configure Columns'))
                    ->lazy()
                    ->visible(fn (Get $get, $record) => $get('component_type'))
                    ->schema(
                        fn (Forms\Get $get) => ! $get('component_type') ? [] :
                            Utils::instantiateClass($get('component_type'))->getColumnsSchema()
                    ),
                Forms\Components\Tabs\Tab::make(__('Validation Rules'))
                    ->lazy()
                    ->visible(fn (Get $get, $record) => filled($get('component_type'))
                        && filled($get('name')) && $record?->getKey() && ! Utils::instantiateClass($get('component_type'))->isLayout())
                    ->schema(fn (Forms\Get $get) => ! $get('component_type') ? [] :
                        Utils::instantiateClass($get('component_type'))->getValidationSchema()),
            ])
                ->activeTab(1)
                ->columnSpanFull(),
        ];
    }

    public function getSpecificBasicSchema(): array
    {
        return [
            Forms\Components\Fieldset::make(__('Basic Details'))
                ->columns(['md' => 2, 'xl' => 3])
                ->visible(fn (Get $get) => filled($get('../component_type')) && filled($get('../name')))
                ->schema(function (Get $get) {
                    $component = Utils::instantiateClass($get('../component_type'))?->letThereBe($get('../name'));

                    return [
                        Forms\Components\TextInput::make('placeholder')->label(__('Placeholder'))->visible(fn (
                        ) => method_exists($component, 'placeholder')),
                        Forms\Components\TextInput::make('hint')->label(__('Hint'))->visible(fn (
                        ) => method_exists($component, 'hint')),
                        Forms\Components\Select::make('hintIcon')->label(__('Hint icon'))->options(Utils::getHeroicons())->searchable()->visible(fn (
                        ) => method_exists($component, 'hintIcon')),
                        Forms\Components\Textarea::make('helperText')->columnSpanFull()->label(__('Helper Text'))->visible(fn (
                        ) => method_exists($component, 'helperText')),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull()
                            ->formatStateUsing(fn (
                                $record
                            ) => $record?->getAttribute('props.description') ?? $record?->getAttribute('description'))
                            ->live(onBlur: true)->afterStateUpdated(fn ($state, Set $set) => $set(
                                '../description',
                                $state
                            ))
                            ->label(__('Description'))->visible(fn () => method_exists($component, 'description')),

                        \Filament\Forms\Components\Select::make('icon')->label(__('Icon'))
                            ->visible(fn () => method_exists($component, 'icon'))
                            ->live()->options(Utils::getHeroicons())->searchable(),
                        \Filament\Forms\Components\ToggleButtons::make('iconSize')->boolean()->inline()->label(__('Icon Size'))->options([
                            IconSize::Small->value => __('Small'),
                            IconSize::Medium->value => __('Medium'),
                            IconSize::Large->value => __('Large'),
                        ])->default(IconSize::Medium->value)->inline()->visible(fn (
                            Get $get
                        ) => $get('icon') && method_exists($component, 'iconSize')),
                        \Filament\Forms\Components\ToggleButtons::make('collapsible')->boolean()->inline()->label(__('Collapsible'))->live()->default(false)->visible(fn (
                        ) => method_exists($component, 'collapsible')),
                        \Filament\Forms\Components\ToggleButtons::make('collapsed')->boolean()->inline()->label(__('Collapsed'))->live()->default(false)->visible(fn (
                            Get $get
                        ) => $get('collapsible') && method_exists($component, 'collapsed')),

                        \Filament\Forms\Components\Textarea::make('mask')->label(__('Mask'))
                            ->live()->visible(fn () => method_exists($component, 'mask'))
                            ->rows(1)->grow()
                            ->placeholder('a RawJs expression e.g $money'),
                        Forms\Components\TagsInput::make('stripCharacters')->label(__('Strip Characters'))
                            ->visible(fn () => method_exists($component, 'stripCharacters'))
                            ->hint('press ENTER to insert a new tag after typing')
                            ->helperText('This removes the listed characters from the input before saving. Useful especially when inputting formatted money values e.g 2,000.0')
                            ->placeholder(__('press enter to insert a new tag after typing'))->columnSpanFull(),

                        // Date props
                        Forms\Components\TagsInput::make('disabledDates')->label(__('Disabled Dates'))
                            ->visible(fn () => method_exists($component, 'disabledDates'))
                            ->helperText('Each of the dates must be in the format YYYY-MM-DD')
                            ->placeholder(__('press enter to insert a new tag after typing'))->columnSpanFull(),

                        Forms\Components\TextInput::make('format')->label(__('Date Format'))
                            ->visible(fn () => method_exists($component, 'format'))
                            ->helperText(__('Date format to be used for the date picker'))
                            ->default('Y-m-d')
                            ->placeholder(__('e.g d/m/Y')),
                        Forms\Components\TextInput::make('displayFormat')->label(__('Date Display Format'))
                            ->visible(fn () => method_exists($component, 'format'))
                            ->helperText(__('Date format to be used for the date picker'))
                            ->placeholder(__('e.g d F Y')),

                        Forms\Components\TextInput::make('timezone')
                            ->label(__('Timezone'))
                            ->visible(fn () => method_exists($component, 'timezone'))
                            ->helperText(__('Timezone to be used for the date picker'))
                            ->placeholder(__('e.g Africa/Nairobi')),

                        Forms\Components\TextInput::make('locale')
                            ->label(__('Locale'))
                            ->visible(fn () => method_exists($component, 'locale'))
                            ->helperText(__('Locale to be used for the date picker'))
                            ->placeholder(__('e.g en')),

                        Forms\Components\ToggleButtons::make('seconds')->label(__('Enable Seconds'))
                            ->default(false)
                            ->live()
                            ->inline()->boolean()
                            ->visible(fn () => method_exists($component, 'seconds'))
                            ->helperText(__('Enable seconds for the time picker')),
                        Forms\Components\ToggleButtons::make('closeOnDateSelection')->default(true)
                            ->label(__('Close on Date Selection'))
                            ->boolean()->inline()
                            ->visible(fn () => method_exists($component, 'closeOnDateSelection')),

                        Forms\Components\TextInput::make('hoursStep')
                            ->label(__('Hours Step'))
                            ->numeric()->integer()
                            ->visible(fn () => method_exists($component, 'hoursStep'))
                            ->helperText(__('Hours step to be used for the time picker'))
                            ->placeholder(__('e.g 1')),
                        Forms\Components\TextInput::make('minutesStep')
                            ->label(__('Minutes Step'))
                            ->numeric()->integer()
                            ->visible(fn () => method_exists($component, 'minutesStep'))
                            ->helperText(__('Minutes step to be used for the time picker'))
                            ->placeholder(__('e.g 1')),

                        Forms\Components\TextInput::make('secondsStep')
                            ->label(__('Seconds Step'))
                            ->numeric()->integer()
                            ->visible(fn (Get $get) => $get('seconds') && method_exists($component, 'secondsStep'))
                            ->helperText(__('Seconds step to be used for the time picker'))
                            ->placeholder(__('e.g 1')),

                        Forms\Components\ToggleButtons::make('firstDayOfWeek')->label(__('First Day of the Week'))
                            ->options([
                                1 => __('Monday'),
                                2 => __('Tuesday'),
                                3 => __('Wednesday'),
                                4 => __('Thursday'),
                                5 => __('Friday'),
                                6 => __('Saturday'),
                                7 => __('Sunday'),
                            ])
                            ->default(1)
                            ->columnSpanFull()
                            ->inline()
                            ->visible(fn () => method_exists($component, 'firstDayOfWeek')),
                        Forms\Components\TagsInput::make('datalist')->label(__('Datalist'))
                            ->visible(fn () => method_exists($component, 'datalist'))
                            ->helperText(__('Datalist to be used for the input'))
                            ->placeholder(__('press enter to insert a new tag after typing'))
                            ->columnSpanFull(),
                        Forms\Components\ToggleButtons::make('multiple')->boolean()->inline()
                            ->label(__('Multiple'))
                            ->live()
                            ->default(false)
                            ->visible(fn () => method_exists($component, 'multiple')),
                        \Filament\Forms\Components\ToggleButtons::make('disabled')
                            ->boolean()->inline()
                            ->label(__('Disabled'))->default(false)->visible(fn (
                            ) => method_exists($component, 'disabled')),
                        // Flags
                        Forms\Components\ToggleButtons::make('autocapitalize')->boolean()->inline()->default(false)->label(__('Autocapitalize'))->visible(fn (
                        ) => method_exists($component, 'autocapitalize')),
                        Forms\Components\ToggleButtons::make('autocomplete')->boolean()->inline()->default(false)->label(__('Autocomplete'))->visible(fn (
                        ) => method_exists($component, 'autocomplete')),
                        Forms\Components\ToggleButtons::make('inline')->inline()->boolean()->default(false)->visible(fn (
                        ) => method_exists($component, 'inline')),
                        Forms\Components\ToggleButtons::make('inlineLabel')->inline()->boolean()->default(false)->visible(fn (
                        ) => method_exists($component, 'inlineLabel')),
                        Forms\Components\ToggleButtons::make('hiddenLabel')->inline()->boolean()->default(false)->label(__('Hidden Label'))
                            ->visible(fn () => method_exists($component, 'hiddenLabel')),
                        Forms\Components\ToggleButtons::make('searchable')->inline()->boolean()
                            ->default(true)
                            ->label(__('Searchable'))
                            ->visible(fn () => method_exists($component, 'searchable')),
                        Forms\Components\ToggleButtons::make('native')
                            ->inline()
                            ->default(true)
                            ->boolean()
                            ->visible(fn () => method_exists($component, 'native'))
                            ->label(__('Native')),
                    ];
                }),
        ];
    }

    abstract public function getSpecificValidationSchema(): array;

    public function getMainSchema(): array
    {
        return $this->extendCommonSchema(
            [
                ...$this->getSpecificBasicSchema(),
                ...$this->affixesSchema(),
            ]
        );
    }

    public function getValidationSchema(): array
    {
        return $this->extendValidationSchema([
            \Filament\Forms\Components\Fieldset::make(__($this->getOptionName() . ' Validation'))
                ->schema($this->getSpecificValidationSchema()),
        ]);
    }

    public function getColumnsSchema(): array
    {
        return $this->extendColumnsSchema();
    }

    /**
     * @param  Forms\Components\Component[]  $schema
     * @return Forms\Components\Component[]
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
                    ->headers([
                        Header::make('key')->label(__('Screen Size')),
                        Header::make('value')->label(__('Value')),
                    ])->schema([
                        \Filament\Forms\Components\Select::make('key')
                            ->options([
                                'default' => __('Default'),
                                'xs' => __('Extra Small'),
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
                                'xs' => __('Extra Small'),
                                'sm' => __('Small'),
                                'md' => __('Medium'),
                                'lg' => __('Large'),
                                'xl' => __('Extra Large'),
                                '2xl' => __('2x Extra Large'),
                            ]),
                        \Filament\Forms\Components\TextInput::make('value')
                            ->integer()
                            ->type('number')
                            ->minValue(1)
                            ->maxValue(12),
                    ])
                    ->deletable(false)
                    ->addable(false)
                    ->columnSpanFull()
                    ->label(__('Column Start'))
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
                                'xs' => __('Extra Small'),
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
        $rules = collect($this->getRecord()->getAttribute('validation_rules') ?? []);

        if ($rules->isEmpty()) {
            return [];
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

        return collect($columns)->mapWithKeys(fn ($column) => [$column['key'] => intval($column['value'])])->filter();
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

        return collect($columns)->mapWithKeys(fn ($column) => [$column['key'] => intval($column['value'])])->filter();
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

        return collect($columns)->mapWithKeys(fn ($column) => [$column['key'] => intval($column['value'])])->filter();
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

    /**
     * @throws Exception
     */
    public function makeComponent(bool $editable = false): Forms\Components\Component
    {
        if (! $record = $this->getRecord()) {
            throw new Exception('Record not found');
        }

        if ($this->isLayout()) {
            $keyAttrib = 'label';
        } else {
            $keyAttrib = 'name';
        }
        $component = $this->letThereBe($record->getAttribute($keyAttrib));

        $this->configureComponent($component, $editable);

        if ($this->hasChildren()) {
            if (method_exists($component, 'schema')) {
                $component->schema($this->makeChildren($editable));
            } elseif (method_exists($component, 'steps')) {
                $component->steps($this->makeChildren($editable));
            }
        }

        return $component;
    }

    public function makeValidation(&$component): void
    {
        $record = $this->getRecord();
        if (! $record) {
            return;
        }
        if ($this->isLayout()) {
            return;
        }

        $this->makeUnique($component);
        $rules = $this->makeRules();

        $props = $this->getProps();
        if (filled($props)) {
            if (method_exists($component, 'required')) {
                $component->required(Utils::getBool($props->get('required')));
            }
            if (method_exists($component, 'accepted')) {
                $component->accepted(Utils::getBool($props->get('accepted')));
            }
            if (method_exists($component, 'declined')) {
                $component->declined(Utils::getBool($props->get('declined')));
            }
            if (method_exists($component, 'disabled')) {
                $component->disabled(Utils::getBool($props->get('disabled')));
            }

            if (method_exists($component, 'readOnly')) {
                $component->readOnly(Utils::getBool($props->get('readOnly')));
            }

            if (method_exists($component, 'email')) {
                $component->email(Utils::getBool($props->get('email')));
            }

            if (method_exists($component, 'tel')) {
                $component->tel(Utils::getBool($props->get('tel')));
            }

            if (method_exists($component, 'url')) {
                $component->url(Utils::getBool($props->get('url')));
            }

            if (method_exists($component, 'integer')) {
                $component->integer(Utils::getBool($props->get('integer')));
            }

            if (method_exists($component, 'numeric')) {
                $component->numeric(Utils::getBool($props->get('numeric')));
            }

            if (method_exists($component, 'password')) {
                $component->password(Utils::getBool($props->get('password')));
            }

            if (filled($props->get('revealable')) && method_exists($component, 'revealable')) {
                $component->revealable(Utils::getBool($props->get('revealable')));
            }

            if (filled($props->get('length')) && method_exists($component, 'length')) {
                $component->length(intval($props->get('length')));
            }
            if (filled($props->get('maxLength')) && method_exists($component, 'maxLength')) {
                $component->maxLength(intval($props->get('maxLength')));
            }

            if (filled($props->get('minLength')) && method_exists($component, 'minLength')) {
                $component->minLength(intval($props->get('minLength')));
            }

            if (filled($props->get('maxValue')) && method_exists($component, 'maxValue')) {
                $component->maxValue($props->get('maxValue'));
            }

            if (filled($props->get('minValue')) && method_exists($component, 'minValue')) {
                $component->minValue($props->get('minValue'));
            }

            if (filled($props->get('same')) && method_exists($component, 'same')) {
                $component->same($props->get('same'));
            }

            if (filled($props->get('different')) && method_exists($component, 'different')) {
                $component->different($props->get('different'));
            }

            if (filled($props->get('lt')) && method_exists($component, 'lt')) {
                $component->lt($props->get('lt'));
            }

            if (filled($props->get('lte')) && method_exists($component, 'lte')) {
                $component->lte($props->get('lte'));
            }

            if (filled($props->get('gt')) && method_exists($component, 'gt')) {
                $component->gt($props->get('gt'));
            }

            if (filled($props->get('gte')) && method_exists($component, 'gte')) {
                $component->gte($props->get('gte'));
            }

            if (filled($props->get('mask')) && method_exists($component, 'mask')) {
                $mask = str($props->get('mask'))->startsWith('$') ? RawJs::make($props->get('mask')) : $props->get('mask');
                $component->mask($mask);
            }

            if (filled($props->get('stripCharacters')) && method_exists($component, 'stripCharacters')) {
                $component->stripCharacters($props->get('stripCharacters'));
            }

            if ($props->get('minDate') && method_exists($component, 'minDate')) {
                $component->minDate($props->get('minDate'));
            }

            if ($props->get('maxDate') && method_exists($component, 'maxDate')) {
                $component->maxDate($props->get('maxDate'));
            }
        }

        if (filled($rules)) {
            $component->rules($rules);
        }

    }

    protected function makeStatePath(&$component): void
    {
        $record = $this->getRecord();
        if ($record->getAttribute('state_path')) {
            if (method_exists($component, 'statePath')) {
                $component->statePath($record->getAttribute('state_path'));
            }
        }
    }

    protected function makeChildren(bool $editable = false): array
    {
        $record = $this->getRecord();
        if (! $record->getAttribute('is_active')) {
            return [];
        }
        if (Utils::instantiateClass($record->getAttribute('component_type'))->hasChildren()) {
            $children = $record->children()
                ->where('is_active', '=', true)
                ->orderBy('sort_order')
                ->orderBy('created_at')
                ->get();
            $schema = [];
            foreach ($children as $child) {
                $component = Utils::instantiateClass($child->getAttribute('component_type'), ['record' => $child]);
                $schema[] = $component->makeComponent($editable);
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
            $rule = Rule::unique(
                Config::get('visual-forms.tables.visual_form_entries'),
                'payload->' . $this->getRecord()->getAttribute('name')
            );
            if ($this->getRecord()->getAttribute('id')) {
                $rule = $rule->ignore($this->getRecord()->getAttribute('id'));
            }
            $component->rule($rule);
        }

        return $component;
    }

    public function makeAffixes(mixed &$component): mixed
    {
        if (! $this->getRecord()) {
            return $component;
        }

        if ($this->isLayout()) {
            return $component;
        }

        $props = $this->getProps();
        if ($props->get('prefixIcon') && method_exists($component, 'prefixIcon')) {
            $component->prefixIcon($props->get('prefixIcon'));
            if ($props->get('prefixIconColor') && method_exists($component, 'prefixIconColor')) {
                $component->prefixIconColor($props->get('prefixIconColor'));
            }
        }
        if ($props->get('prefix') && method_exists($component, 'prefix')) {
            $component->prefix($props->get('prefix'));
        }

        if ($props->get('suffixIcon') && method_exists($component, 'suffixIcon')) {
            $component->suffixIcon($props->get('suffixIcon'));
            if ($props->get('suffixIconColor') && method_exists($component, 'suffixIconColor')) {
                $component->suffixIconColor($props->get('suffixIconColor'));
            }
        }

        if ($props->get('suffix') && method_exists($component, 'suffix')) {
            $component->suffix($props->get('suffix'));
        }

        if (method_exists($component, 'inlineSuffix')) {
            $component->inlineSuffix(Utils::getBool($props->get('inlineSuffix')));
        }

        if (method_exists($component, 'inlinePrefix')) {
            $component->inlinePrefix(Utils::getBool($props->get('inlinePrefix')));
        }

        if (method_exists($component, 'native')) {
            $component->native(Utils::getBool($props->get('native')));
        }

        return $component;
    }

    public function hasOptions(): bool
    {
        return Utils::classHasTrait($this, HasOptions::class);
    }

    protected function affixesSchema(): array
    {
        return [
            \Filament\Forms\Components\Placeholder::make('affixes')
                ->label(new HtmlString("<h3 class='font-black text-md'>" . __('Affixes') . '</h3>'))->columnSpanFull(),
            \Filament\Forms\Components\Fieldset::make(__('Prefix'))
                ->visible(fn (Get $get) => $get('../component_type') && $get('../name'))
                ->columns([
                    'md' => 2, 'lg' => 3, 'xl' => 4,
                ])->schema(function (Get $get) {
                    if (! $get('../component_type')) {
                        return [];
                    }
                    if (! $get('../name')) {
                        return [];
                    }
                    $component = Utils::instantiateClass($get('../component_type'))?->letThereBe($get('../name'));
                    if (! $component) {
                        return [];
                    }

                    return [
                        \Filament\Forms\Components\TextInput::make('prefix')->label(__('Prefix'))->live(onBlur: true)->visible(fn (
                        ) => method_exists($component, 'prefix')),
                        \Filament\Forms\Components\Select::make('prefixIcon')->label(__('Prefix Icon'))
                            ->visible(fn () => method_exists($component, 'prefixIcon'))
                            ->live()->options(Utils::getHeroicons())->searchable(),
                        \Filament\Forms\Components\ToggleButtons::make('inlinePrefix')->boolean()
                            ->visible(fn () => method_exists($component, 'inlinePrefix'))
                            ->inline()->label(__('Inline Prefix'))->default(false),
                        \Filament\Forms\Components\Select::make('prefixIconColor')->label(__('Prefix Icon Color'))
                            ->live()->visible(fn ($get) => $get('prefixIcon') && method_exists(
                                $component,
                                'prefixIconColor'
                            ))
                            ->options(Utils::getAppColors())->native(false),
                    ];
                }),

            \Filament\Forms\Components\Fieldset::make(__('Suffix'))
                ->columns([
                    'md' => 2, 'lg' => 3, 'xl' => 4,
                ])
                ->schema(function (Get $get) {
                    if (! $get('../component_type')) {
                        return [];
                    }

                    if (! $get('../name')) {
                        return [];
                    }

                    $component = Utils::instantiateClass($get('../component_type'))?->letThereBe($get('../name'));
                    if (! $component) {
                        return [];
                    }

                    return [
                        \Filament\Forms\Components\TextInput::make('suffix')->label(__('Suffix'))->live(onBlur: true)->visible(fn (
                        ) => method_exists($component, 'suffix')),
                        \Filament\Forms\Components\Select::make('suffixIcon')->label(__('Suffix Icon'))->visible(fn (
                        ) => method_exists($component, 'suffixIcon'))
                            ->live()->options(Utils::getHeroicons())->searchable(),
                        \Filament\Forms\Components\ToggleButtons::make('inlineSuffix')
                            ->visible(fn () => method_exists(
                                $component,
                                'inlineSuffix'
                            ))->boolean()->inline()->label(__('Inline Suffix'))->default(false),
                        \Filament\Forms\Components\Select::make('suffixIconColor')->label(__('Suffix Icon Color'))
                            ->live()->visible(fn ($get) => $get('suffixIcon') && method_exists($component, 'suffixIcon'))
                            ->options(Utils::getAppColors())->native(false),
                    ];
                }),
        ];
    }

    public function makeEditableAction(&$component, bool $editable): void
    {
        $record = $this->getRecord();

        $createAction = Action::make('create_field')
            ->label(__('Add a Component'))
            ->icon('heroicon-o-plus')
            ->iconButton()
            ->size('xs')
            ->color('success')
            ->extraAttributes(['class' => 'static'])
            ->authorize('create', Config::get('visual-forms.models.visual_form_component'))
            ->form(fn (Form $form) => $form
                ->model(VisualFormComponent::class)
                ->schema(VisualFormComponentResource::getSchema()))
            ->mountUsing(fn (ComponentContainer $form) => $form->fill([
                'parent_id' => $record->getKey(),
            ]))
            ->action(function (array $data) use ($record) {
                $data['form_id'] = $record->getKey();
                $record->createChild($data);

                return $record;
            });

        $editAction = Action::make('edit_field')->label(__('Edit Component'))
            ->icon('heroicon-o-pencil-square')
            ->iconButton()
            ->authorize('update', $record)
            ->color('warning')
            ->size('xs')
            ->extraAttributes(['class' => 'static'])
            ->slideOver()
            ->modalWidth('container')
            ->form(fn (Form $form) => $form
                ->model($record)
                ->schema(VisualFormComponentResource::getSchema()))
            ->mountUsing(fn (ComponentContainer $form) => $form->fill($record->toArray()))
            ->action(function (array $data) use ($record) {
                $record->update($data);
            });

        $deleteAction = Action::make('action_delete_component')->label(__('Delete Component'))
            ->icon('heroicon-o-trash')
            ->iconButton()
            ->authorize('delete', $record)
            ->color('danger')
            ->size('xs')
            ->extraAttributes(['class' => 'static'])
            ->requiresConfirmation()
            ->modalContent(fn () => new HtmlString(__('This will delete this component and all its children if any.')))
            ->action(function () use ($record) {
                $record->deleteOrFail();
            });

        $upAction = Action::make('sort')->label(__('Move Up'))
            ->icon('heroicon-o-chevron-up')
            ->size('xs')
            ->outlined()
            ->color('primary')
            ->action(function (array $data) use ($record) {
                $record->moveOrderUp();
            });
        $downAction = Action::make('action_move_down')->label(__('Move Down'))
            ->icon('heroicon-o-chevron-down')
            ->color('primary')
            ->size('xs')
            ->outlined()
            ->action(function (array $data) use ($record) {
                $record->moveOrderDown();
            });
        $startAction = Action::make('action_move_to_start')->label(__('Move to Start'))
            ->icon('heroicon-o-chevron-double-up')
            ->color('primary')
            ->size('xs')
            ->outlined()
            ->action(function (array $data) use ($record) {
                $record->moveToStart();
            });
        $endAction = Action::make('action_move_to_end')->label(__('Move to End'))
            ->icon('heroicon-o-chevron-double-down')
            ->color('primary')
            ->size('xs')
            ->outlined()
            ->action(function (array $data) use ($record) {
                $record->moveToEnd();
            });

        $sortAction = Action::make('sort')->label(__('Sort'))
            ->icon('heroicon-o-arrows-up-down')
            ->outlined()
            ->size('xs')
            ->color('gray')
            ->modalWidth('lg')
            ->form([
                Actions::make([
                    $upAction,
                    $downAction,
                    $startAction,
                    $endAction,
                ])
                    ->columnSpanFull()
                    ->columns(1),
            ])
            ->modalFooterActions()
            ->modalSubmitAction(false);
        if ($editable) {
            $actions = [
                $editAction,
                $deleteAction,
                $sortAction,
            ];
            /**
             * @var Component $componentType
             */
            $componentType = Utils::instantiateClass($record->getAttribute('component_type'));
            if ($componentType->hasChildren()) {
                $actions = [
                    $createAction,
                    $editAction,
                    $deleteAction,
                    $sortAction,
                ];
            }
            if (method_exists($component, 'hintActions')) {
                $component->hintActions($actions);
            } elseif (method_exists($component, 'headerActions')) {
                $component->headerActions($actions);
            }

            if (method_exists($component, 'extraFieldWrapperAttributes')) {
                $component->extraFieldWrapperAttributes(['class' => 'border-dashed border-primary border-2 border-gray-300 rounded-md p-2']);
            } elseif (method_exists($component, 'extraAttributes')) {
                $component->extraAttributes(['class' => 'border-dashed border-primary border-2 border-gray-300 rounded-md p-2']);
            }
        }
    }

    public function configureComponent(&$component, bool $editable): void
    {
        $record = $this->getRecord();
        $props = $this->getProps();
        if (method_exists($component, 'label')) {
            if (filled($record->getAttribute('label'))) {
                $component->label($record->getAttribute('label'));
            }
        }
        if (method_exists($component, 'description')) {
            if (filled($record->getAttribute('description'))) {
                $component->description($record->getAttribute('description'));
            }

            if (method_exists($component, 'inlineLabel')) {
                $component->inlineLabel(Utils::getBool($props->get('inlineLabel')));
            }

            if (method_exists($component, 'hiddenLabel')) {
                $component->hiddenLabel(Utils::getBool($props->get('hiddenLabel')));
            }
        }
        if (method_exists($component, 'icon')) {
            if ($props->get('icon')) {
                $component->icon($props->get('icon'));
            }
            if (method_exists($component, 'iconSize')) {
                $component->iconSize($props->get('iconSize') ?? IconSize::Medium->value);
            }
        }

        if (method_exists($component, 'collapsible')) {
            $component->collapsible(Utils::getBool($props->get('collapsible')));
        }
        if (method_exists($component, 'collapsed')) {
            $component->collapsed(Utils::getBool($props->get('collapsed')));
        }

        if (method_exists($component, 'disabled')) {
            $component->disabled(Utils::getBool($props->get('disabled')));
        }

        if (method_exists($component, 'readOnly')) {
            $component->readOnly(Utils::getBool($props->get('readOnly')));
        }

        if (($placeholder = $props->get('placeholder')) && method_exists($component, 'placeholder')) {
            $component->placeholder($placeholder);
        }

        $helper = $props->get('helperText');
        if ($helper && method_exists($component, 'helperText')) {
            $component->helperText($helper);
        }

        if ($props->get('hint') && method_exists($component, 'hint')) {
            $component->hint($props->get('hint'));
        }

        if ($props->get('hintIcon') && method_exists($component, 'hintIcon')) {
            $component->hintIcon($props->get('hintIcon'));
        }

        $this->makeColumns($component);
        $this->makeAffixes($component);
        $this->makeUnique($component);
        $this->makeStatePath($component);

        if (! $this->isLayout()) {
            if ($props->get('default') !== null) {
                $component->default($props->get('default'));
            }

            if (Utils::getBool($props->get('searchable'))) {
                // check if component has searchable method
                if (method_exists($component, 'searchable')) {
                    $component->searchable();
                }
            }

            if (method_exists($component, 'boolean') && Utils::getBool($props->get('boolean'))) {
                $component->boolean();
                if ($props->get('default') !== null) {
                    if (method_exists($component, 'default')) {
                        $component->default(Utils::getBool($props->get('default')));
                    }
                }
            }

            if (Utils::getBool($props->get('inline'))) {
                if (method_exists($component, 'inline')) {
                    $component->inline();
                }
            }
            if (Utils::getBool($props->get('inlineLabel'))) {
                $component->inlineLabel();
            }

            if (filled($props->get('multiple')) && method_exists($component, 'multiple')) {
                $component->multiple(Utils::getBool($props->get('multiple')));
            }

            $this->makeValidation($component);
        }

        $this->makeEditableAction($component, $editable);
    }
}
