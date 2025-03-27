<?php

namespace Coolsam\VisualForms\Filament\Resources;

use Coolsam\VisualForms\Facades\VisualForms;
use Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource\Pages;
use Coolsam\VisualForms\Filament\Resources\VisualFormResource\RelationManagers\ComponentsRelationManager;
use Coolsam\VisualForms\Models\VisualFormComponent;
use Coolsam\VisualForms\Utils;
use Filament\Forms;
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

class VisualFormComponentResource extends Resource
{
    protected static ?string $slug = 'visual-form-components';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function getModel(): string
    {
        return \Config::get('visual-forms.models.visual_form_component', VisualFormComponent::class);
    }

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

        if ($visualForm = $record->getAttribute('visualForm')) {
            $details['VisualForm'] = $visualForm->getAttribute('name');
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
            Forms\Components\Tabs::make()->schema([
                Forms\Components\Tabs\Tab::make(__('Component Type'))->schema([
                    Forms\Components\Select::make('component_type')
                        ->required()
                        ->live()
                        ->searchable()
                        ->options(VisualForms::getComponentTypeOptions()),
                    Forms\Components\Select::make('parent_id')->label(__('Parent Component'))
                        ->live()
                        ->searchable()
                        ->visible(fn ($record, $state) => (bool) $record?->getAttribute('id') || $state)
                        ->options(Utils::getEligibleParentComponents()->toArray()),
                    Forms\Components\TextInput::make('sort_order')->visible(fn ($record) => (bool) $record?->id)->live()
                        ->afterStateHydrated(function ($state, Forms\Set $set, ?VisualFormComponent $record) {
                            if (! $record) {
                                return;
                            }
                            if ($record->getAttribute('parent_id') === null) {
                                $q = $record->visualForm->children()->whereNull('parent_id');
                            } else {
                                $q = $record->siblings();
                            }
                            $next = $q
                                ->where('is_active', '=', true)
                                ->where('id', '!=', $record->id)
                                ->where('sort_order', '<=', $state)
                                ->orderBy('created_at', 'desc')
                                ->orderBy('sort_order', 'desc')
                                ->first();
                            $set('create_after', $next?->getKey() ?? 0);
                        })
                        ->default(0),
                    Forms\Components\Fieldset::make(__('Sort'))->schema([
                        Forms\Components\Select::make('create_after')
                            ->visible(fn ($record) => (bool) $record?->id)
                            ->options(function (Forms\Get $get, VisualFormComponent $record) {
                                // get the siblings of this record
                                if ($record->getAttribute('parent_id') == null) {
                                    $options = $record->visualForm->children()
                                        ->whereNull('parent_id')
                                        ->where('id', '!=', $record->getKey())
                                        ->orderBy('created_at')
                                        ->orderBy('sort_order')
                                        ->where('is_active', '=', true)->get();
                                } else {
                                    $options = $record->siblings()->where('is_active', '=', true)->get();
                                }

                                return $options
                                    ->mapWithKeys(
                                        fn (VisualFormComponent $sibling) => [$sibling->getAttributeValue('id') => $sibling->getAttribute('label')]
                                    )->prepend('Create at the Beginning', 0);
                            })
                            ->visible(fn ($record) => $record)
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $after = VisualFormComponent::find($state);
                                if ($after?->id) {
                                    $set('sort_order', $after->sort_order + 1);
                                }
                            })
                            ->label(__('Move After')),
                    ]),
                ]),
                Forms\Components\Tabs\Tab::make(__('Component Details'))
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
                ->extraAttributes(['class' => 'fi-fo-wizard-vertical'])
                ->columnSpanFull(),
        ];
    }
}
