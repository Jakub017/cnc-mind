<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout
        :heading="__('Language')"
        :subheading=" __('Update the language settings for your account')"
    >
        <livewire:language-switcher class="w-full" />
    </x-settings.layout>
</section>
