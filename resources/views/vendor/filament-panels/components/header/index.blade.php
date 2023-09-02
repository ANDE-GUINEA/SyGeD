@props([
    'actions' => [],
    'breadcrumbs' => [],
    'heading',
    'subheading' => null,
])

<header
    {{ $attributes->class(['fi-header flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-red-500']) }}
>
    <div>
        @if ($breadcrumbs)
            <x-filament::breadcrumbs
                :breadcrumbs="$breadcrumbs"
                class="hidden mb-2 sm:block"
            />
        @endif

        <h1
            class="text-2xl font-bold tracking-tight fi-header-heading text-gray-950 dark:text-white sm:text-3xl"
        >
            {{ $heading }}
        </h1>

        @if ($subheading)
            <p
                class="max-w-2xl mt-2 text-lg text-gray-600 fi-header-subheading dark:text-gray-400"
            >
                {{ $subheading }}
            </p>
        @endif
    </div>

    @if ($actions)
        <x-filament-actions::actions
            :actions="$actions"
            @class([
                'shrink-0',
                'sm:mt-7' => $breadcrumbs,
            ])
        />
    @endif
</header>
