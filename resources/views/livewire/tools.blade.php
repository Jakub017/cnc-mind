<div>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div
            class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-xl border border-default"
        >
            <div
                class="px-6 py-5 w-full flex items-start flex-col gap-4 md:flex-row md:justify-between md:items-center"
            >
                <div class="text-left rtl:text-right">
                    <flux:heading size="lg">{{ __("Tools") }}</flux:heading>
                    <flux:text class="mt-1">{{
                        __("List of tools available in the system.")
                    }}</flux:text>
                </div>
                <flux:modal.trigger name="add-tool">
                    <flux:button class="cursor-pointer" variant="primary">{{
                        __("Add Tool")
                    }}</flux:button>
                </flux:modal.trigger>
            </div>
            <table class="w-full text-sm text-left rtl:text-right text-body">
                <thead
                    class="text-sm text-body bg-neutral-secondary-medium border-b border-t border-default-medium"
                >
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">
                            {{ __("Tool name") }}
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            {{ __("Tool type") }}
                        </th>

                        <th scope="col" class="px-6 py-3 font-medium">
                            {{ __("Tool material") }}
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            <span class="sr-only">{{ __("Actions") }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tools as $tool)
                    <tr class="bg-neutral-primary-soft border-b border-default">
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-heading whitespace-nowrap"
                        >
                            {{$tool->name}}
                        </th>
                        <td class="px-6 py-4">{{ $tool->typeLabel() }}</td>
                        <td class="px-6 py-4">{{ $tool->materialLabel() }}</td>

                        <td
                            class="px-6 py-4 text-right flex justify-end items-center gap-2"
                        >
                            <flux:button
                                wire:click="editTool({{ $tool }})"
                                icon="pencil-square"
                                class="cursor-pointer text-blue-500 hover:text-blue-700"
                                >{{ __("Edit") }}</flux:button
                            >
                            <flux:button
                                wire:click="deleteTool({{ $tool }})"
                                icon="trash"
                                class="cursor-pointer text-red-500 hover:text-red-700"
                                >{{ __("Delete") }}</flux:button
                            >
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $tools->links() }}

        <!-- Add tool modal -->
        <flux:modal name="add-tool" class="w-3/4 md:w-lg">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __("Add Tool") }}</flux:heading>
                    <flux:text class="mt-2">{{
                        __(
                            "Enter technical parameters to add a new tool to the inventory."
                        )
                    }}</flux:text>
                </div>

                <form wire:submit="addTool" class="w-full flex flex-col gap-4">
                    <flux:input
                        wire:model="name"
                        label="{{ __('Tool name') }}"
                        placeholder="{{ __('Display name') }}"
                    />
                    <div class="w-full flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-1/2">
                            <flux:select
                                wire:model.live="type"
                                class="w-full"
                                label="{{ __('Tool type') }}"
                                placeholder="{{ __('Tool type') }}"
                            >
                                <flux:select.option value="end_mill">{{
                                    __("End mill")
                                }}</flux:select.option>
                                <flux:select.option value="turning_tool">{{
                                    __("Turning tool")
                                }}</flux:select.option>
                                <flux:select.option value="drill">{{
                                    __("Drill")
                                }}</flux:select.option>
                                <flux:select.option value="face_mill">{{
                                    __("Face mill")
                                }}</flux:select.option>
                                <flux:select.option value="center_drill">{{
                                    __("Center drill")
                                }}</flux:select.option>
                            </flux:select>
                        </div>

                        <div class="w-full md:w-1/2">
                            <flux:select
                                wire:model="material"
                                class="w-full"
                                label="{{ __('Tool material') }}"
                                placeholder="{{ __('Tool material') }}"
                            >
                                <flux:select.option value="solid_carbide">{{
                                    __("Solid Carbide")
                                }}</flux:select.option>
                                <flux:select.option value="hss">{{
                                    __("HSS")
                                }}</flux:select.option>
                                <flux:select.option value="carbide_insert">{{
                                    __("Carbide (Insert)")
                                }}</flux:select.option>
                                <flux:select.option value="pcd">{{
                                    __("PCD (Diamond)")
                                }}</flux:select.option>
                                <flux:select.option value="ceramic">{{
                                    __("Ceramic")
                                }}</flux:select.option>
                            </flux:select>
                        </div>
                    </div>
                    @if($type == "turning_tool")
                    <div class="w-full flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-1/2">
                            <flux:select
                                wire:model="insert_shape"
                                class="w-full"
                                label="{{ __('Insert shape') }}"
                                placeholder="{{ __('Insert shape') }}"
                            >
                                <flux:select.option value="c">{{
                                    __("C (80° Rhombic)")
                                }}</flux:select.option>
                                <flux:select.option value="d">{{
                                    __("D (55° Rhombic)")
                                }}</flux:select.option>
                                <flux:select.option value="t">{{
                                    __("T (Triangle)")
                                }}</flux:select.option>
                                <flux:select.option value="w">{{
                                    __("W (Trigon)")
                                }}</flux:select.option>
                                <flux:select.option value="s">{{
                                    __("S (Square)")
                                }}</flux:select.option>
                                <flux:select.option value="v">{{
                                    __("V (35° Rhombic)")
                                }}</flux:select.option>
                                <flux:select.option value="r">{{
                                    __("R (Round)")
                                }}</flux:select.option>
                            </flux:select>
                        </div>

                        <div class="w-full md:w-1/2">
                            <flux:input
                                wire:model="insert_code"
                                class="w-full"
                                label="{{ __('Insert code') }}"
                                placeholder="{{ __('Insert code') }}"
                            />
                        </div>
                    </div>
                    @endif @if($type != "turning_tool")
                    <div class="w-full flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-1/2">
                            <flux:input
                                wire:model="diameter"
                                class="w-full"
                                label="{{ __('Diameter') }}"
                                placeholder="{{ __('Tool diameter') }}"
                            />
                        </div>

                        <div class="w-full md:w-1/2">
                            <flux:select
                                wire:model="flutes"
                                class="w-full"
                                label="{{ __('Flutes') }}"
                                placeholder="{{ __('Flutes count') }}"
                            >
                                <flux:select.option value="1"
                                    >1 {{ __("Flute") }}</flux:select.option
                                >
                                <flux:select.option value="2"
                                    >2 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="3"
                                    >3 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="4"
                                    >4 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="5"
                                    >5 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="6"
                                    >6 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="7"
                                    >7 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="8"
                                    >8 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="9"
                                    >9 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="10"
                                    >10 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="11"
                                    >11 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="12"
                                    >12 {{ __("Flutes") }}</flux:select.option
                                >
                            </flux:select>
                        </div>
                    </div>
                    @endif
                    <div class="flex">
                        <flux:spacer />
                        <flux:button
                            class="cursor-pointer"
                            type="submit"
                            variant="primary"
                            >{{ __("Add Tool") }}</flux:button
                        >
                    </div>
                </form>
            </div>
        </flux:modal>

        <!-- Edit tool modal -->
        <flux:modal name="edit-tool" class="w-3/4 md:w-lg" flyout>
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __("Edit Tool") }}</flux:heading>
                    <flux:text class="mt-2">{{
                        __("Modify tool parameters, dimensions, or type.")
                    }}</flux:text>
                </div>

                <form
                    wire:submit="updateTool"
                    class="w-full flex flex-col gap-4"
                >
                    <flux:input
                        wire:model="name"
                        label="{{ __('Tool name') }}"
                        placeholder="{{ __('Display name') }}"
                    />
                    <div class="w-full flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-1/2">
                            <flux:select
                                wire:model.live="type"
                                class="w-full"
                                label="{{ __('Tool type') }}"
                                placeholder="{{ __('Tool type') }}"
                            >
                                <flux:select.option value="end_mill">{{
                                    __("End mill")
                                }}</flux:select.option>
                                <flux:select.option value="turning_tool">{{
                                    __("Turning tool")
                                }}</flux:select.option>
                                <flux:select.option value="drill">{{
                                    __("Drill")
                                }}</flux:select.option>
                                <flux:select.option value="face_mill">{{
                                    __("Face mill")
                                }}</flux:select.option>
                                <flux:select.option value="center_drill">{{
                                    __("Center drill")
                                }}</flux:select.option>
                            </flux:select>
                        </div>

                        <div class="w-full md:w-1/2">
                            <flux:select
                                wire:model="material"
                                class="w-full"
                                label="{{ __('Tool material') }}"
                                placeholder="{{ __('Tool material') }}"
                            >
                                <flux:select.option value="solid_carbide">{{
                                    __("Solid Carbide")
                                }}</flux:select.option>
                                <flux:select.option value="hss">{{
                                    __("HSS")
                                }}</flux:select.option>
                                <flux:select.option value="carbide_insert">{{
                                    __("Carbide (Insert)")
                                }}</flux:select.option>
                                <flux:select.option value="pcd">{{
                                    __("PCD (Diamond)")
                                }}</flux:select.option>
                                <flux:select.option value="ceramic">{{
                                    __("Ceramic")
                                }}</flux:select.option>
                            </flux:select>
                        </div>
                    </div>
                    @if($type == "turning_tool")
                    <div class="w-full flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-1/2">
                            <flux:select
                                wire:model="insert_shape"
                                class="w-full"
                                label="{{ __('Insert shape') }}"
                                placeholder="{{ __('Insert shape') }}"
                            >
                                <flux:select.option value="c">{{
                                    __("C (80° Rhombic)")
                                }}</flux:select.option>
                                <flux:select.option value="d">{{
                                    __("D (55° Rhombic)")
                                }}</flux:select.option>
                                <flux:select.option value="t">{{
                                    __("T (Triangle)")
                                }}</flux:select.option>
                                <flux:select.option value="w">{{
                                    __("W (Trigon)")
                                }}</flux:select.option>
                                <flux:select.option value="s">{{
                                    __("S (Square)")
                                }}</flux:select.option>
                                <flux:select.option value="v">{{
                                    __("V (35° Rhombic)")
                                }}</flux:select.option>
                                <flux:select.option value="r">{{
                                    __("R (Round)")
                                }}</flux:select.option>
                            </flux:select>
                        </div>

                        <div class="w-full md:w-1/2">
                            <flux:input
                                wire:model="insert_code"
                                class="w-full"
                                label="{{ __('Insert code') }}"
                                placeholder="{{ __('Insert code') }}"
                            />
                        </div>
                    </div>
                    @endif @if($type != "turning_tool")
                    <div class="w-full flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-1/2">
                            <flux:input
                                wire:model="diameter"
                                class="w-full"
                                label="{{ __('Diameter') }}"
                                placeholder="{{ __('Tool diameter') }}"
                            />
                        </div>

                        <div class="w-full md:w-1/2">
                            <flux:select
                                wire:model="flutes"
                                class="w-full"
                                label="{{ __('Flutes') }}"
                                placeholder="{{ __('Flutes count') }}"
                            >
                                <flux:select.option value="1"
                                    >1 {{ __("Flute") }}</flux:select.option
                                >
                                <flux:select.option value="2"
                                    >2 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="3"
                                    >3 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="4"
                                    >4 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="5"
                                    >5 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="6"
                                    >6 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="7"
                                    >7 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="8"
                                    >8 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="9"
                                    >9 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="10"
                                    >10 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="11"
                                    >11 {{ __("Flutes") }}</flux:select.option
                                >
                                <flux:select.option value="12"
                                    >12 {{ __("Flutes") }}</flux:select.option
                                >
                            </flux:select>
                        </div>
                    </div>
                    @endif
                    <div class="flex">
                        <flux:spacer />
                        <flux:button
                            class="cursor-pointer"
                            type="submit"
                            variant="primary"
                            >{{ __("Save Changes") }}</flux:button
                        >
                    </div>
                </form>
            </div>
        </flux:modal>
    </div>
</div>
