<?php

namespace Coolsam\VisualForms\ComponentTypes;

use Coolsam\VisualForms\Utils;
use Filament\Forms;

class RichEditor extends Field
{
    public function getOptionName(): string
    {
        return __('Rich Editor');
    }

    public function letThereBe(string $name): Forms\Components\RichEditor|Forms\Components\Component
    {
        return Forms\Components\RichEditor::make($name)->default('');
    }

    public function getSpecificBasicSchema(): array
    {
        return [
            ...parent::getSpecificBasicSchema(),
            Forms\Components\Fieldset::make()->columns([
                'md' => 2, 'lg' => 3, 'xl' => 4,
            ])->schema([
                Forms\Components\ToggleButtons::make('disableAllToolbarButtons')
                    ->label(__('Disable All Toolbar Buttons'))
                    ->helperText(__('Disable all toolbar buttons.'))
                    ->boolean()
                    ->live()
                    ->inline(),
                Forms\Components\TextInput::make('fileAttachmentsDisk')
                    ->label(__('File Attachments Disk'))
                    ->placeholder('e.g s3')
                    ->helperText(__('The disk to use for file attachments.')),
                Forms\Components\TextInput::make('fileAttachmentsDirectory')
                    ->label(__('File Attachments Directory'))
                    ->placeholder('e.g rich-editor')
                    ->helperText(__('The directory to use for file attachments.')),
                Forms\Components\ToggleButtons::make('fileAttachmentsVisibility')
                    ->label(__('File Attachments Visibility'))
                    ->options([
                        'public' => __('Public'),
                        'private' => __('Private'),
                    ])
                    ->inline()
                    ->helperText(__('The visibility to use for file attachments.')),
                Forms\Components\ToggleButtons::make('disableGrammarly')
                    ->label(__('Disable Grammarly'))
                    ->helperText(__('Disable Grammarly for this field.'))
                    ->boolean()
                    ->inline()
                    ->visible(function ($get) {
                        if (! $get('../component_type')) {
                            return false;
                        }
                        if (! $get('../name')) {
                            return false;
                        }
                        $component = (Utils::instantiateClass($get('../component_type')))?->letThereBe($get('../name'));
                        if (! $component) {
                            return false;
                        }

                        return method_exists($component, 'disableGrammarly');
                    }),
                Forms\Components\CheckboxList::make('toolbarButtons')
                    ->label(__('Toolbar Buttons'))
                    ->columnSpanFull()
                    ->visible(fn ($get) => ! $get('disableAllToolbarButtons'))
                    ->options($this->getToolbarButtons())
                    ->columns(['sm' => 2, 'md' => 4, 'lg' => 5]),
            ]),
        ];
    }

    public function configureComponent(&$component, bool $editable): void
    {
        parent::configureComponent($component, $editable);
        $props = $this->getProps();

        if (filled($props->get('disableAllToolbarButtons')) && method_exists($component, 'disableAllToolbarButtons')) {
            $component->disableAllToolbarButtons(Utils::getBool($props->get('disableAllToolbarButtons')));
        }
        $disableAll = Utils::getBool($props->get('disableAllToolbarButtons'));
        if (! $disableAll) {
            if (filled($props->get('toolbarButtons')) && method_exists($component, 'toolbarButtons')) {
                $component->toolbarButtons($props->get('toolbarButtons'));
            }
        }
        if (filled($props->get('fileAttachmentsDisk')) && method_exists($component, 'fileAttachmentsDisk')) {
            $component->fileAttachmentsDisk($props->get('fileAttachmentsDisk'));
        }

        if (filled($props->get('fileAttachmentsDirectory')) && method_exists($component, 'fileAttachmentsDirectory')) {
            $component->fileAttachmentsDirectory($props->get('fileAttachmentsDirectory'));
        }

        if (filled($props->get('fileAttachmentsVisibility')) && method_exists($component, 'fileAttachmentsVisibility')) {
            $component->fileAttachmentsVisibility($props->get('fileAttachmentsVisibility'));
        }

        if (filled($props->get('disableGrammarly')) && method_exists($component, 'disableGrammarly')) {
            $component->disableGrammarly(Utils::getBool($props->get('disableGrammarly')));
        }
    }

    protected function getToolbarButtons(): array
    {
        return [
            'attachFiles' => __('Attach Files'),
            'blockquote' => __('Blockquote'),
            'bold' => __('Bold'),
            'bulletList' => __('Bullet List'),
            'codeBlock' => __('Code Block'),
            'h1' => __('H1'),
            'h2' => __('H2'),
            'h3' => __('H3'),
            'italic' => __('Italic'),
            'link' => __('Link'),
            'orderedList' => __('Ordered List'),
            'redo' => __('Redo'),
            'strike' => __('Strike'),
            'underline' => __('Underline'),
            'undo' => __('Undo'),
        ];
    }
}
