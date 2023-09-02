@forelse ($dossier->document as $doc)
<a href="{{ asset('storage/'. $doc) }}" target="__blank"
        class="inline-flex items-center px-4 py-2 mb-2 text-sm font-medium text-green-500 uppercase border border-gray-200 rounded-lg shadow cursor-pointer bg-primary dark:bg-gray-900 dark:border-gray-600">

        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <span class="ml-2 text-sm">
            {{ $doc}}
        </span>
    </a>

@empty

@endforelse
