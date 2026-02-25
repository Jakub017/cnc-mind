<div>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-xl border dark:border-zinc-700">
            <div class="px-6 py-5 w-full flex items-start flex-col gap-4 md:flex-row md:justify-between md:items-center">
                <div class="text-left rtl:text-right">
                    <flux:heading size="lg">{{ __('Operations') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('List of operations available in the system.') }}</flux:text>
                </div>
                <flux:button wire:click="showAddOperationModal" class="cursor-pointer" variant="primary">
                    {{ __('Add operation') }}
                </flux:button>
            </div>
            <table class="w-full text-sm text-left rtl:text-right text-body">
                <thead class="bg-zinc-100 dark:bg-zinc-700 text-sm text-body border-b border-t dark:border-zinc-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">
                            {{ __('Operation name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            {{ __('Operation tool') }}
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            {{ __('Operation material') }}
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            <span class="sr-only">{{ __('Actions') }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($operations as $operation)
                        @if ($operation->status == 'pending')
                            <flux:skeleton.group wire:poll animate="shimmer" class="">
                                <tr class="border-b dark:border-zinc-700 relative">
                                    <th class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                        <flux:skeleton.line class="w-full" />
                                    </th>
                                    <td class="px-6 py-4">
                                        <flux:skeleton.line class="w-full" />
                                    </td>
                                    <td class="px-6 py-4">
                                        <flux:skeleton.line class="w-full" />
                                    </td>

                                    <td class="px-6 py-4 text-right flex justify-end items-center gap-2">
                                        <flux:skeleton.line class="w-[117px]" />
                                        <flux:skeleton.line class="w-[128px]" />
                                        <flux:skeleton.line class="w-[94px]" />
                                        <flux:skeleton.line class="w-[85px]" />
                                    </td>
                                </tr>
                            </flux:skeleton.group>
                        @else
                            <tr class="border-b dark:border-zinc-700">
                                <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                    {{ $operation->name }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ $operation->tool->name }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $operation->material->name }}
                                </td>

                                <td class="px-6 py-4 text-right flex justify-end items-center gap-2">
                                    <flux:button wire:click="seeOperation({{ $operation }})" icon="eye"
                                        class="cursor-pointer hover:text-blue-700 dark:hover:text-blue-400">
                                        {{ __('Details') }}</flux:button>
                                    <flux:button href="{{ route('operation.download', $operation) }}" target="_blank"
                                        icon="arrow-down-tray"
                                        class="cursor-pointer hover:text-blue-700 dark:hover:text-blue-400">
                                        {{ __('Download') }} pdf</flux:button>
                                    <flux:button wire:click="editOperation({{ $operation }})" icon="pencil-square"
                                        class="cursor-pointer hover:text-blue-700 dark:hover:text-blue-400">
                                        {{ __('Edit') }}</flux:button>
                                    <flux:button wire:click="deleteOperation({{ $operation }})" icon="trash"
                                        class="cursor-pointer hover:text-red-700 dark:hover:text-red-400">
                                        {{ __('Delete') }}</flux:button>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $operations->links() }}

        <!-- Add operation modal -->
        <flux:modal name="add-operation" class="w-3/4 md:w-lg">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Add operation') }}</flux:heading>
                    <flux:text class="mt-2">
                        {{ __('Enter the data, and AI will calculate the cutting parameters.') }}
                    </flux:text>
                </div>

                <form wire:submit="addOperation" class="w-full flex flex-col gap-4">
                    <flux:input wire:model="name" label="{{ __('Operation name') }}"
                        placeholder="{{ __('Enter operation name') }}" required />
                    <flux:textarea wire:model="description" label="{{ __('Description') }}"
                        placeholder="{{ __('Enter operation description') }}" />
                    <div class="w-full flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-1/2">
                            <flux:select wire:model.live="tool_id" label="{{ __('Tool') }}"
                                placeholder="{{ __('Select tool') }}">
                                @foreach ($tools as $tool)
                                    <flux:select.option wire:key="{{ $tool->id }}" value="{{ $tool->id }}">
                                        {{ $tool->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                        <div class="w-full md:w-1/2">
                            <flux:select wire:model.live="material_id" label="{{ __('Material') }}"
                                placeholder="{{ __('Select material') }}">
                                @foreach ($materials as $material)
                                    <flux:select.option wire:key="{{ $material->id }}" value="{{ $material->id }}">
                                        {{ $material->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                    </div>
                    @if (count($files) > 0)
                        <div class="w-full">
                            <flux:select wire:model.live="file_id" label="{{ __('File') }}">
                                <flux:select.option value="">{{ __('Select file') }}</flux:select.option>
                                @foreach ($files as $file)
                                    <flux:select.option wire:key="{{ $file->id }}" value="{{ $file->id }}">
                                        {{ $file->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>
                    @endif

                    <div class="flex flex-col gap-4">
                        <flux:field variant="inline">
                            <flux:checkbox wire:model="want_g_code" />
                            <flux:label>
                                {{ __('Include G-code generation for this operation') }}
                            </flux:label>
                        </flux:field>
                        <flux:button class="cursor-pointer w-full" type="submit" variant="primary">
                            {{ __('Add operation') }}</flux:button>
                    </div>

                </form>

            </div>
        </flux:modal>

        <!-- Edit operation modal -->
        @if ($current_operation)
            <flux:modal name="edit-operation" class="w-3/4 md:w-xl" flyout>
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('Edit operation') }}</flux:heading>
                        <flux:text class="mt-2">
                            {{ __('Adjust operation data and cutting parameters as needed.') }}
                        </flux:text>
                    </div>

                    <form wire:submit="updateOperation" class="w-full flex flex-col gap-4">
                        <flux:input wire:model="name" label="{{ __('Operation name') }}"
                            placeholder="{{ __('Enter operation name') }}" required />
                        <flux:textarea wire:model="description" label="{{ __('Description') }}"
                            placeholder="{{ __('Enter operation description') }}" />

                        <div class="w-full flex flex-col md:flex-row gap-4">
                            <div class="w-full md:w-1/2">
                                <flux:input label="{{ __('Tool') }}" disabled
                                    value="{{ $current_operation->tool->name }}" />
                            </div>
                            <div class="w-full md:w-1/2">
                                <flux:input label="{{ __('Material') }}" disabled
                                    value="{{ $current_operation->material->name }}" />
                            </div>
                        </div>
                        @if ($current_operation->file != null)
                            <div class="w-full">
                                <flux:input label="{{ __('File') }}" disabled
                                    value="{{ $current_operation->file->name }}" />
                            </div>
                        @endif

                        <div class="w-full flex flex-col md:flex-row gap-4">
                            <div class="w-full md:w-1/2">
                                <flux:input wire:model="cutting_speed_vc"
                                    label="{{ __('Cutting speed') }} [{{ __('m/min') }}]"
                                    placeholder="{{ __('Enter cutting speed') }}" required />
                            </div>
                            <div class="w-full md:w-1/2">
                                <flux:input wire:model="spindle_speed_n"
                                    label="{{ __('Spindle speed') }} [{{ __('rpm') }}]"
                                    placeholder="{{ __('Enter spindle speed') }}" required />
                            </div>
                        </div>

                        <div class="w-full flex flex-col md:flex-row gap-4">
                            @if ($feed_per_tooth_fz != null)
                                <div class="w-full md:w-1/2">
                                    <flux:input wire:model="feed_per_tooth_fz"
                                        label="{{ __('Feed per tooth') }} [{{ __('mm/tooth') }}]"
                                        placeholder="{{ __('Enter feed per tooth') }}" required />
                                </div>
                                @endif @if ($feed_per_revolution_fn != null)
                                    <div class="w-full md:w-1/2">
                                        <flux:input wire:model="feed_per_revolution_fn"
                                            label="{{ __('Feed per revolution') }} [{{ __('mm/rev') }}]"
                                            placeholder="{{ __('Enter feed per revolution') }}" required />
                                    </div>
                                @endif
                                <div class="w-full md:w-1/2">
                                    <flux:input wire:model="feed_rate_vf"
                                        label="{{ __('Feed rate') }} [{{ __('mm/min') }}]"
                                        placeholder="{{ __('Enter feed rate') }}" required />
                                </div>
                        </div>
                        <div class="w-full flex flex-col md:flex-row gap-4">
                            <div class="w-full md:w-1/2">
                                <flux:input wire:model="depth_of_cut_ap"
                                    label="{{ __('Depth of cut') }} [{{ __('mm') }}]"
                                    placeholder="{{ __('Enter depth of cut') }}" required />
                            </div>
                            @if ($width_of_cut_ae != null)
                                <div class="w-full md:w-1/2">
                                    <flux:input wire:model="width_of_cut_ae"
                                        label="{{ __('Width of cut') }} [{{ __('mm') }}]"
                                        placeholder="{{ __('Enter width of cut') }}" required />
                                </div>
                                @endif @if ($theoretical_roughness_ra != null)
                                    <div class="w-full md:w-1/2">
                                        <flux:input wire:model="theoretical_roughness_ra"
                                            label="{{ __('Theoretical roughness') }} [{{ __('μm') }}]"
                                            placeholder="{{ __('Enter theoretical roughness') }}" required />
                                    </div>
                                @endif
                        </div>
                        @if ($g_code != '')
                            <flux:textarea wire:model="g_code" label="{{ __('G-code') }}"
                                placeholder="{{ __('Enter G-code') }}" />
                        @endif
                        <flux:textarea wire:model="notes" label="{{ __('Notes') }}"
                            placeholder="{{ __('Enter notes') }}" />
                        <div class="flex">
                            <flux:spacer />
                            <flux:button class="cursor-pointer" type="submit" variant="primary">
                                {{ __('Save Changes') }}</flux:button>
                        </div>
                    </form>
                </div>
            </flux:modal>
        @endif

        <!-- See operation modal -->
        @if ($current_operation)
            <flux:modal name="see-operation" class="w-3/4 md:w-xl" flyout>
                <div class="space-y-6">
                    <div class="w-full flex flex-col gap-4">
                        <flux:input wire:model="name" label="{{ __('Operation name') }}"
                            placeholder="{{ __('Enter operation name') }}" disabled />
                        <flux:textarea wire:model="description" label="{{ __('Description') }}"
                            placeholder="{{ __('Enter operation description') }}" disabled />
                        <div class="w-full flex flex-col md:flex-row gap-4">

                            <div class="w-full md:w-1/2">
                                <flux:input label="{{ __('Tool') }}" disabled
                                    value="{{ $current_operation->tool->name }}" />
                            </div>

                            <div class="w-full md:w-1/2">
                                <flux:input label="{{ __('Material') }}" disabled
                                    value="{{ $current_operation->material->name }}" />
                            </div>

                        </div>

                        @if ($current_operation->file != null)
                            <div class="w-full">
                                <flux:input label="{{ __('File') }}" disabled
                                    value="{{ $current_operation->file->name }}" />
                            </div>
                        @endif

                    </div>
                    <flux:separator text="{{ __('Calculated Parameters') }}" />
                    <div class="flex w-full flex-col gap-4">
                        <div class="flex w-full flex-col md:flex-row gap-4">
                            <div
                                class="bg-zinc-50 dark:bg-zinc-700 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4">
                                <flux:heading>
                                    {{ __('Cutting speed') }}
                                </flux:heading>
                                <flux:text>{{ $cutting_speed_vc }}
                                    {{ __('m/min') }}</flux:text>
                            </div>
                            <div
                                class="bg-zinc-50 dark:bg-zinc-700 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4">
                                <flux:heading>
                                    {{ __('Spindle speed') }}
                                </flux:heading>
                                <flux:text>{{ $spindle_speed_n }}
                                    {{ __('rpm') }}</flux:text>
                            </div>
                            @if ($feed_per_tooth_fz != null)
                                <div
                                    class="bg-zinc-50 dark:bg-zinc-700 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4">
                                    <flux:heading>
                                        {{ __('Feed per tooth') }}
                                    </flux:heading>
                                    <flux:text>{{ $feed_per_tooth_fz }}
                                        {{ __('mm/tooth') }}</flux:text>
                                </div>
                                @endif @if ($feed_per_revolution_fn != null)
                                    <div
                                        class="bg-zinc-50 dark:bg-zinc-700 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4">
                                        <flux:heading>
                                            {{ __('Feed per revolution') }}
                                        </flux:heading>
                                        <flux:text>{{ $feed_per_revolution_fn }}
                                            {{ __('mm/rev') }}</flux:text>
                                    </div>
                                @endif
                        </div>

                        <div class="flex w-full flex-col md:flex-row gap-4">
                            <div
                                class="bg-zinc-50 dark:bg-zinc-700 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4">
                                <flux:heading>
                                    {{ __('Feed rate') }}
                                </flux:heading>
                                <flux:text>{{ $feed_rate_vf }}
                                    {{ __('mm/min') }}</flux:text>
                            </div>
                            <div
                                class="bg-zinc-50 dark:bg-zinc-700 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4">
                                <flux:heading>
                                    {{ __('Depth of cut') }}
                                </flux:heading>
                                <flux:text>{{ $depth_of_cut_ap }}
                                    {{ __('mm') }}</flux:text>
                            </div>
                            @if ($width_of_cut_ae != null)
                                <div
                                    class="bg-zinc-50 dark:bg-zinc-700 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4">
                                    <flux:heading>
                                        {{ __('Width of cut') }}
                                    </flux:heading>
                                    <flux:text>{{ $width_of_cut_ae }}
                                        {{ __('mm') }}</flux:text>
                                </div>
                                @endif @if ($theoretical_roughness_ra != null)
                                    <div
                                        class="bg-zinc-50 dark:bg-zinc-700 flex w-full md:w-1/3 rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4">
                                        <flux:heading>
                                            {{ __('Theoretical roughness') }}
                                        </flux:heading>
                                        <flux:text>{{ $theoretical_roughness_ra }}
                                            {{ __('μm') }}</flux:text>
                                    </div>
                                @endif
                        </div>
                        @if ($g_code != '')
                            <div
                                class="bg-zinc-50 dark:bg-zinc-700 flex w-full rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4">
                                <flux:heading>
                                    {{ __('G-code') }}
                                </flux:heading>
                                <flux:text>{{ $g_code }}</flux:text>
                            </div>
                        @endif
                        <div
                            class="bg-blue-50 dark:bg-blue-700 flex w-full rounded-lg flex-col gap-2 border dark:border-blue-600 p-4">
                            <flux:heading class="dark:text-white">
                                {{ __('Notes') }}
                            </flux:heading>
                            <flux:text class="dark:text-white">{{ $notes }}</flux:text>
                        </div>
                        <div
                            class="bg-zinc-50 dark:bg-zinc-700 flex w-full rounded-lg flex-col gap-2 border dark:border-zinc-600 p-4">
                            <flux:heading>
                                {{ __('Important safety note') }}
                            </flux:heading>
                            <flux:text>
                                {{ __(
                                    'AI-generated parameters and G-code are for informational purposes only. Always verify data with manufacturer catalogs and machine manuals. The user assumes full responsibility for any tool breakage, machine damage, or accidents resulting from the use of this data.',
                                ) }}
                            </flux:text>
                        </div>
                    </div>
                </div>
            </flux:modal>
        @endif
    </div>
</div>
