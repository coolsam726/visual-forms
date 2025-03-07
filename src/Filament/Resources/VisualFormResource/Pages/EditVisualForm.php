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
    protected static string $resource = VisualFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')->label(__('Preview Form'))
                ->form(fn (VisualForm $record, Form $form) => $form
                    ->columns()
                    ->schema($record->schema()))
                ->modalCancelActionLabel(__('Close'))->action(function (VisualForm $record, array $data) {
                    \Log::info(collect($data));
                    $record->recordSubmission($data, isProcessed: true);
                    Notification::make('success')->title('Submitted Data')
                        ->body(json_encode($data))
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }
}
