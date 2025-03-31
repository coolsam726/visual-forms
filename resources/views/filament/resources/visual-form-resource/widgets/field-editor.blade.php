<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Manage Form Fields
        </x-slot>
        <x-slot name="description">
            Click the <strong>Edit Field</strong> button to edit the field.
        </x-slot>
        {{ $this->form }}
    </x-filament::section>
    <x-filament-actions::modals/>
</x-filament-widgets::widget>

