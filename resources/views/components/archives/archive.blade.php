<div x-data="{ open: false }" class="px-6 py-4 ">
    <a x-on:click="open = ! open"
        class="inline-flex items-center px-4 py-2 mb-2 text-sm font-medium text-gray-600 uppercase border border-gray-200 rounded-lg shadow cursor-pointer bg-warning dark:bg-gray-900 dark:border-gray-600 dark:text-white">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        <span class="mr-2 text-xl text-gray-900 capitalize dark:text-white">
            Historique
        </span>
    </a>

    <div>
        <div x-show="open" x-transition>

            <div class="grid w-full grid-cols-1 gap-6 p-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @if ($record->archives)
                    {{-- {{ dd($record->archives) }} --}}
                    @forelse ($record->archives as $key=> $archive)
                        @include('components.archives.archive-card')

                        @forelse($archive->references as $key=>$dossier)
                            @include('components.archives.archive-references')
                        @empty
                        @endforelse

                        @if ($archive->confidential)
                            @forelse($archive->confidential as $key=>$dossier)
                                @include('components.archives.archive-confidentiel')
                            @empty
                            @endforelse
                        @endif

                        @if ($archive->autres)
                            @forelse($archive->autres as $key=>$dossier)
                                @include('components.archives.archive-public')
                            @empty
                            @endforelse
                        @endif
                        <hr>
                    @empty
                    @endforelse

                @endif
            </div>
        </div>
    </div>
</div>
