<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Coolsam\VisualForms\Concerns\HasOptions;
use Coolsam\VisualForms\Facades\VisualForms;
use Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource;
use Coolsam\VisualForms\Models\VisualFormComponent;
use Coolsam\VisualForms\Utils;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
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

    abstract public function makeComponent(bool $editable = false);

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
            \Filament\Forms\Components\Section::make(__('Common Details'))
                ->collapsible()
                ->schema([
                    \Filament\Forms\Components\TextInput::make('name')->label(__('Name'))
                        ->hint(__('e.g first_name'))
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                            $set(
                                'label',
                                str($state)->camel()->snake()->title()->replace('_', ' ')->toString()
                            );
                            $class = Utils::instantiateClass($get('component_type'));
                            if ($class && ! ($class->isLayout())) {
                                $set('state_path', $get('name'));
                            }
                            if (! $this->getRecord()) {
                                $set('column_span_full', false);
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
                    \Filament\Forms\Components\TextInput::make('label')->label(__('Label'))
                        ->required(fn (Get $get) => $get('first_name') !== null)
                        ->hint(__('e.g First Name')),
                    \Filament\Forms\Components\TextInput::make('state_path')->label(__('State Path'))
                        ->hint(__('e.g biodata.first_name'))
                        ->helperText(__('For layouts, setting this will nest the data under that key. For inputs, the default statePath is the component\'s name. Leave blank to use the default state path. Use dots to nest data.'))
                        ->live(onBlur: true),
                    \Filament\Forms\Components\Textarea::make('description')->columnSpanFull()->label(__('Description'))->default(''),
                    \Filament\Forms\Components\Checkbox::make('is_active')->default(true),
                ]),
            ...($isFieldsets ? $components : [\Filament\Forms\Components\Section::make($fieldsetLabel ?? 'Specific Field Details')->collapsible()->schema($components)]),
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
                \Config::get('visual-forms.tables.visual_form_entries'),
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
        if ($props->get('prefixIcon')) {
            $component->prefixIcon($props->get('prefixIcon'));
            if ($props->get('prefixIconColor')) {
                $component->prefixIconColor($props->get('prefixIconColor'));
            }
        }
        if ($props->get('prefix')) {
            $component->prefix($props->get('prefix'));
        }

        if ($props->get('suffixIcon')) {
            $component->suffixIcon($props->get('suffixIcon'));
            if ($props->get('suffixIconColor')) {
                $component->suffixIconColor($props->get('suffixIconColor'));
            }
        }

        if ($props->get('suffix')) {
            $component->suffix($props->get('suffix'));
        }

        if ($props->get('inlineSuffix')) {
            $component->inlineSuffix(Utils::getBool($props->get('inlineSuffix')));
        }

        if ($props->get('inlinePrefix')) {
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
                ->statePath('props')
                ->columns([
                    'md' => 2, 'lg' => 3, 'xl' => 4,
                ])->schema([
                    \Filament\Forms\Components\TextInput::make('prefix')->label(__('Prefix'))->live(onBlur: true),
                    \Filament\Forms\Components\Select::make('prefixIcon')->label(__('Prefix Icon'))->live(onBlur: true)->options(Utils::getHeroicons())->searchable(),
                    \Filament\Forms\Components\ToggleButtons::make('inlinePrefix')->boolean()->inline()->label(__('Inline Prefix'))->default(false),
                    \Filament\Forms\Components\Select::make('prefixIconColor')->label(__('Prefix Icon Color'))
                        ->live()->visible(fn ($get) => $get('prefixIcon'))->options(Utils::getAppColors())->native(false),
                ]),

            \Filament\Forms\Components\Fieldset::make(__('Suffix'))->columns([
                'md' => 2, 'lg' => 3, 'xl' => 4,
            ])
                ->statePath('props')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('suffix')->label(__('Suffix'))->live(onBlur: true),
                    \Filament\Forms\Components\Select::make('suffixIcon')->label(__('Suffix Icon'))->live()->options(Utils::getHeroicons())->searchable(),
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

        $createAction = Action::make('create_field')->label(__('Add a Component'))
            ->icon('heroicon-o-plus-circle')
            ->iconButton()
            ->size('xs')
            ->color('success')
            ->extraAttributes(['class' => 'static'])
            ->authorize('create', \Config::get('visual-forms.models.visual_form_component'))
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
            ->modalContent(fn() => new HtmlString(__('This will delete this component and all its children if any.')))
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
            ])->modalSubmitAction(false);
        if ($editable) {
            $actions = [
                $editAction,
                $deleteAction,
                $sortAction
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
            if (method_exists($component, 'hintAction')) {
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
}
