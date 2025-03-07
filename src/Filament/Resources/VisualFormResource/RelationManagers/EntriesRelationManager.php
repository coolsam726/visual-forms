<?php

namespace Coolsam\VisualForms\Filament\Resources\VisualFormResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class EntriesRelationManager extends RelationManager
{
    protected $listeners = [
        'refresh' => '$refresh',
    ];

    protected static string $relationship = 'entries';

    protected static ?string $label = 'Submitted Entries';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
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
                        ->mapWithKeys(fn ($value, $key) => [$key => new HtmlString($value)]))->columnSpanFull(),
                Infolists\Components\TextEntry::make('created_at')->label(__('Created At'))->dateTime(),
                Infolists\Components\TextEntry::make('updated_at')->label(__('Updated At'))->dateTime(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ulid')
            ->modifyQueryUsing(function ($query) {
                return $query->orderByDesc('created_at');
            })
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('ulid')->label(__('Unique ID')),
                Tables\Columns\TextColumn::make('ip_address')->label(__('IP Address')),
                Tables\Columns\TextColumn::make('created_at')->label(__('Created At'))->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')->label(__('Updated At'))->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('refresh')->label(__('Refresh'))->action(fn () => $this->dispatch('refresh')),
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
}
