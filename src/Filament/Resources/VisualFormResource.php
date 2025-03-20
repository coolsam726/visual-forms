<?php

namespace Coolsam\VisualForms\Filament\Resources;

use Coolsam\VisualForms\Filament\Resources;
use Coolsam\VisualForms\Models\VisualForm;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class VisualFormResource extends Resource
{
    protected static ?string $slug = 'visual-forms';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModel(): string
    {
        return \Config::get('visual-forms.models.visual_form');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                    TextInput::make('slug')
                        ->readonly()
                        ->required()
                        ->unique(VisualForm::class, 'slug', fn ($record) => $record),

                    TextInput::make('description'),

                    Checkbox::make('is_active')->default(true),

                    Placeholder::make('created_at')
                        ->label('Created Date')
                        ->content(fn (?VisualForm $record): string => $record?->getAttribute('created_at')?->diffForHumans() ?? '-'),

                    Placeholder::make('updated_at')
                        ->label('Last Modified Date')
                        ->content(fn (?VisualForm $record): string => $record?->getAttribute('updated_at')?->diffForHumans() ?? '-'),
                ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description'),
                TextColumn::make('is_active')->badge()
                    ->label(__('Active'))
                    ->color(fn (VisualForm $record) => match ($record->getAttribute('is_active')) {
                        true => 'success',
                        false => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
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
            'index' => Resources\VisualFormResource\Pages\ListVisualForms::route('/'),
            'create' => Resources\VisualFormResource\Pages\CreateVisualForm::route('/create'),
            'edit' => Resources\VisualFormResource\Pages\EditVisualForm::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug'];
    }

    public static function getRelations(): array
    {
        return [
            Resources\VisualFormResource\RelationManagers\ComponentsRelationManager::class,
            //            Resources\VisualFormResource\RelationManagers\FieldsRelationManager::class,
            Resources\VisualFormResource\RelationManagers\EntriesRelationManager::class,
        ];
    }
}
