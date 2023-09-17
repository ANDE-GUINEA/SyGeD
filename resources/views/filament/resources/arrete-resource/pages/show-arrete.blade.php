<x-filament-panels::page>
<div class="max-w-full overflow-auto bg-gray-100 rounded shadow-lg dark:bg-gray-900 fi-descript">
        <div x-data="{ open: true }" class="px-6 py-4 mb-2">
            <span x-on:click="open = ! open" class="mb-2 text-xl font-bold uppercase cursor-pointer text-primary-400">
                DECRET: {!! $record->code !!}/{!! $record->init !!}/{!! $record->objet !!}

            </span>
            <div x-show="open" x-transition>

                <p class="text-base text-gray-700 dark:text-white">
                    {!! $record->content !!}
                </p>
            </div>
        </div>

        <div class="block">
            {{-- @if ($record->signe || $record->publie)
                @include('components.publiers.arrete-publie')
                @include('components.signes.arrete-signe')
                @include('components.archives.archive')
            @endif --}}
            @if ($record->publie)
                @include('components.publiers.arrete-publie')
            @endif
            @if ($record->signe)
                @include('components.signes.arrete-signe')
            @endif
            @include('components.soumis.document-soumis')
        </div>
    </div>
    <h1 class="text-xl font-extrabold text-gray-900 uppercase dark:text-white">
        Parcours de l'Arrete
    </h1>
    @include('components.timeline')
</x-filament-panels::page>
