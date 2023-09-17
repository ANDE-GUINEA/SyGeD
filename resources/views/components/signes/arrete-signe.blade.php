<div x-data="{ open: false }" class="px-6 py-1">
    <a x-on:click="open = ! open"
        class="inline-flex items-center px-4 py-2 mb-2 text-sm font-medium text-green-900 border border-gray-200 rounded-lg shadow cursor-pointer bg-warning dark:bg-gray-900 dark:border-gray-600 dark:text-white">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        <span class="mr-2 text-lg text-green-700 uppercase dark:text-white">
           document signé
        </span>
    </a>
    <div>
        <div x-show="open" x-transition>

            <div class="grid w-full grid-cols-1 gap-6 p-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                {{-- @include('components.card-icons') --}}
                {{-- @forelse($record->documentPrivate as $dossier)
                                @include('components.card-confidentiel')
                        @empty
                        @endforelse --}}
                {{-- @forelse($record->dossiers as $dossier)
                    @include('components.signe')
                @empty
                @endforelse --}}
                {{-- @include('components.signe') --}}

                <a href="{{ asset('storage/' . $record->signe) }}" target="__blank"
                    class="inline-flex items-center px-4 py-2 mb-2 text-sm font-medium text-green-500 uppercase border border-gray-200 rounded-lg shadow cursor-pointer bg-primary dark:bg-gray-900 dark:border-gray-600">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="ml-2 text-sm">
                        Document
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>
