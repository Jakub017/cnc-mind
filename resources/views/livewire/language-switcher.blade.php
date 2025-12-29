<flux:dropdown >
    <flux:button icon:trailing="chevron-down" class="cursor-pointer">
        <span
            class="fi fi-{{ $this->languageLabel['flag'] }} inline-block mr-2"
        ></span>
        {{ $this->languageLabel['name'] }}
    </flux:button>
    <flux:navmenu>
        <flux:navmenu.item
            wire:click="setLocale('pl')"
            class="flex items-center gap-2 cursor-pointer"
            ><span class="fi fi-pl"></span>
            {{ __("Polish") }}</flux:navmenu.item
        >
        <flux:navmenu.item
            wire:click="setLocale('en')"
            class="flex items-center gap-2 cursor-pointer"
            ><span class="fi fi-gb"></span>
            {{ __("English") }}</flux:navmenu.item
        >
        <flux:navmenu.item
            wire:click="setLocale('de')"
            class="flex items-center gap-2 cursor-pointer"
            ><span class="fi fi-de"></span>
            {{ __("German") }}</flux:navmenu.item
        >
    </flux:navmenu>
</flux:dropdown>
