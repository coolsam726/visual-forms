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
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, Set $set, Get $get, $record) {
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
                    ->schema(fn (Forms\Get $get) => ! $get('component_type') ? [] :
                        Utils::instantiateClass($get('component_type'))->getMainSchema()),
                Forms\Components\Tabs\Tab::make(__('Configure Options'))
                    ->lazy()
                    ->visible(fn (Forms\Get $get) => $get('component_type') && Utils::instantiateClass($get('component_type'))->hasOptions())
                    ->schema(fn (Forms\Get $get) => $get('component_type') && Utils::instantiateClass($get('component_type'))->hasOptions() ?
                        Utils::instantiateClass($get('component_type'))->extendOptionsSchema() : []),
                Forms\Components\Tabs\Tab::make(__('Configure Columns'))
                    ->lazy()
                    ->schema(
                        fn (Forms\Get $get) => ! $get('component_type') ? [] :
                            Utils::instantiateClass($get('component_type'))->getColumnsSchema()
                    ),
                Forms\Components\Tabs\Tab::make(__('Validation Rules'))
                    ->lazy()
                    ->schema(fn (Forms\Get $get) => ! $get('component_type') ? [] :
                        Utils::instantiateClass($get('component_type'))->getValidationSchema())
                    ->visible(fn (
                        Forms\Get $get
                    ) => $get('component_type') && ! Utils::instantiateClass($get('component_type'))->isLayout()),
            ])
                ->activeTab(1)
                ->columnSpanFull(),
        ];
    }

    abstract public function getSpecificBasicSchema(): array;

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
                ->statePath('props')
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
        if (! $this->getRecord()) {
            return;
        }
        if ($this->isLayout()) {
            return;
        }
        $rules = $this->makeRules();
        if (count($rules)) {
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

        if (method_exists($component, 'inlineSuffixIcon')) {
            $component->inlinePrefix(Utils::getBool($props->get('inlinePrefix')));
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
            \Filament\Forms\Components\Placeholder::make('affixes')->label(new HtmlString("<h3 class='font-black text-md'>" . __('Affixes') . '</h3>'))->columnSpanFull(),
            \Filament\Forms\Components\Fieldset::make(__('Prefix'))
                ->columns([
                    'md' => 2, 'lg' => 3, 'xl' => 4,
                ])->schema([
                    \Filament\Forms\Components\TextInput::make('prefix')->label(__('Prefix'))->live(onBlur: true),
                    \Filament\Forms\Components\Select::make('prefixIcon')->label(__('Prefix Icon'))
                        ->live()->options(Utils::getHeroicons())->searchable(),
                    \Filament\Forms\Components\ToggleButtons::make('inlinePrefix')->boolean()
                        ->inline()->label(__('Inline Prefix'))->default(false),
                    \Filament\Forms\Components\Select::make('prefixIconColor')->label(__('Prefix Icon Color'))
                        ->live()->visible(fn ($get) => $get('prefixIcon'))->options(Utils::getAppColors())->native(false),
                ]),

            \Filament\Forms\Components\Fieldset::make(__('Suffix'))->columns([
                'md' => 2, 'lg' => 3, 'xl' => 4,
            ])
                ->schema([
                    \Filament\Forms\Components\TextInput::make('suffix')->label(__('Suffix'))->live(onBlur: true),
                    \Filament\Forms\Components\Select::make('suffixIcon')->label(__('Suffix Icon'))
                        ->live()->options(Utils::getHeroicons())->searchable(),
                    \Filament\Forms\Components\ToggleButtons::make('inlineSuffix')->boolean()->inline()->label(__('Inline Suffix'))->default(false),
                    \Filament\Forms\Components\Select::make('suffixIconColor')->label(__('Suffix Icon Color'))
                        ->live()->visible(fn ($get) => $get('suffixIcon'))
                        ->options(Utils::getAppColors())->native(false),
                ]),
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

            if (Utils::getBool($props->get('required'))) {
                $component->required();
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

            if (Utils::getBool($props->get('unique'))) {
                $component->unique();
            }

            if (Utils::getBool($props->get('inline'))) {
                if (method_exists($component, 'inline')) {
                    $component->inline();
                }
            }
            if (Utils::getBool($props->get('inlineLabel'))) {
                $component->inlineLabel();
            }

            $this->makeValidation($component);
        }

        $this->makeEditableAction($component, $editable);
    }
}
