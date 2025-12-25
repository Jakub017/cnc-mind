<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout
        :heading="__('Language')"
        :subheading=" __('Update the language settings for your account')"
    >
        <flux:select wire:model.live="locale">
            <flux:select.option value="pl">{{
                __("Polish")
            }}</flux:select.option>
            <flux:select.option value="en">{{
                __("English")
            }}</flux:select.option>
            <flux:select.option value="de">{{
                __("German")
            }}</flux:select.option>
        </flux:select>
    </x-settings.layout>
</section>
