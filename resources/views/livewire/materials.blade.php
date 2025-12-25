<div>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div
            class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-xl border border-default"
        >
            <div
                class="px-6 py-5 w-full flex items-start flex-col gap-4 md:flex-row md:justify-between md:items-center"
            >
                <div class="text-left rtl:text-right">
                    <flux:heading size="lg">{{ __("Materials") }}</flux:heading>
                    <flux:text class="mt-1">{{
                        __("List of materials available in the system.")
                    }}</flux:text>
                </div>
                <flux:modal.trigger name="add-material">
                    <flux:button class="cursor-pointer" variant="primary">{{
                        __("Add Material")
                    }}</flux:button>
                </flux:modal.trigger>
            </div>
            <table class="w-full text-sm text-left rtl:text-right text-body">
                <thead
                    class="text-sm text-body bg-neutral-secondary-medium border-b border-t border-default-medium"
                >
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">
                            {{ __("Material name") }}
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            {{ __("Material category") }}
                        </th>

                        <th scope="col" class="px-6 py-3 font-medium">
                            {{ __("Material subcategory") }}
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            <span class="sr-only">{{ __("Actions") }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materials as $material)
                    <tr class="bg-neutral-primary-soft border-b border-default">
                        <th
                            scope="row"
                            class="px-6 py-4 font-medium text-heading whitespace-nowrap"
                        >
                            {{$material->name}}
                        </th>
                        <td class="px-6 py-4">
                            {{strtoupper($material->category)}}
                        </td>
                        <td class="px-6 py-4">
                            {{strtoupper($material->sub_category)}}
                        </td>

                        <td
                            class="px-6 py-4 text-right flex justify-end items-center gap-2"
                        >
                            <flux:button
                                wire:click="editMaterial({{ $material }})"
                                icon="pencil-square"
                                class="cursor-pointer text-blue-500 hover:text-blue-700"
                                >{{ __("Edit") }}</flux:button
                            >
                            <flux:button
                                wire:click="deleteMaterial({{ $material }})"
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
        {{ $materials->links() }}

        <!-- Add material modal -->
        <flux:modal name="add-material" class="w-3/4 md:w-lg">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{
                        __("Add Material")
                    }}</flux:heading>
                    <flux:text class="mt-2">{{
                        __(
                            "Enter details to create a new material in the system."
                        )
                    }}</flux:text>
                </div>

                <form
                    wire:submit="addMaterial"
                    class="w-full flex flex-col gap-4"
                >
                    <flux:input
                        wire:model="name"
                        label="{{ __('Material name') }}"
                        placeholder="{{ __('Display name') }}"
                    />
                    <flux:select
                        wire:model.live="category"
                        class="w-full"
                        label="{{ __('Material category (ISO)') }}"
                        placeholder="{{ __('Material category (ISO)') }}"
                    >
                        <flux:select.option value="p">{{
                            __("P (Steel)")
                        }}</flux:select.option>
                        <flux:select.option value="m">{{
                            __("M (Stainless Steel)")
                        }}</flux:select.option>
                        <flux:select.option value="k">{{
                            __("K (Cast Iron)")
                        }}</flux:select.option>
                        <flux:select.option value="n">{{
                            __("N (Non-ferrous / Aluminum)")
                        }}</flux:select.option>
                        <flux:select.option value="s">{{
                            __("S (Superalloys / Titanium)")
                        }}</flux:select.option>
                        <flux:select.option value="h">{{
                            __("H (Hardened Steel)")
                        }}</flux:select.option>
                    </flux:select>

                    {{-- Dynamiczne Podkategorie --}}
                    @if($category == 'p')
                    <flux:select
                        wire:model="sub_category"
                        label="{{ __('Subcategory (Steel)') }}"
                        placeholder="{{ __('Subcategory (Steel)') }}"
                    >
                        <flux:select.option value="p1">{{
                            __("P1 (Low Carbon)")
                        }}</flux:select.option>
                        <flux:select.option value="p2">{{
                            __("P2 (Medium Carbon)")
                        }}</flux:select.option>
                        <flux:select.option value="p3">{{
                            __("P3 (Alloy Steel - Low)")
                        }}</flux:select.option>
                        <flux:select.option value="p4">{{
                            __("P4 (Alloy Steel - High)")
                        }}</flux:select.option>
                        <flux:select.option value="p5">{{
                            __("P5 (Tool Steel)")
                        }}</flux:select.option>
                    </flux:select>
                    @elseif($category == 'm')
                    <flux:select
                        wire:model="sub_category"
                        label="{{ __('Subcategory (Stainless)') }}"
                        placeholder="{{ __('Subcategory (Stainless)') }}"
                    >
                        <flux:select.option value="m1">{{
                            __("M1 (Austenitic)")
                        }}</flux:select.option>
                        <flux:select.option value="m2">{{
                            __("M2 (Ferritic / Martensitic)")
                        }}</flux:select.option>
                        <flux:select.option value="m3">{{
                            __("M3 (Duplex)")
                        }}</flux:select.option>
                    </flux:select>
                    @elseif($category == 'k')
                    <flux:select
                        wire:model="sub_category"
                        label="{{ __('Subcategory (Cast Iron)') }}"
                        placeholder="{{ __('Subcategory (Cast Iron)') }}"
                    >
                        <flux:select.option value="k1">{{
                            __("K1 (Grey Cast Iron)")
                        }}</flux:select.option>
                        <flux:select.option value="k2">{{
                            __("K2 (Nodular Cast Iron)")
                        }}</flux:select.option>
                    </flux:select>
                    @elseif($category == 'n')
                    <flux:select
                        wire:model="sub_category"
                        label="{{ __('Subcategory (Non-ferrous)') }}"
                        placeholder="{{ __('Subcategory (Non-ferrous)') }}"
                    >
                        <flux:select.option value="n1">{{
                            __("N1 (Aluminium < 12% Si)")
                        }}</flux:select.option>
                        <flux:select.option value="n2">{{
                            __("N2 (Aluminium > 12% Si)")
                        }}</flux:select.option>
                        <flux:select.option value="n3">{{
                            __("N3 (Copper Alloys)")
                        }}</flux:select.option>
                    </flux:select>
                    @elseif($category == 's')
                    <flux:select
                        wire:model="sub_category"
                        label="{{ __('Subcategory (Superalloys)') }}"
                        placeholder="{{ __('Subcategory (Superalloys)') }}"
                    >
                        <flux:select.option value="s1">{{
                            __("S1 (Iron based)")
                        }}</flux:select.option>
                        <flux:select.option value="s2">{{
                            __("S2 (Nickel / Cobalt based)")
                        }}</flux:select.option>
                        <flux:select.option value="s3">{{
                            __("S3 (Titanium Alloys)")
                        }}</flux:select.option>
                    </flux:select>
                    @elseif($category == 'h')
                    <flux:select
                        wire:model="sub_category"
                        label="{{ __('Subcategory (Hardened)') }}"
                        placeholder="{{ __('Subcategory (Hardened)') }}"
                    >
                        <flux:select.option value="h1">{{
                            __("H1 (Hardened < 55 HRC)")
                        }}</flux:select.option>
                        <flux:select.option value="h2">{{
                            __("H2 (Hardened > 55 HRC)")
                        }}</flux:select.option>
                    </flux:select>
                    @endif
                    <div class="w-full flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-1/2">
                            <flux:select
                                wire:model="hardness_unit"
                                label="{{ __('Hardness unit') }}"
                                placeholder="{{ __('Hardness unit') }}"
                            >
                                <flux:select.option value="hb">{{
                                    __("HB")
                                }}</flux:select.option>
                                <flux:select.option value="hrc">{{
                                    __("HRC")
                                }}</flux:select.option>
                                <flux:select.option value="hv">{{
                                    __("HV")
                                }}</flux:select.option>
                            </flux:select>
                        </div>
                        <div class="w-full md:w-1/2">
                            <flux:input
                                wire:model="hardness_value"
                                label="{{ __('Hardness value') }}"
                                placeholder="{{ __('Hardness value') }}"
                            >
                            </flux:input>
                        </div>
                    </div>

                    <div class="flex">
                        <flux:spacer />
                        <flux:button
                            class="cursor-pointer"
                            type="submit"
                            variant="primary"
                            >{{ __("Add Material") }}</flux:button
                        >
                    </div>
                </form>
            </div>
        </flux:modal>

        <!-- Edit material modal -->
        <flux:modal name="edit-material" class="w-3/4 md:w-lg" flyout>
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{
                        __("Edit Material")
                    }}</flux:heading>
                    <flux:text class="mt-2">{{
                        __(
                            "Update material details such as name, category, or hardness."
                        )
                    }}</flux:text>
                </div>

                <form
                    wire:submit="updateMaterial"
                    class="w-full flex flex-col gap-4"
                >
                    <flux:input
                        wire:model="name"
                        label="{{ __('Material name') }}"
                        placeholder="{{ __('Display name') }}"
                    />
                    <flux:select
                        wire:model.live="category"
                        class="w-full"
                        label="{{ __('Material category (ISO)') }}"
                        placeholder="{{ __('Material category (ISO)') }}"
                    >
                        <flux:select.option value="p">{{
                            __("P (Steel)")
                        }}</flux:select.option>
                        <flux:select.option value="m">{{
                            __("M (Stainless Steel)")
                        }}</flux:select.option>
                        <flux:select.option value="k">{{
                            __("K (Cast Iron)")
                        }}</flux:select.option>
                        <flux:select.option value="n">{{
                            __("N (Non-ferrous / Aluminum)")
                        }}</flux:select.option>
                        <flux:select.option value="s">{{
                            __("S (Superalloys / Titanium)")
                        }}</flux:select.option>
                        <flux:select.option value="h">{{
                            __("H (Hardened Steel)")
                        }}</flux:select.option>
                    </flux:select>

                    {{-- Dynamiczne Podkategorie --}}
                    @if($category == 'p')
                    <flux:select
                        wire:model="sub_category"
                        label="{{ __('Subcategory (Steel)') }}"
                        placeholder="{{ __('Subcategory (Steel)') }}"
                    >
                        <flux:select.option value="p1">{{
                            __("P1 (Low Carbon)")
                        }}</flux:select.option>
                        <flux:select.option value="p2">{{
                            __("P2 (Medium Carbon)")
                        }}</flux:select.option>
                        <flux:select.option value="p3">{{
                            __("P3 (Alloy Steel - Low)")
                        }}</flux:select.option>
                        <flux:select.option value="p4">{{
                            __("P4 (Alloy Steel - High)")
                        }}</flux:select.option>
                        <flux:select.option value="p5">{{
                            __("P5 (Tool Steel)")
                        }}</flux:select.option>
                    </flux:select>
                    @elseif($category == 'm')
                    <flux:select
                        wire:model="sub_category"
                        label="{{ __('Subcategory (Stainless)') }}"
                        placeholder="{{ __('Subcategory (Stainless)') }}"
                    >
                        <flux:select.option value="m1">{{
                            __("M1 (Austenitic)")
                        }}</flux:select.option>
                        <flux:select.option value="m2">{{
                            __("M2 (Ferritic / Martensitic)")
                        }}</flux:select.option>
                        <flux:select.option value="m3">{{
                            __("M3 (Duplex)")
                        }}</flux:select.option>
                    </flux:select>
                    @elseif($category == 'k')
                    <flux:select
                        wire:model="sub_category"
                        label="{{ __('Subcategory (Cast Iron)') }}"
                        placeholder="{{ __('Subcategory (Cast Iron)') }}"
                    >
                        <flux:select.option value="k1">{{
                            __("K1 (Grey Cast Iron)")
                        }}</flux:select.option>
                        <flux:select.option value="k2">{{
                            __("K2 (Nodular Cast Iron)")
                        }}</flux:select.option>
                    </flux:select>
                    @elseif($category == 'n')
                    <flux:select
                        wire:model="sub_category"
                        label="{{ __('Subcategory (Non-ferrous)') }}"
                        placeholder="{{ __('Subcategory (Non-ferrous)') }}"
                    >
                        <flux:select.option value="n1">{{
                            __("N1 (Aluminium < 12% Si)")
                        }}</flux:select.option>
                        <flux:select.option value="n2">{{
                            __("N2 (Aluminium > 12% Si)")
                        }}</flux:select.option>
                        <flux:select.option value="n3">{{
                            __("N3 (Copper Alloys)")
                        }}</flux:select.option>
                    </flux:select>
                    @elseif($category == 's')
                    <flux:select
                        wire:model="sub_category"
                        label="{{ __('Subcategory (Superalloys)') }}"
                        placeholder="{{ __('Subcategory (Superalloys)') }}"
                    >
                        <flux:select.option value="s1">{{
                            __("S1 (Iron based)")
                        }}</flux:select.option>
                        <flux:select.option value="s2">{{
                            __("S2 (Nickel / Cobalt based)")
                        }}</flux:select.option>
                        <flux:select.option value="s3">{{
                            __("S3 (Titanium Alloys)")
                        }}</flux:select.option>
                    </flux:select>
                    @elseif($category == 'h')
                    <flux:select
                        wire:model="sub_category"
                        label="{{ __('Subcategory (Hardened)') }}"
                        placeholder="{{ __('Subcategory (Hardened)') }}"
                    >
                        <flux:select.option value="h1">{{
                            __("H1 (Hardened < 55 HRC)")
                        }}</flux:select.option>
                        <flux:select.option value="h2">{{
                            __("H2 (Hardened > 55 HRC)")
                        }}</flux:select.option>
                    </flux:select>
                    @endif
                    <div class="w-full flex flex-col md:flex-row gap-4">
                        <div class="w-full md:w-1/2">
                            <flux:select
                                wire:model="hardness_unit"
                                label="{{ __('Hardness unit') }}"
                                placeholder="{{ __('Hardness unit') }}"
                            >
                                <flux:select.option value="hb">{{
                                    __("HB")
                                }}</flux:select.option>
                                <flux:select.option value="hrc">{{
                                    __("HRC")
                                }}</flux:select.option>
                                <flux:select.option value="hv">{{
                                    __("HV")
                                }}</flux:select.option>
                            </flux:select>
                        </div>
                        <div class="w-full md:w-1/2">
                            <flux:input
                                wire:model="hardness_value"
                                label="{{ __('Hardness value') }}"
                                placeholder="{{ __('Hardness value') }}"
                            >
                            </flux:input>
                        </div>
                    </div>

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
