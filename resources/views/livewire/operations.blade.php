<div>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div
            class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-xl border dark:border-zinc-700"
        >
            <div
                class="px-6 py-5 w-full flex items-start flex-col gap-4 md:flex-row md:justify-between md:items-center"
            >
                <div class="text-left rtl:text-right">
                    <flux:heading size="lg">{{
                        __("Operations")
                    }}</flux:heading>
                    <flux:text class="mt-1">{{
                        __("List of operations available in the system.")
                    }}</flux:text>
                </div>
                <flux:modal.trigger name="add-operation">
                    <flux:button class="cursor-pointer" variant="primary">{{
                        __("Add operation")
                    }}</flux:button>
                </flux:modal.trigger>
            </div>
            <table class="w-full text-sm text-left rtl:text-right text-body">
                <thead
                    class="bg-zinc-100 dark:bg-zinc-700 text-sm text-body border-b border-t dark:border-zinc-700"
                >
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">
                            {{ __("Operation name") }}
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            {{ __("Operation tool") }}
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            {{ __("Operation material") }}
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            <span class="sr-only">{{ __("Actions") }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($operations as $operation)
                    <tr class="border-b dark:border-zinc-700">
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-heading whitespace-nowrap"
                        >
                            {{ $operation->name }}
                        </th>
                        <td class="px-6 py-4">
                            {{ $operation->tool->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $operation->material->name }}
                        </td>

                        <td
                            class="px-6 py-4 text-right flex justify-end items-center gap-2"
                        >
                            <flux:button
                                wire:click="seeOperation({{ $operation }})"
                                icon="eye"
                                class="cursor-pointer hover:text-blue-700 dark:hover:text-blue-400"
                                >{{ __("Details") }}</flux:button
                            >

                            <flux:button
                                wire:click="deleteOperation({{ $operation }})"
                                icon="trash"
                                class="cursor-pointer hover:text-red-700 dark:hover:text-red-400"
                                >{{ __("Delete") }}</flux:button
                            >
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $operations->links() }}

        <!-- Add operation modal -->
        <flux:modal name="add-operation" class="w-3/4 md:w-lg">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{
                        __("Add Operation")
                    }}</flux:heading>
                    <flux:text class="mt-2"
                        >{{
                            __(
                                "Enter the data, and AI will calculate the cutting parameters."
                            )
                        }}
                    </flux:text>
                </div>

                <form
                    wire:submit="addOperation"
                    class="w-full flex flex-col gap-4"
                >
                    <flux:input
                        wire:model="name"
                        label="{{ __('Operation name') }}"
                        placeholder="{{ __('Enter operation name') }}"
                        required
                    />
                    <flux:textarea
                        wire:model="description"
                        label="{{ __('Description') }}"
                        placeholder="{{ __('Enter operation description') }}"
                    />
                    <div class="w-full flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-1/2">
                            <flux:select
                                wire:model.live="tool_id"
                                label="{{ __('Tool') }}"
                                placeholder="{{ __('Select tool') }}"
                            >
                                @foreach($tools as $tool)
                                <flux:select.option
                                    wire:key="{{ $tool->id }}"
                                    value="{{ $tool->id }}"
                                    >{{ $tool->name }}</flux:select.option
                                >
                                @endforeach
                            </flux:select>
                        </div>
                        <div class="w-full md:w-1/2">
                            <flux:select
                                wire:model.live="material_id"
                                label="{{ __('Material') }}"
                                placeholder="{{ __('Select material') }}"
                            >
                                @foreach($materials as $material)
                                <flux:select.option
                                    wire:key="{{ $material->id }}"
                                    value="{{ $material->id }}"
                                    >{{ $material->name }}</flux:select.option
                                >
                                @endforeach
                            </flux:select>
                        </div>
                    </div>
                    @if($visible_answer == false)
                    <div class="flex">
                        <flux:spacer />
                        <flux:button
                            class="cursor-pointer w-full"
                            type="submit"
                            variant="primary"
                            >{{ __("Add operation") }}</flux:button
                        >
                    </div>
                    @endif
                </form>
                @if($visible_answer == true)
                <flux:separator text="{{ __('Calculated Parameters') }}" />
                <div class="flex w-full flex-col gap-4">
                    <div class="flex w-full gap-4">
                        <div
                            class="bg-zinc-50 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border p-4"
                        >
                            <flux:text>
                                {{ __("Cutting speed") }}
                            </flux:text>
                            <flux:heading
                                >{{ $cutting_speed_vc }}
                                {{ __("m/min") }}</flux:heading
                            >
                        </div>
                        <div
                            class="bg-zinc-50 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border p-4"
                        >
                            <flux:text>
                                {{ __("Spindle speed") }}
                            </flux:text>
                            <flux:heading
                                >{{ $spindle_speed_n }}
                                {{ __("rpm") }}</flux:heading
                            >
                        </div>
                        <div
                            class="bg-zinc-50 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border p-4"
                        >
                            <flux:text>
                                {{ __("Feed per tooth") }}
                            </flux:text>
                            <flux:heading
                                >{{ $feed_per_tooth_fz }}
                                {{ __("mm/tooth") }}</flux:heading
                            >
                        </div>
                    </div>

                    <div class="flex w-full gap-4">
                        <div
                            class="bg-zinc-50 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border p-4"
                        >
                            <flux:text>
                                {{ __("Feed rate") }}
                            </flux:text>
                            <flux:heading
                                >{{ $feed_rate_vf }}
                                {{ __("mm/min") }}</flux:heading
                            >
                        </div>
                        <div
                            class="bg-zinc-50 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border p-4"
                        >
                            <flux:text>
                                {{ __("Depth of cut") }}
                            </flux:text>
                            <flux:heading
                                >{{ $depth_of_cut_ap }}
                                {{ __("mm") }}</flux:heading
                            >
                        </div>
                        <div
                            class="bg-zinc-50 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border p-4"
                        >
                            <flux:text>
                                {{ __("Width of cut") }}
                            </flux:text>
                            <flux:heading
                                >{{ $width_of_cut_ae }}
                                {{ __("mm") }}</flux:heading
                            >
                        </div>
                    </div>
                    <div
                        class="bg-blue-50 border-blue-100 flex w-full rounded-lg flex-col gap-2 border p-4"
                    >
                        <flux:text>
                            {{ __("Notes") }}
                        </flux:text>
                        <flux:text class="text-zinc-800 dark:text-white">{{
                            $notes
                        }}</flux:text>
                    </div>
                </div>
                @endif
            </div>
        </flux:modal>

        <!-- See operation modal -->
        <flux:modal name="see-operation" class="w-3/4 md:w-lg" flyout>
            <div class="space-y-6">
                <form class="w-full flex flex-col gap-4">
                    <flux:input
                        wire:model="name"
                        label="{{ __('Operation name') }}"
                        placeholder="{{ __('Enter operation name') }}"
                        disabled
                    />
                    <flux:textarea
                        wire:model="description"
                        label="{{ __('Description') }}"
                        placeholder="{{ __('Enter operation description') }}"
                        disabled
                    />
                    <div class="w-full flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-1/2">
                            <flux:select
                                wire:model.live="tool_id"
                                label="{{ __('Tool') }}"
                                placeholder="{{ __('Select tool') }}"
                                disabled
                            >
                                @foreach($tools as $tool)
                                <flux:select.option
                                    wire:key="{{ $tool->id }}"
                                    value="{{ $tool->id }}"
                                    >{{ $tool->name }}</flux:select.option
                                >
                                @endforeach
                            </flux:select>
                        </div>
                        <div class="w-full md:w-1/2">
                            <flux:select
                                wire:model.live="material_id"
                                label="{{ __('Material') }}"
                                placeholder="{{ __('Select material') }}"
                                disabled
                            >
                                @foreach($materials as $material)
                                <flux:select.option
                                    wire:key="{{ $material->id }}"
                                    value="{{ $material->id }}"
                                    >{{ $material->name }}</flux:select.option
                                >
                                @endforeach
                            </flux:select>
                        </div>
                    </div>
                </form>
                <flux:separator text="{{ __('Calculated Parameters') }}" />
                <div class="flex w-full flex-col gap-4">
                    <div class="flex w-full gap-4">
                        <div
                            class="bg-zinc-50 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border p-4"
                        >
                            <flux:text>
                                {{ __("Cutting speed") }}
                            </flux:text>
                            <flux:heading
                                >{{ $cutting_speed_vc }}
                                {{ __("m/min") }}</flux:heading
                            >
                        </div>
                        <div
                            class="bg-zinc-50 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border p-4"
                        >
                            <flux:text>
                                {{ __("Spindle speed") }}
                            </flux:text>
                            <flux:heading
                                >{{ $spindle_speed_n }}
                                {{ __("rpm") }}</flux:heading
                            >
                        </div>
                        <div
                            class="bg-zinc-50 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border p-4"
                        >
                            <flux:text>
                                {{ __("Feed per tooth") }}
                            </flux:text>
                            <flux:heading
                                >{{ $feed_per_tooth_fz }}
                                {{ __("mm/tooth") }}</flux:heading
                            >
                        </div>
                    </div>

                    <div class="flex w-full gap-4">
                        <div
                            class="bg-zinc-50 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border p-4"
                        >
                            <flux:text>
                                {{ __("Feed rate") }}
                            </flux:text>
                            <flux:heading
                                >{{ $feed_rate_vf }}
                                {{ __("mm/min") }}</flux:heading
                            >
                        </div>
                        <div
                            class="bg-zinc-50 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border p-4"
                        >
                            <flux:text>
                                {{ __("Depth of cut") }}
                            </flux:text>
                            <flux:heading
                                >{{ $depth_of_cut_ap }}
                                {{ __("mm") }}</flux:heading
                            >
                        </div>
                        <div
                            class="bg-zinc-50 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border p-4"
                        >
                            <flux:text>
                                {{ __("Width of cut") }}
                            </flux:text>
                            <flux:heading
                                >{{ $width_of_cut_ae }}
                                {{ __("mm") }}</flux:heading
                            >
                        </div>
                    </div>
                    <div
                        class="bg-blue-50 border-blue-100 flex w-full rounded-lg flex-col gap-2 border p-4"
                    >
                        <flux:text>
                            {{ __("Notes") }}
                        </flux:text>
                        <flux:text class="text-zinc-800 dark:text-white">{{
                            $notes
                        }}</flux:text>
                    </div>
                </div>
            </div>
        </flux:modal>
    </div>
</div>
