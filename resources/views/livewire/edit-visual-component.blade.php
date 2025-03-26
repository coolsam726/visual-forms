<div>
    <form wire:submit.prevent="save" novalidate autocomplete="off">
        {{ $this->form }}
        <button type="submit">
            Submit
        </button>
    </form>
</div>
