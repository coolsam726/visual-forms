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
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
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

    public static function getNavigationIcon(): string | Htmlable | null
    {
        return \Config::get('visual-forms.resources.visual-form.navigation-icon') ?? parent::getNavigationIcon();
    }

    public static function getNavigationLabel(): string
    {
        return \Config::get('visual-forms.resources.visual-form.navigation-label') ?? parent::getNavigationLabel();
    }

    public static function getNavigationGroup(): ?string
    {
        return \Config::get('visual-forms.resources.visual-form.navigation-group') ?? parent::getNavigationGroup();
    }

    public static function getNavigationSort(): ?int
    {
        return \Config::get('visual-forms.resources.visual-form.navigation-sort') ?? parent::getNavigationSort();
    }

    public static function getModelLabel(): string
    {
        return \Config::get('visual-forms.resources.visual-form.model-label') ?? parent::getModelLabel();
    }

    public static function getCluster(): ?string
    {
        return \Config::get('visual-forms.resources.visual-form.cluster') ?? parent::getCluster();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make()
                    ->columnSpanFull()
                    ->schema(function () {
                        $settingsSchema = \Config::get('visual-forms.closures.form-settings-schema', []);

                        return [
                            Tabs\Tab::make(__('Basic Details'))->schema([
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
                                    ->content(fn (
                                        ?VisualForm $record
                                    ): string => $record?->getAttribute('created_at')?->diffForHumans() ?? '-'),

                                Placeholder::make('updated_at')
                                    ->label('Last Modified Date')
                                    ->content(fn (
                                        ?VisualForm $record
                                    ): string => $record?->getAttribute('updated_at')?->diffForHumans() ?? '-'),
                            ])->columns(),
                            Tabs\Tab::make(__('More Settings'))
                                ->columns(['md' => 2, 'lg' => 3])
                                ->statePath('settings')
                                ->lazy()
                                ->schema($settingsSchema),
                        ];
                    }),
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
                ViewAction::make()->slideOver()->modalWidth('container'),
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
