<x-filament-panels::page>

    <div class="max-w-full overflow-auto bg-gray-100 rounded shadow-lg dark:bg-gray-900 fi-descript">
        <div x-data="{ open: true }" class="px-6 py-4 mb-2">
            <span x-on:click="open = ! open"
                class="mb-2 text-xl font-bold cursor-pointer text-primary-400">
                DECRET: {!! $record->code !!}/{!! $record->init !!}/{!! $record->objet !!}

            </span>
            <div x-show="open" x-transition>

                <p class="text-base text-gray-700 dark:text-white">
                    {!! $record->content !!}
                </p>
            </div>
        </div>

        <div class="block">
<div x-data="{ open: false }" class="px-6 py-4 ">
            <a x-on:click="open = ! open"
                class="inline-flex items-center px-4 py-2 mb-2 text-sm font-medium text-green-900 uppercase border border-gray-200 rounded-lg shadow cursor-pointer bg-warning dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span class="mr-2 text-xl text-green-700 uppercase dark:text-white">
                    Documents signé
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
                        @forelse($record->dossiers as $dossier)
                                @include('components.signe')
                        @empty
                        @endforelse
                        {{-- @include('components.signe') --}}
                    </div>
                </div>
            </div>
        </div>
        <div x-data="{ open: false }" class="px-6 py-4 ">
            <a x-on:click="open = ! open"
                class="inline-flex items-center px-4 py-2 mb-2 text-sm font-medium text-orange-600 uppercase border border-gray-200 rounded-lg shadow cursor-pointer bg-warning dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span class="mr-2 text-xl text-indigo-500 capitalize dark:text-white">
                    Documents
                </span>
            </a>

            <div>
                <div x-show="open" x-transition>

                    <div class="grid w-full grid-cols-1 gap-6 p-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @include('components.card-icons')
                        @if ($record->documentPrivate)
                        @forelse($record->documentPrivate as $dossier)
                                @include('components.card-confidentiel')
                        @empty
                        @endforelse

                        @endif
                        @if ($record->documentPublic)
                        @forelse($record->documentPublic as $dossier)
                                @include('components.card-public')
                        @empty
                        @endforelse

                        @endif
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

    <h1 class="text-lg uppercase dark:text-white">
        Parcours du decret
    </h1>
    @include('components.timeline')

</x-filament-panels::page>
