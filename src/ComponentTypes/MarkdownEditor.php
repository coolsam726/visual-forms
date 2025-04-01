<?php

namespace Coolsam\VisualForms\ComponentTypes;

class MarkdownEditor extends RichEditor {
    public function getOptionName(): string
    {
        return __('Markdown Editor');
    }

    public function letThereBe(string $name): \Filament\Forms\Components\MarkdownEditor
    {
        return \Filament\Forms\Components\MarkdownEditor::make($name);
    }

    protected function getToolbarButtons(): array
    {
        return collect([
            'attachFiles',
            'blockquote',
            'bold',
            'bulletList',
            'codeBlock',
            'heading',
            'italic',
            'link',
            'orderedList',
            'redo',
            'strike',
            'table',
            'undo',
        ])->mapWithKeys(fn($item) => [$item => str($item)->pascal()->snake()->title()->replace('_', ' ')])->toArray();
    }
}
