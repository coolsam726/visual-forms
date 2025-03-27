@php
$headerActions = $getHeaderActions();
if (is_array($headerActions)) {
        $headerActions = array_filter(
            $headerActions,
            fn ($headerAction): bool => $headerAction->isVisible(),
        );
    }
$hasHeaderActions = filled($headerActions);
@endphp
<x-filament::fieldset
        :label="$getLabel()"
        :label-hidden="$isLabelHidden()"
        :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
    "
>
    @if ($hasHeaderActions)
        <div id="actions-legend" class="flex items-center justify-start space-x-2 bg-white p-2 opacity-100 dark:bg-gray-900 dark:opacity-100">
            <x-filament::actions
                    :actions="$headerActions"
                    :alignment="\Filament\Support\Enums\Alignment::Start"
                    x-on:click.stop=""
            />
        </div>
    @endif
    {{ $getChildComponentContainer() }}
</x-filament::fieldset>
<style>
    fieldset {
        position: relative;
    }
    #actions-legend {
        position: absolute;
        top: -30px;
        right: 25px;
    }
</style>