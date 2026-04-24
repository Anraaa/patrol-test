@php
    $state = $getState();
    $statePaths = $getStatePath(false);
    $isDisabled = $isDisabled();
    $files = array_values((array) $state);
    $uuid = $getComponent()->getContainer()->getParentComponent()?->getName() ?? 'default';
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field">

    <div class="space-y-4">
        {{-- Camera & Gallery Buttons --}}
        <div class="flex flex-col sm:flex-row gap-2">
            {{-- Camera Button --}}
            <button
                type="button"
                x-on:click="document.getElementById('camera-{{ $uuid }}').click()"
                :disabled="{{ $isDisabled ? 'true' : 'false' }}"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-lg border-2 border-blue-300 dark:border-blue-600 bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-300 font-semibold hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                <span>📷 Ambil Foto Langsung</span>
            </button>

            {{-- Gallery Button --}}
            <button
                type="button"
                x-on:click="document.getElementById('gallery-{{ $uuid }}').click()"
                :disabled="{{ $isDisabled ? 'true' : 'false' }}"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-lg border-2 border-emerald-300 dark:border-emerald-600 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 font-semibold hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2l1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z" />
                </svg>
                <span>🖼️ Pilih dari Galery</span>
            </button>
        </div>

        {{-- Hidden File Inputs --}}
        <input
            id="camera-{{ $uuid }}"
            type="file"
            accept="image/*"
            capture="environment"
            {{ $isDisabled ? 'disabled' : '' }}
            wire:model.live="{{ $statePaths }}"
            class="hidden">

        <input
            id="gallery-{{ $uuid }}"
            type="file"
            accept="image/*"
            multiple
            {{ $isDisabled ? 'disabled' : '' }}
            wire:model.live="{{ $statePaths }}"
            class="hidden">

        {{-- File Previews --}}
        @if ($files)
        <div class="space-y-2">
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                Foto yang dipilih ({{ count($files) }}/5)
            </p>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                @foreach ($files as $index => $file)
                <div class="relative group">
                    @if (is_string($file) && (str_starts_with($file, 'http') || str_starts_with($file, '/')))
                    <img
                        src="{{ is_string($file) ? (str_starts_with($file, 'http') ? $file : \Storage::url($file)) : $file->getTemporaryUrl() }}"
                        alt="Photo {{ $index + 1 }}"
                        class="w-full h-24 object-cover rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                    @else
                    <div class="w-full h-24 bg-gray-200 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-6-6.5h.008v.008h-.008v-.008Zm0 6h.008v.008h-.008v-.008Zm6-11.25h.008v.008h-.008v-.008Zm0 6h.008v.008h-.008v-.008Z" />
                        </svg>
                    </div>
                    @endif
                    <button
                        type="button"
                        x-on:click="$wire.$call('deleteUploadedFile', '{{ $statePaths }}', {{ $index }})"
                        class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                        {{ $isDisabled ? 'disabled' : '' }}>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Info --}}
        <p class="text-xs text-gray-500 dark:text-gray-400">
            💡 Klik <span class="font-semibold">📷 Ambil Foto Langsung</span> untuk menggunakan kamera perangkat, atau klik <span class="font-semibold">🖼️ Pilih dari Galery</span> untuk memilih dari galeri. Maksimal 5 foto.
        </p>
    </div>

</x-dynamic-component>

