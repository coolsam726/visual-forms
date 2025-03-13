<?php

namespace Coolsam\VisualForms\Filament\Resources\VisualFormResource\RelationManagers;

use Coolsam\VisualForms\Facades\VisualForms;
use Coolsam\VisualForms\Utils;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ComponentsRelationManager extends RelationManager
{
    protected static string $relationship = 'components';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Wizard::make([
                    Forms\Components\Wizard\Step::make('Step 1: Component Type')->schema([
                        Forms\Components\Select::make('component_type')
                            ->required()
                            ->live()
                            ->searchable()
                            ->options(VisualForms::getComponentTypeOptions()),
                    ]),
                    Forms\Components\Wizard\Step::make('Step 2: Component Details')
                        ->schema(fn (Forms\Get $get) => ! $get('component_type') ? [] :
                            Utils::instantiateClass($get('component_type'))->getBackendSchema()),

                ])->columnSpanFull(),
                Forms\Components\Placeholder::make('info')
                    ->content(fn (Forms\Get $get) => new HtmlString($get('component_type')))
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('label'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->mutateFormDataUsing(function (array $data) {
                    dd($data);

                    return $data;
                }),
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
