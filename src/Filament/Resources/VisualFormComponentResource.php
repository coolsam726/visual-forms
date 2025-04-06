<?php

namespace Coolsam\VisualForms\Filament\Resources;

use Coolsam\VisualForms\ComponentTypes\Component;
use Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource\Pages;
use Coolsam\VisualForms\Filament\Resources\VisualFormResource\RelationManagers\ComponentsRelationManager;
use Coolsam\VisualForms\Models\VisualFormComponent;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
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

    public static function getNavigationIcon(): string | Htmlable | null
    {
        return \Config::get('visual-forms.resources.visual-form-component.navigation-icon') ?? parent::getNavigationIcon();
    }

    public static function getNavigationLabel(): string
    {
        return \Config::get('visual-forms.resources.visual-form-component.navigation-label') ?? parent::getNavigationLabel();
    }

    public static function getNavigationGroup(): ?string
    {
        return \Config::get('visual-forms.resources.visual-form-component.navigation-group') ?? parent::getNavigationGroup();
    }

    public static function getNavigationSort(): ?int
    {
        return \Config::get('visual-forms.resources.visual-form-component.navigation-sort') ?? parent::getNavigationSort();
    }

    public static function getModelLabel(): string
    {
        return \Config::get('visual-forms.resources.visual-form-component.model-label') ?? parent::getModelLabel();
    }

    public static function getCluster(): ?string
    {
        return \Config::get('visual-forms.resources.visual-form-component.cluster') ?? parent::getCluster();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'visualForm.name'];
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
        return Component::getFullSchema();
    }
}
