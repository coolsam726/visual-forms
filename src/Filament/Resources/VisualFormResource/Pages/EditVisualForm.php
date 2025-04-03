<?php

namespace Coolsam\VisualForms\Filament\Resources\VisualFormResource\Pages;

use Coolsam\VisualForms\Filament\Resources\VisualFormResource;
use Coolsam\VisualForms\Models\VisualForm;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditVisualForm extends EditRecord
{
    public static function getResource(): string
    {
        return \Config::get('visual-forms.resources.visual-form.resource', VisualFormResource::class);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')->label(__('Preview Form'))
                ->modalWidth('container')
                ->slideOver()
                ->form(fn (VisualForm $record, Form $form) => $form
                    ->columns()
                    ->schema($record->schema()))
                ->modalCancelActionLabel(__('Close'))->action(function (VisualForm $record, array $data) {
                    \Log::info(collect($data));
                    // TODO: Uncomment below or implement your own method to save the data.
                    //                    $record->recordSubmission($data, isProcessed: true);
                    Notification::make('success')
                        ->title('Submitted Data')
                        ->body(json_encode($data))
                        ->success()
                        ->persistent()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            VisualFormResource\Widgets\FieldEditor::make(),
        ];
    }
}
