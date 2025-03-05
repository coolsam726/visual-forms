<?php

namespace Coolsam\VisualForms\Filament\Resources\VisualFormResource\RelationManagers;

use Coolsam\VisualForms\ControlTypes;
use Coolsam\VisualForms\Facades\VisualForms;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
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
                    ->afterStateUpdated(fn ($state, callable $set) => $set('label', str($state)->camel()->snake()->title()->explode('_')->join(' '))),
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
                    ->searchable()->options(VisualForms::getControlTypeOptions())
                    ->default(ControlTypes::TextInput->name),
                Forms\Components\TextInput::make('placeholder'),
                Forms\Components\Fieldset::make('Flags')->schema([
                    Forms\Components\Checkbox::make('required')->default(true),
                    Forms\Components\Checkbox::make('disabled')->default(false),
                    Forms\Components\Checkbox::make('readonly')->default(false),
                    Forms\Components\Checkbox::make('autofocus')->default(false),
                    Forms\Components\Checkbox::make('multiple')->default(false),
                    Forms\Components\Checkbox::make('autocomplete')->default(true),
                    Forms\Components\Checkbox::make('autocapitalize')->default(false),


                ]),
                Forms\Components\TextInput::make('default_value')->nullable(),
                Forms\Components\TextInput::make('hint')->nullable(),
                Forms\Components\TextInput::make('helper_text')->nullable(),
                Forms\Components\TextInput::make('prefix_icon')->nullable()
                    ->placeholder('e.g heroicon-o-calendar')
                    ->helperText(new HtmlString(\Blade::render('See <x-filament::link href="https://heroicons.com" target="_blank">Heroicons</x-filament::link>'))),
                Forms\Components\TextInput::make('suffix_icon')->nullable()
                    ->placeholder('e.g heroicon-o-calendar')
                    ->helperText(new HtmlString(\Blade::render('See <x-filament::link href="https://heroicons.com" target="_blank">Heroicons</x-filament::link>'))),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('label'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('control_type'),
            ])
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
