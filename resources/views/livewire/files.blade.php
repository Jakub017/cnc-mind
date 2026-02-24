<div>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-xl border dark:border-zinc-700">
            <div class="px-6 py-5 w-full flex items-start flex-col gap-4 md:flex-row md:justify-between md:items-center">
                <div class="text-left rtl:text-right">
                    <flux:heading size="lg">{{ __('Files') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('List of files available in the system.') }}</flux:text>
                </div>
                <flux:button class="cursor-pointer" wire:click="openModal" variant="primary">{{ __('Add file') }}
                </flux:button>
            </div>
            <table class="w-full text-sm text-left rtl:text-right text-body">
                <thead class="bg-zinc-100 dark:bg-zinc-700 text-sm text-body border-b border-t dark:border-zinc-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">
                            {{ __('File name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            {{ __('File size') }}
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            {{ __('Status') }}
                        </th>
                        <th scope="col" class="px-6 py-3 font-medium">
                            <span class="sr-only">{{ __('Actions') }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($files as $file)
                        <tr class="border-b dark:border-zinc-700">
                            <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                {{ $file->name }}
                            </th>
                            <td class="px-6 py-4">
                                {{ round($file->size / 1024 / 1024, 2) }} MB
                            </td>
                            <td class="px-6 py-4" wire:poll.5000ms>
                                @if ($file->status == 'indexing')
                                    <flux:badge color="yellow" class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-yellow-800 dark:bg-yellow-200 rounded-full animate-ping">
                                        </div> {{ __('Indexing') }}
                                    </flux:badge>
                                @elseif($file->status == 'indexed')
                                    <flux:badge color="green">{{ __('Indexed') }}</flux:badge>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right flex justify-end items-center gap-2">
                                <flux:button href="{{ route('file.download', $file) }}" icon="arrow-down-tray"
                                    class="cursor-pointer hover:text-blue-700 dark:hover:text-blue-400">
                                    {{ __('Download') }}</flux:button>

                                <flux:button wire:click="deleteFile({{ $file }})" icon="trash"
                                    class="cursor-pointer hover:text-red-700 dark:hover:text-red-400">
                                    {{ __('Delete') }}</flux:button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $files->links() }}

        <!-- Add file modal -->
        <flux:modal name="add-file" class="w-3/4 md:w-md">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Add File') }}</flux:heading>
                    <flux:text class="mt-2">{{ __('Upload a file to add it to the system.') }}</flux:text>
                </div>
                <form class="w-full flex flex-col gap-2">
                    <flux:input type="file" wire:model.live="file" label="{{ __('File') }}" />
                    <flux:text variant="subtle">
                        {{ __('Supported formats: .pdf, .doc, .docx. Max size: 50MB.') }}
                    </flux:text>
                </form>
            </div>
        </flux:modal>
    </div>
</div>
