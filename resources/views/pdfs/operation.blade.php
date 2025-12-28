<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body>
        <div class="flex flex-col gap-4 p-4">
            <h1 class="text-2xl font-bold text-center">
                {{ $operation->name }}
            </h1>
            <div
                class="bg-zinc-50 dark:bg-zinc-700 flex w-full rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4"
            >
                <flux:heading>
                    {{ __("Description") }}
                </flux:heading>
                <flux:text>{{ $operation->description }}</flux:text>
            </div>
            <div class="flex w-full flex-col gap-4">
                <div class="flex w-full gap-4">
                    <div
                        class="bg-zinc-50 dark:bg-zinc-700 flex w-full md:w-1/2 rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4"
                    >
                        <flux:heading>
                            {{ __("Tool") }}
                        </flux:heading>
                        <flux:text>{{ $operation->tool->name }}</flux:text>
                    </div>
                    <div
                        class="bg-zinc-50 dark:bg-zinc-700 flex w-full md:w-1/2 rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4"
                    >
                        <flux:heading>
                            {{ __("Material") }}
                        </flux:heading>
                        <flux:text>{{ $operation->material->name }}</flux:text>
                    </div>
                </div>
            </div>
            <flux:separator text="{{ __('Calculated Parameters') }}" />
            <div class="flex w-full flex-col gap-4">
                <div class="flex w-full gap-4">
                    <div
                        class="bg-zinc-50 dark:bg-zinc-700 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4"
                    >
                        <flux:heading>
                            {{ __("Cutting speed") }}
                        </flux:heading>
                        <flux:text
                            >{{ $operation->cutting_speed_vc }}
                            {{ __("m/min") }}</flux:text
                        >
                    </div>
                    <div
                        class="bg-zinc-50 dark:bg-zinc-700 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4"
                    >
                        <flux:heading>
                            {{ __("Spindle speed") }}
                        </flux:heading>
                        <flux:text
                            >{{ $operation->spindle_speed_n }}
                            {{ __("rpm") }}</flux:text
                        >
                    </div>
                    <div
                        class="bg-zinc-50 dark:bg-zinc-700 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4"
                    >
                        <flux:heading>
                            {{ __("Feed per tooth") }}
                        </flux:heading>
                        <flux:text
                            >{{ $operation->feed_per_tooth_fz }}
                            {{ __("mm/tooth") }}</flux:text
                        >
                    </div>
                </div>

                <div class="flex w-full gap-4">
                    <div
                        class="bg-zinc-50 dark:bg-zinc-700 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4"
                    >
                        <flux:heading>
                            {{ __("Feed rate") }}
                        </flux:heading>
                        <flux:text
                            >{{ $operation->feed_rate_vf }}
                            {{ __("mm/min") }}</flux:text
                        >
                    </div>
                    <div
                        class="bg-zinc-50 dark:bg-zinc-700 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4"
                    >
                        <flux:heading>
                            {{ __("Depth of cut") }}
                        </flux:heading>
                        <flux:text
                            >{{ $operation->depth_of_cut_ap }}
                            {{ __("mm") }}</flux:text
                        >
                    </div>
                    <div
                        class="bg-zinc-50 dark:bg-zinc-700 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4"
                    >
                        <flux:heading>
                            {{ __("Width of cut") }}
                        </flux:heading>
                        <flux:text
                            >{{ $operation->width_of_cut_ae }}
                            {{ __("mm") }}</flux:text
                        >
                    </div>
                </div>
                @if($operation->g_code != '')
                <div
                    class="bg-zinc-50 dark:bg-zinc-700 flex w-full rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4"
                >
                    <flux:heading class="dark:text-white">
                        {{ __("G-code") }}
                    </flux:heading>
                    <flux:text>{{
                    $operation->g_code
                    }}</flux:text>
                </div>
                @endif
                <div
                    class="bg-blue-50 dark:bg-blue-700 flex w-full rounded-lg flex-col gap-2 border dark:border-blue-600 p-4"
                >
                    <flux:heading class="dark:text-white">
                        {{ __("Notes") }}
                    </flux:heading>
                    <flux:text>{{
                    $operation->notes
                    }}</flux:text>
                </div>
                <div
                    class="bg-zinc-50 dark:bg-zinc-700 flex w-full rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4"
                >
                    <flux:heading class="dark:text-white">
                        {{ __("Important safety note") }}
                    </flux:heading>
                    <flux:text>{{
                        __(
                            "AI-generated parameters and G-code are for informational purposes only. Always verify data with manufacturer catalogs and machine manuals. The user assumes full responsibility for any tool breakage, machine damage, or accidents resulting from the use of this data."
                        )
                    }}</flux:text>
                </div>
            </div>
        </div>
    </body>
</html>
