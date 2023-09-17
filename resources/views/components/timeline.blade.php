<style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap");

    *,
    *::before,
    *::after {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .container {
        --color: rgba(30, 30, 30);
        --bgColor: rgba(245, 245, 245);
        min-height: 100vh;
        /* width::100vw; */
        display: grid;
        align-content: center;
        gap: 2rem;
        padding: 2rem;
        font-family: "Poppins", sans-serif;
        color: var(--color);
        background: var(--bgColor);
    }

    h1 {
        text-align: center;
    }

    .contener {
        --col-gap: 2rem;
        --row-gap: 2rem;
        --line-w: 0.25rem;
        display: grid;
        grid-template-columns: var(--line-w) 1fr;
        grid-auto-columns: max-content;
        column-gap: var(--col-gap);
        list-style: none;
        width: min(60rem, 90%);
        margin-inline: auto;
    }

    /* line */
    .contener::before {
        content: "";
        grid-column: 1;
        grid-row: 1 / span 20;
        background: rgb(225, 225, 225);
        border-radius: calc(var(--line-w) / 2);
    }

    /* columns*/

    /* row gaps */
    .contener .box:not(:last-child) {
        margin-bottom: var(--row-gap);
    }

    /* card */
    .contener .box {
        grid-column: 2;
        --inlineP: 1.5rem;
        margin-inline: var(--inlineP);
        grid-row: span 2;
        display: grid;
        grid-template-rows: min-content min-content min-content;
    }

    /* date */
    .contener .box .date {
        --dateH: 3rem;
        height: var(--dateH);
        margin-inline: calc(var(--inlineP) * -1);

        text-align: center;
        background-color: var(--accent-color);

        color: white;
        font-size: 1.25rem;
        font-weight: 700;

        display: grid;
        place-content: center;
        position: relative;

        border-radius: calc(var(--dateH) / 2) 0 0 calc(var(--dateH) / 2);
    }

    /* date flap */
    .contener .box .date::before {
        content: "";
        width: var(--inlineP);
        aspect-ratio: 1;
        background: var(--accent-color);
        background-image: linear-gradient(rgba(0, 0, 0, 0.2) 100%, transparent);
        position: absolute;
        top: 100%;

        clip-path: polygon(0 0, 100% 0, 0 100%);
        right: 0;
    }

    /* circle */
    .contener .box .date::after {
        content: "";
        position: absolute;
        width: 2rem;
        aspect-ratio: 1;
        background: var(--bgColor);
        border: 0.3rem solid var(--accent-color);
        border-radius: 50%;
        top: 50%;

        transform: translate(50%, -50%);
        right: calc(100% + var(--col-gap) + var(--line-w) / 2);
    }

    /* title descr */
    .contener .box .title,
    .contener .box .descr {
        background: var(--bgColor);
        position: relative;
        padding-inline: 1.5rem;
    }

    .contener .box .title {
        overflow: hidden;
        padding-block-start: 1.5rem;
        padding-block-end: 1rem;
        font-weight: 500;
    }

    .contener .box .descr {
        padding-block-end: 1.5rem;
        font-weight: 300;
    }

    /* shadows */
    .contener .box .title::before,
    .contener .box .descr::before {
        content: "";
        position: absolute;
        width: 90%;
        height: 0.5rem;
        background: rgba(0, 0, 0, 0.5);
        left: 50%;
        border-radius: 50%;
        filter: blur(4px);
        transform: translate(-50%, 50%);
    }

    .contener .box .title::before {
        bottom: calc(100% + 0.125rem);
    }

    .contener .box .descr::before {
        z-index: -1;
        bottom: 0.25rem;
    }

    @media (min-width: 40rem) {
        .contener {
            grid-template-columns: 1fr var(--line-w) 1fr;
        }

        .contener::before {
            grid-column: 2;
        }

        .contener .box:nth-child(odd) {
            grid-column: 1;
        }

        .contener .box:nth-child(even) {
            grid-column: 3;
        }

        /* start second card */
        .contener .box:nth-child(2) {
            grid-row: 2/4;
        }

        .contener .box:nth-child(odd) .date::before {
            clip-path: polygon(0 0, 100% 0, 100% 100%);
            left: 0;
        }

        .contener .box:nth-child(odd) .date::after {
            transform: translate(-50%, -50%);
            left: calc(100% + var(--col-gap) + var(--line-w) / 2);
        }

        .contener .box:nth-child(odd) .date {
            border-radius: 0 calc(var(--dateH) / 2) calc(var(--dateH) / 2) 0;
        }
    }

    .credits {
        margin-top: 1rem;
        text-align: right;
    }

    .credits a {
        color: var(--color);
    }

    .btn {
        box-shadow: 2px 2px 2px;
        padding: 5px;
        background-color: teal;
        /* margin-top: 20px */
        color: #fff;
    }
</style>

<div class="container max-w-full overflow-auto bg-gray-100 rounded shadow-lg dark:bg-gray-900 fi-descript">

    <h1 class="text-lg dark:text-white">
        {{-- {!! $record->code !!} --}}
        Soumis: @if ($record->submit_at)
                    {{ Carbon\Carbon::parse($record->submit_at)->diffForHumans() }}
                @endif
    </h1>
    <ul class="w-full contener">
        @forelse ($record->validations as $item)
        <li style="--accent-color:{{ $item->color }}" class="box fi-descript">
            <div class="date">{!! $item->user->departement->name !!} {!! ':' !!} {!! $item->created_at->format('d/m/Y H:m') !!}</div>
            <div class="text-justify descr dark:text-gray-700 dark:bg-gray-50">
                <p class="mt-2">
                    {!! $item->comments !!}
                </p>
                <hr class="my-2 ">
                <div>

                    @if ($item->document)
                        <a href="{{ asset('storage/' . $item->document) }}" target="__blank"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 uppercase bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                            <svg aria-hidden="true" class="w-4 h-4 mr-2 fill-current" fill="currentColor"
                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M2 9.5A3.5 3.5 0 005.5 13H9v2.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 15.586V13h2.5a4.5 4.5 0 10-.616-8.958 4.002 4.002 0 10-7.753 1.977A3.5 3.5 0 002 9.5zm9 3.5H9V8a1 1 0 012 0v5z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="mr-2 text-xl">
                                DOCUMENT
                            </span>
                            </span>
                        </a>
                    @endif


                </div>
            </div>
        </li>
    @empty

    @endforelse

    </ul>
</div>
