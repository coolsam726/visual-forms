<?php

namespace Coolsam\VisualForms\Filament\Resources\VisualFormResource\RelationManagers;

use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Coolsam\VisualForms\ControlTypes;
use Coolsam\VisualForms\Facades\VisualForms;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class FieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'fields';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set(
                        'label',
                        str($state)->camel()->snake()->title()->explode('_')->join(' ')
                    )),
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->datalist([
                        'text',
                        'email',
                        'password',
                        'number',
                        'tel',
                        'url',
                        'date',
                        'time',
                        'datetime-local',
                        'month',
                        'week',
                        'color',
                        'file',
                        'image',
                        'hidden',
                    ])
                    ->required()
                    ->default('text'),
                Forms\Components\Select::make('control_type')->required()
                    ->live(debounce: 300)
                    ->searchable()
                    ->options(VisualForms::getControlTypeOptions())
                    ->default(ControlTypes::TextInput->name),
                Forms\Components\TextInput::make('placeholder'),
                Forms\Components\Fieldset::make('Flags')->schema([
                    Forms\Components\Checkbox::make('required')->default(true),
                    Forms\Components\Checkbox::make('unique')->default(false),
                    Forms\Components\Checkbox::make('disabled')->default(false),
                    Forms\Components\Checkbox::make('readonly')->default(false),
                    Forms\Components\Checkbox::make('autofocus')->default(false),
                    Forms\Components\Checkbox::make('multiple')->default(false),
                    Forms\Components\Checkbox::make('autocomplete')->default(false),
                    Forms\Components\Checkbox::make('autocapitalize')->default(false),
                ]),
                Forms\Components\Fieldset::make('Field Options')->visible(fn (
                    Forms\Get $get
                ) => $get('control_type') && ControlTypes::hasOptions($get('control_type')))
                    ->schema([
                        Forms\Components\ToggleButtons::make('options_from_db')
                            ->label(__('Options Source'))
                            ->inline()->options([
                                true => 'From Database',
                                false => 'Specify Manually',
                            ])->live()->default(true),
                        TableRepeater::make('options')
                            ->visible(fn (Forms\Get $get) => ! $get('options_from_db'))
                            ->columnSpanFull()
                            ->hiddenLabel()
                            ->headers([
                                Header::make('label'),
                                Header::make('value'),
                            ])
                            ->schema([
                                Forms\Components\TextInput::make('label'),
                                Forms\Components\TextInput::make('value'),
                            ]),
                        Forms\Components\Select::make('options_db_table')
                            ->required(fn (Forms\Get $get) => $get('options_from_db'))
                            ->visible(fn (Forms\Get $get) => $get('options_from_db'))
                            ->searchable()
                            ->options(fn (Forms\Get $get) => VisualForms::getDatabaseTables())
                            ->live(),
                        Forms\Components\Select::make('options_key_attribute')
                            ->required(fn (Forms\Get $get) => $get('options_from_db') && $get('options_db_table'))
                            ->visible(fn (Forms\Get $get) => $get('options_from_db') && $get('options_db_table'))
                            ->live(onBlur: true)
                            ->default('id')
                            ->options(fn (
                                Forms\Get $get
                            ) => VisualForms::getDatabaseColumns($get('options_db_table'))),
                        Forms\Components\Select::make('options_value_attribute')
                            ->required(fn (Forms\Get $get) => $get('options_from_db') && $get('options_db_table'))
                            ->visible(fn (Forms\Get $get) => $get('options_from_db') && $get('options_db_table'))
                            ->live(onBlur: true)
                            ->options(fn (
                                Forms\Get $get
                            ) => VisualForms::getDatabaseColumns($get('options_db_table'))),
                        TableRepeater::make('options_where_conditions')
                            ->columnSpanFull()
                            ->defaultItems(0)
                            ->visible(fn (Forms\Get $get) => $get('options_from_db') && $get('options_db_table'))
                            ->headers([
                                Header::make('column'),
                                Header::make('operator'),
                                Header::make('value'),
                            ])
                            ->schema([
                                Forms\Components\Select::make('column')
                                    ->options(fn (
                                        Forms\Get $get
                                    ) => VisualForms::getDatabaseColumns($get('../../options_db_table'))),
                                Forms\Components\Select::make('operator')
                                    ->searchable()
                                    ->options(fn () => VisualForms::getDbOperators()),
                                Forms\Components\TextInput::make('value'),
                            ]),

                        Forms\Components\Checkbox::make('searchable')->default(true),
                    ]),
                Forms\Components\Fieldset::make(__('Column Control'))->columnSpanFull()->schema(
                    [
                        Forms\Components\Checkbox::make('colspan_full')->label(__('Full Width'))->live()->default(false),
                        Forms\Components\TextInput::make('colspan')
                            ->numeric()
                            ->label(__('Column Span'))
                            ->inlineLabel()
                            ->required(fn (Forms\Get $get) => ! $get('colspan_full'))
                            ->visible(fn (Forms\Get $get) => ! $get('colspan_full'))
                            ->default(1),
                    ]
                ),
                Forms\Components\Fieldset::make(__('Validation Rules'))->schema([
                    TableRepeater::make('validation_rules')
                        ->columnSpanFull()
                        ->hiddenLabel()
                        ->defaultItems(0)
                        ->headers([
                            Header::make('rule'),
                            Header::make('value'),
                        ])
                        ->schema([
                            Forms\Components\Select::make('rule')
                                ->required()->searchable()
                                ->options(VisualForms::getValidationRules()),
                            Forms\Components\TextInput::make('value')->placeholder('value if needed'),
                        ])
                        ->hint(new HtmlString(\Blade::render("See <x-filament::link target='_blank' href='https://laravel.com/docs/12.x/validation#available-validation-rules'>Available Validation Rules</x-filament::link>"))),
                ]),
                Forms\Components\Radio::make('live_status')
                    ->options([
                        'on' => 'On',
                        'off' => 'Off',
                        'onBlur' => 'onBlur',
                        'debounce' => 'Debounced',
                    ])
                    ->default('off'),
                Forms\Components\TextInput::make('default_value')->nullable(),
                Forms\Components\TextInput::make('hint')->nullable(),
                Forms\Components\TextInput::make('helper_text')->nullable(),
                Forms\Components\TextInput::make('prefix_icon')->nullable()
                    ->placeholder('e.g heroicon-o-calendar')
                    ->helperText(new HtmlString(\Blade::render('See <x-filament::link href="https://heroicons.com" target="_blank">Heroicons</x-filament::link>'))),
                Forms\Components\TextInput::make('suffix_icon')->nullable()
                    ->placeholder('e.g heroicon-o-calendar')
                    ->helperText(new HtmlString(\Blade::render('See <x-filament::link href="https://heroicons.com" target="_blank">Heroicons</x-filament::link>'))),
                Forms\Components\Checkbox::make('inline_prefix')->default(false),
                Forms\Components\Checkbox::make('inline_suffix')->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->modifyQueryUsing(function ($query) {
                $query->orderBy('sort_order');
            })
            ->columns([
                Tables\Columns\TextColumn::make('label'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('control_type'),
            ])
            ->reorderable('sort_order')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
