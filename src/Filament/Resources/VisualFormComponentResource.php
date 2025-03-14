<?php

namespace Coolsam\VisualForms\Filament\Resources;

use Coolsam\VisualForms\Facades\VisualForms;
use Coolsam\VisualForms\Filament\Resources\VisualFormResource\RelationManagers\ComponentsRelationManager;
use Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource\Pages;
use Coolsam\VisualForms\Models\VisualFormComponent;
use Coolsam\VisualForms\Utils;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;

class VisualFormComponentResource extends Resource
{
    protected static ?string $model = VisualFormComponent::class;

    protected static ?string $slug = 'visual-form-components';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(static::getSchema());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('visualForm.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('label'),
                TextColumn::make('is_active')->badge()->alignEnd(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisualFormComponents::route('/'),
            'create' => Pages\CreateVisualFormComponent::route('/create'),
            'edit' => Pages\EditVisualFormComponent::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['visualForm']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'visualForm.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->visualForm) {
            $details['VisualForm'] = $record->visualForm->name;
        }

        return $details;
    }

    public static function getRelations(): array
    {
        return [
            ComponentsRelationManager::class,
        ];
    }

    public static function getSchema(): array
    {
        return [
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
                        Utils::instantiateClass($get('component_type'))->getMainSchema()),
                Forms\Components\Wizard\Step::make('Step 3: Validation Rules')
                    ->schema(fn (Forms\Get $get) => ! $get('component_type') ? [] :
                        Utils::instantiateClass($get('component_type'))->getValidationSchema())
                    ->visible(fn (Forms\Get $get) => $get('component_type') && ! Utils::instantiateClass($get('component_type'))->isLayout()),

            ])
                ->extraAttributes(['class' => 'fi-fo-wizard-vertical'])
                ->columnSpanFull(),
        ];
    }
}
