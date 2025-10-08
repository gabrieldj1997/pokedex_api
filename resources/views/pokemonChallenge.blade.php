<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('PokemonList') }}
        </h2>
    </x-slot>

    <div class="container">
        <div class="bg-white shadow-sm sm:rounded-l m-3 p-3">
            <div class="row d-flex justify-content-around">
                <div class="col-xs-3 d-flex justify-content-center p-3">
                    <x-cardPokemon
                        pokeId="{{ $pokemonDrawn['id'] }}"
                        pokeName="{{ $pokemonDrawn['name'] }}"
                        discovered="{{ $pokemonDrawn['discovered'] ? '1':'0' }}" />
                </div>
            </div>
            <div class="row d-flex justify-content-around">
                @foreach ($pokemonNames as $pokemonName)
                <div class="col-6 col-lg-2 d-flex justify-content-center p-3">
                    <button type="button" class="btn btn-light border-danger-subtle w-75" onclick="checkAnswer(this)" value="{{ $pokemonName }}">{{ $pokemonName }}</button>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <script>
        function checkAnswer(button) {
            if ($(button).attr('value') === '{{ $pokemonDrawn['name'] }}') {
                alert('parabens vc acertou!');
                window.location.href = "{{ route('discoveredPokemon', $pokemonDrawn['id']) }}"
            } else {
                alert('Você errou o pokemon!');
                window.location.reload()
            }
        }
    </script>
</x-app-layout>