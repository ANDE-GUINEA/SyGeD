<x-filament-panels::page>

    <div class="max-w-full rounded max-h-24  overflow-auto shadow-lg bg-gray-100 dark:bg-gray-900">
        <div class="px-6 py-4">
            <div class="font-bold text-xl mb-2 text-primary-400">{!! $record->objet !!}</div>
            <p class="text-gray-700 text-base dark:text-white">
                {!! $record->content !!}

            </p>
        </div>
        <div class="px-6 pt-4 pb-2">

            <span class="inline-block bg-gray-200 rounded-lg px-1 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">
                <a href="{{ asset('storage/' . $record->documents) }}" target="__blank"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-primary-600 bg-white border border-gray-200 rounded-lg shadow  uppercase dark:bg-gray-300 dark:border-gray-600">
                    <svg aria-hidden="true" class="w-4 h-4 mr-2 fill-current" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M2 9.5A3.5 3.5 0 005.5 13H9v2.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 15.586V13h2.5a4.5 4.5 0 10-.616-8.958 4.002 4.002 0 10-7.753 1.977A3.5 3.5 0 002 9.5zm9 3.5H9V8a1 1 0 012 0v5z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="mr-2 text-xl">
                        DOCUMENTS DU PROJET
                    </span>
                </a>
        </div>
    </div>

    
    @include('components.timeline')















</x-filament-panels::page>
