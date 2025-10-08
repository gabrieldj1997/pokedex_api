<style>
  .card {
    transition: transform 0.3s ease-in-out;

    /* Outros estilos básicos */
    padding: 20px;
    margin: 10px;
    background-color: #f0f0f0;
    border: 1px solid #ccc;
    cursor: pointer;
  }

  .card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
  }
</style>
<div class="card w-100" {{ $discovered == 1 ? "onclick=getPokemonDetails('".route('getPokemonByIdOrName', $pokeId)."')" : 'onclick=undiscoveredPokemon()' }}>
  <div class="d-flex justify-content-center">
    <img
      src="{{ url(path: "/pokemon_files/sprites/{$pokeId}.png") }}"
      class="img-fluid rounded"
      @style([ 'width: 200px' , 'height: 200px' , 'filter:grayscale(100%) brightness(0%)'=> $discovered == 0,
    ])
    >
  </div>
  <div class="card-body">
    <p class="font-flexo-demi" style="color:#919191; font-size:80%">
      Nº {{ $discovered == 1 ? str_pad($pokeId, 3, '0', STR_PAD_LEFT) : '---' }}</p>
    <p class="card-text font-flexo-demi" style="font-size: 150%;text-transform: capitalize;">{{ $discovered == 1 ? $pokeName : '--------' }}</p>
  </div>
</div>
<script>
  function getPokemonDetails(url){
    window.location.href = url;
  }
  function undiscoveredPokemon(){
    alert('Pokemon ainda não descoberto, jogue mais vezes para descobrir esse pokemon na aba de "desafios pokemon".')
  }
</script>