<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pokedex') }}
        </h2>
    </x-slot>

    <div class="container">
        <div class="bg-white shadow-sm sm:rounded-l m-3 p-3">
            <div class="row d-flex justify-content-around">
                @foreach ($pokeList as $poke)
                <div class="col-sm-3 d-flex justify-content-center p-3">
                    <x-cardPokemon
                        pokeId="{{ $poke['id'] }}"
                        pokeName="{{ $poke['name'] }}"
                        discovered="{{ $poke['discovered'] ? '1':'0' }}" />
                </div>
                @endforeach
            </div>
        </div>
        <div class="p-4 flex justify-center">
            {{ $pokeList->links() }}
        </div>
    </div>


</x-app-layout>