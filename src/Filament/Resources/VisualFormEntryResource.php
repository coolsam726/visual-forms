<?php

namespace Coolsam\VisualForms\Filament\Resources;

use Coolsam\VisualForms\Filament\Resources\VisualFormEntryResource\Pages;
use Coolsam\VisualForms\Models\VisualFormEntry;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class VisualFormEntryResource extends Resource
{
    protected $listeners = [
        'refresh' => '$refresh',
    ];

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModel(): string
    {
        return \Config::get('visual-forms.models.visual_form_entry') ?? VisualFormEntry::class;
    }

    public static function getNavigationIcon(): string | Htmlable | null
    {
        return \Config::get('visual-forms.resources.visual-form-entry.navigation-icon') ?? parent::getNavigationIcon();
    }

    public static function getNavigationLabel(): string
    {
        return \Config::get('visual-forms.resources.visual-form-entry.navigation-label') ?? parent::getNavigationLabel();
    }

    public static function getNavigationGroup(): ?string
    {
        return \Config::get('visual-forms.resources.visual-form-entry.navigation-group') ?? parent::getNavigationGroup();
    }

    public static function getNavigationSort(): ?int
    {
        return \Config::get('visual-forms.resources.visual-form-entry.navigation-sort') ?? parent::getNavigationSort();
    }

    public static function getModelLabel(): string
    {
        return \Config::get('visual-forms.resources.visual-form-entry.model-label') ?? parent::getModelLabel();
    }

    public static function getCluster(): ?string
    {
        return \Config::get('visual-forms.resources.visual-form-entry.cluster') ?? parent::getCluster();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('id')->label(__('ID')),
                Infolists\Components\TextEntry::make('ulid')->label(__('ULID')),
                Infolists\Components\TextEntry::make('parent.name')->label(__('Parent Form')),
                Infolists\Components\TextEntry::make('ip_address')->label(__('IP Address')),
                Infolists\Components\KeyValueEntry::make('payload')->label(__('Payload'))
                    ->extraAttributes(['class' => 'prose max-w-none'])
                    ->getStateUsing(fn ($record) => collect($record->payload)
                        ->mapWithKeys(fn (
                            $value,
                            $key
                        ) => [$key => new HtmlString(is_array($value) ? json_encode($value) : $value)]))->columnSpanFull(),
                Infolists\Components\TextEntry::make('created_at')->label(__('Created At'))->dateTime(),
                Infolists\Components\TextEntry::make('updated_at')->label(__('Updated At'))->dateTime(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ulid')
            ->modifyQueryUsing(function ($query) {
                return $query->orderByDesc('created_at');
            })
            ->columns(static::getTableSchema())
            ->persistFiltersInSession()
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->filters([
                Tables\Filters\SelectFilter::make('parent')
                    ->relationship('parent', 'name')
                    ->label(__('Parent Form'))
                    ->searchable()
                    ->preload()
                    ->columnSpan(['md' => 2])
                    ->placeholder(__('Select Parent Form')),
            ])
            ->headerActions([
                Tables\Actions\Action::make('refresh')
                    ->label(__('Refresh'))
                    ->action(fn ($livewire) => $livewire->dispatch('refresh')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageVisualFormEntries::route('/'),
        ];
    }

    public static function getTableSchema(): array
    {
        return [
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('parent.name')
                ->label(__('Parent Form'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('ulid')
                ->label(__('Unique ID'))->toggleable(isToggledHiddenByDefault: true)
                ->formatStateUsing(fn ($state) => strtoupper($state))
                ->sortable(),
            Tables\Columns\TextColumn::make('ip_address')->label(__('IP Address'))->searchable(),
            Tables\Columns\IconColumn::make('is_processed')->boolean()->sortable(),
            Tables\Columns\TextColumn::make('created_at')->sortable()->label(__('Created At'))->dateTime(),
            Tables\Columns\TextColumn::make('updated_at')->sortable()->label(__('Updated At'))->dateTime(),
        ];
    }
}
