<?php

namespace Coolsam\VisualForms\Filament\Resources;

use Coolsam\VisualForms\Filament\Resources;
use Coolsam\VisualForms\Models\VisualForm;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
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

    public static function getWidgets(): array
    {
        return [
            Resources\VisualFormResource\Widgets\FieldEditor::class,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make()
                    ->columnSpanFull()
                    ->schema([
                        Tabs\Tab::make(__('Basic Details'))->schema([
                            TextInput::make('name')
                                ->required()
                                ->afterStateHydrated(function (VisualForm $record, Get $get, Set $set) {
                                    if (! $get('settings')) {
                                        $set('settings', [
                                            ['name' => 'contact_phone', 'setting_type' => 'text', 'value' => null],
                                            ['name' => 'contact_email', 'setting_type' => 'email', 'value' => null],
                                            ['name' => 'privacy_policy_url', 'setting_type' => 'url', 'value' => null],
                                            ['name' => 'show_media_consent', 'setting_type' => 'boolean', 'value' => null],
                                            ['name' => 'media_consent_content', 'setting_type' => 'richText', 'value' => null],
                                        ]);
                                    }
                                })
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                            TextInput::make('slug')
                                ->readonly()
                                ->required()
                                ->unique(VisualForm::class, 'slug', fn ($record) => $record),

                            TextInput::make('description'),
                            Select::make('users')->options(\Config::get('visual-forms.closures.fetchUsers'))->multiple()->required(),

                            Checkbox::make('is_active')->default(true),

                            Placeholder::make('created_at')
                                ->label('Created Date')
                                ->content(fn (
                                    ?VisualForm $record
                                ): string => $record?->getAttribute('created_at')?->diffForHumans() ?? '-'),

                            Placeholder::make('updated_at')
                                ->label('Last Modified Date')
                                ->content(fn (
                                    ?VisualForm $record
                                ): string => $record?->getAttribute('updated_at')?->diffForHumans() ?? '-'),
                        ])->columns(),
                        Tabs\Tab::make(__('More Settings'))->lazy()
                            ->schema([
                                Repeater::make('settings')
                                    ->schema(components: [
                                        TextInput::make('name')->label(__('Setting Name'))
                                            ->placeholder('e.g email_recipients')->required(),
                                        Select::make('setting_type')->label(__('Setting Type'))
                                            ->options([
                                                'text' => 'Text',
                                                'integer' => 'Integer',
                                                'url' => 'URL',
                                                'float' => 'Decimal',
                                                'password' => 'Password/Secret',
                                                'email' => 'Email',
                                                'longText' => 'Textarea',
                                                'file' => 'File',
                                                'date' => 'Date',
                                                'time' => 'Time',
                                                'datetime' => 'Date & Time',
                                                'boolean' => 'Boolean',
                                                'richText' => 'Rich Text (HTML)',
                                                'markdown' => 'Markdown',
                                                'tags' => 'Tags',
                                            ])->searchable()->default('text')
                                            ->live()
                                            ->required(),
                                        ...static::makeSettingValueField(),
                                    ])->columns(3),
                            ]),
                    ]),
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
        return [];
    }

    public static function makeSettingValueField(): array
    {
        // Convert this to be an array, each with a closure to visible
        return [
            TextInput::make('value')->label(__('Setting Value'))->required()->visible(fn (
                Get $get
            ) => $get('setting_type') == 'text'),
            TextInput::make('value')->label(__('Setting Value'))->required()->numeric()->integer()->visible(fn (
                Get $get
            ) => $get('setting_type') == 'integer'),
            TextInput::make('value')->label(__('Setting Value'))->required()->numeric()->visible(fn (
                Get $get
            ) => $get('setting_type') == 'float'),
            TextInput::make('value')->label(__('Setting Value'))->required()->password()->revealable()->visible(fn (
                Get $get
            ) => $get('setting_type') == 'password'),
            TextInput::make('value')->label(__('Setting Value'))->required()->email()->visible(fn (
                Get $get
            ) => $get('setting_type') == 'email'),
            Textarea::make('value')->label(__('Setting Value'))->required()->visible(fn (
                Get $get
            ) => $get('setting_type') == 'longText'),
            FileUpload::make('value')->label(__('Setting Value'))->required()->visible(fn (
                Get $get
            ) => $get('setting_type') == 'file'),
            DatePicker::make('value')->label(__('Setting Value'))->required()->date()->visible(fn (
                Get $get
            ) => $get('setting_type') == 'date'),
            TimePicker::make('value')->label(__('Setting Value'))->required()->time()->visible(fn (
                Get $get
            ) => $get('setting_type') == 'time'),
            DateTimePicker::make('value')->label(__('Setting Value'))->required()->visible(fn (
                Get $get
            ) => $get('setting_type') == 'datetime'),
            ToggleButtons::make('value')->boolean()->inline()->label(__('Setting Value'))->default(false)->visible(fn (
                Get $get
            ) => $get('setting_type') == 'boolean'),
            RichEditor::make('value')->label(__('Setting Value'))->required()->visible(fn (
                Get $get
            ) => $get('setting_type') == 'richText')->columnSpanFull(),
            MarkdownEditor::make('value')->label(__('Setting Value'))->required()->visible(fn (
                Get $get
            ) => $get('setting_type') == 'markdown')->columnSpanFull(),
            TagsInput::make('value')->label(__('Setting Value'))->required()->visible(fn (
                Get $get
            ) => $get('setting_type') == 'tags')->columnSpanFull(),
            TextInput::make('one_conf'),
        ];
    }
}
