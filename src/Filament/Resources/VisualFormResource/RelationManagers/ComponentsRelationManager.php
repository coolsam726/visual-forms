<?php

namespace Coolsam\VisualForms\Filament\Resources\VisualFormResource\RelationManagers;

use Coolsam\VisualForms\Filament\Resources\VisualFormComponentResource;
use Coolsam\VisualForms\Models\VisualForm;
use Coolsam\VisualForms\Models\VisualFormComponent;
use Coolsam\VisualForms\Utils;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ComponentsRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    public static function getResource()
    {
        return \Config::get('visual-forms.resources.visual-form-component', VisualFormComponentResource::class);
    }

    protected function configureCreateAction(Tables\Actions\CreateAction $action): void
    {
        parent::configureCreateAction($action);
        $action->slideOver()->modalWidth('container');
    }

    protected function configureEditAction(Tables\Actions\EditAction $action): void
    {
        parent::configureEditAction($action);

        $action
            ->slideOver()
            ->modalWidth('container');
    }

    public static function canViewForRecord(Model | VisualForm | VisualFormComponent $ownerRecord, string $pageClass): bool
    {
        return (parent::canViewForRecord($ownerRecord, $pageClass)
                && (is_subclass_of(
                    $ownerRecord,
                    \Config::get('visual-forms.models.visual_form')
                ))) || (is_subclass_of($ownerRecord, \Config::get('visual-forms.models.visual_form_component')) && Utils::instantiateClass($ownerRecord->getAttribute('component_type'))->hasChildren());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(VisualFormComponentResource::getSchema());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->modifyQueryUsing(function (Builder $query) {
                if ($this->ownerRecord instanceof VisualForm) {
                    $query->whereNull('parent_id');
                }
                $query->orderBy('sort_order');

                return $query;
            })
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('label')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('component_type')->searchable()->sortable()->formatStateUsing(function (string $state) {
                    return Utils::instantiateClass($state)->getOptionName();
                }),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label(__('Active')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->mutateFormDataUsing(function (array $data) {

                    if ($this->ownerRecord instanceof VisualFormComponent) {
                        $data['form_id'] = $this->ownerRecord->getAttribute('form_id');
                    }

                    return $data;
                }),
            ])
            ->actions([
                Tables\Actions\Action::make('manage')->label(__('Manage'))
                    ->icon('heroicon-o-chevron-double-right')
                    ->color('success')
                    ->url(fn (VisualFormComponent $record) => (static::getResource())::getUrl('edit', ['record' => $record->getKey()])),
                Tables\Actions\EditAction::make()->color('warning'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
