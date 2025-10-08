<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Pokemon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Client\ConnectionException;
use Exception;

class PokemonController extends Controller
{
    private $disk;
    private $rootFiles;
    private $listPokemon;
    private $sprites;
    private $spritesUrl;
    private $spritesType;
    public function __construct()
    {
        $this->disk = 'public_root';
        $this->rootFiles = 'pokemon_files/';
        $this->listPokemon = $this->rootFiles . 'pokemon_list.json';
        $this->sprites = $this->rootFiles . 'sprites/';
        $this->spritesUrl = env('POKEMON_SPRITE_URL', 'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/home/');
        $this->spritesType = env('POKEMON_SPRITE_TYPE', '.png');
    }
    public function pokemonList(Request $request)
    {
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $filePokemonList = $this->listPokemon;
        $fullPokeList = [];
        if (Storage::disk($this->disk)->exists($this->listPokemon)) {
            $json_content = Storage::disk($this->disk)->get($filePokemonList);
            $pokeList = json_decode($json_content, true);
            $pokeUndiscovered = array_filter($pokeList, function ($poke) {
                return $poke['discovered'] == false;
            });
            $pokeDiscovered = array_filter($pokeList, function ($poke) {
                return $poke['discovered'] == true;
            });
            $pokeUndiscovered = array_values($pokeUndiscovered);
            $pokeDiscovered = array_values($pokeDiscovered);
            shuffle($pokeUndiscovered);
            array_multisort(array_column($pokeDiscovered, 'id'), SORT_ASC, $pokeDiscovered);
            $fullPokeList = array_merge($pokeDiscovered, $pokeUndiscovered);
        } else {
            $resp = Http::get('https://pokeapi.co/api/v2/pokedex/2/');
            $data = $resp->json();
            $fullPokeList = array_map(function ($poke) {
                return [
                    'name' => $poke['pokemon_species']['name'],
                    'id' => $poke['entry_number'],
                    'discovered' => false
                ];
            }, $data['pokemon_entries']);
            Storage::disk($this->disk)->put($filePokemonList, json_encode($fullPokeList, JSON_PRETTY_PRINT));
            shuffle($fullPokeList);
        }
        $collection = new Collection($fullPokeList);
        $paginatedItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $pokeListPaginated = new LengthAwarePaginator(
            $paginatedItems,
            $collection->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        return view('pokemonList', ['pokeList' => $pokeListPaginated]);
    }

    public function getPokemonByIdOrName($identifier)
    {
        $fileName = $this->existsFilePokemon($identifier);
        if ($fileName) {
            $jsonPoke = Storage::disk($this->disk)->get($fileName);
            $objPoke = json_decode($jsonPoke, true);
            $pokemon = new Pokemon($objPoke);
            return view('pokemon', ['pokemon' => $pokemon]);
        }
        $respPoke = Http::get('https://pokeapi.co/api/v2/pokemon/' . $identifier);
        $jsonPoke = $respPoke->json();
        if ($jsonPoke == null) return 'pokemon não encontrado.';
        if ($jsonPoke['id'] > 151) return 'apenas da primeira geração.';
        $pokemon = new Pokemon([
            'id' => $jsonPoke['id'],
            'name' => $jsonPoke['species']['name'],
            'height' => $jsonPoke['height'],
            'weight' => $jsonPoke['weight'],
            'types' => $jsonPoke['types'],
            'ps' => $this->getStatsByName($jsonPoke['stats'], 'hp'),
            'attack' => $this->getStatsByName($jsonPoke['stats'], 'attack'),
            'defense' => $this->getStatsByName($jsonPoke['stats'], 'defense'),
            'attack_special' => $this->getStatsByName($jsonPoke['stats'], 'special-attack'),
            'defense_special' => $this->getStatsByName($jsonPoke['stats'], 'special-defense'),
            'speed' => $this->getStatsByName($jsonPoke['stats'], 'speed'),
        ]);
        $respPoke = Http::get($jsonPoke['species']['url']);
        $jsonPoke = $respPoke->json();
        $pokemon->category = $this->getCategory($jsonPoke['genera'], 'en');
        $pokemon = $this->getEvolutionChain($pokemon, $jsonPoke['evolution_chain']['url']);
        Storage::disk($this->disk)->put($this->rootFiles . 'pokemon_' . $pokemon->id . '_' . $pokemon->name . '.json', json_encode($pokemon, JSON_PRETTY_PRINT));
        return view('pokemon', ['pokemon' => $pokemon]);
    }


    public function getImages()
    {
        set_time_limit(180); 
        $jsonPoke = Storage::disk($this->disk)->get($this->listPokemon);
        $jsonPoke = json_decode($jsonPoke, true);
        if(!$jsonPoke) return "Volte para rota inicial para pegar a lista de pokemons";
        foreach ($jsonPoke as $poke) {
            $fileName = $poke['id'] . $this->spritesType;
            $filePath = $this->sprites . $fileName;
            if (!Storage::disk($this->disk)->exists($filePath)) {
                $remoteUrl = $this->getSpriteUrl($poke['id']);
                try {
                    $response = Http::retry(3, 60000)->get($remoteUrl);

                    if ($response->successful()) {
                        $resource = $response->toPsrResponse()->getBody()->getContents();
                        Storage::disk($this->disk)->put($filePath, $resource);
                    } else {
                        Log::error("Falha na resposta HTTP para {$poke['id']} (Status: {$response->status()}). URL: {$remoteUrl}");
                    }
                } catch (ConnectionException $e) {
                    Log::error("Falha persistente na conexão após 3 tentativas para {$poke['id']}. URL: {$remoteUrl}. Erro: " . $e->getMessage());

                } catch (Exception $e) {
                    Log::error("Erro geral ao processar imagem para {$poke['id']}. URL: {$remoteUrl}. Erro: " . $e->getMessage());
                }
            }
        }
        return redirect()->route('getPokemonList');
    }
    private function existsFilePokemon($identifier)
    {
        $directory = $this->rootFiles;
        $search_term = Str::lower($identifier);
        $allFiles = Storage::disk($this->disk)->files($directory);
        $foundFiles = collect($allFiles)->filter(function ($filePath) use ($search_term) {
            $fileName = basename($filePath);
            return Str::contains($fileName, '_' . $search_term . '_') || Str::contains($fileName,   $search_term . '.json');
        })->first();
        if ($foundFiles) {
            return $foundFiles;
        }
        return false;
    }
    public function discoveredPokemon($id)
    {
        $pokeJson = Storage::disk($this->disk)->get($this->listPokemon);
        $pokeJson = json_decode($pokeJson);
        $pokemonDiscovered = [];
        foreach ($pokeJson as $key => $poke) {
            if ($poke->id == $id) {
                $pokeJson[$key]->discovered = true;
                $pokemonDiscovered = $poke;
            }
        }
        Storage::disk($this->disk)->put($this->listPokemon, json_encode($pokeJson, JSON_PRETTY_PRINT));
        if ($pokemonDiscovered) {
            return redirect()->route('getPokemonByIdOrName', $pokemonDiscovered->id);
        }
        return redirect()->route('getPokemonList');
    }
    private function getSpriteUrl($id)
    {
        return $this->spritesUrl . $id . $this->spritesType;
    }
    private function getStatsByName($listStats, $name)
    {
        foreach ($listStats as $stats) {
            if ($stats['stat']['name'] == $name) {
                return $stats['base_stat'];
            }
        }
        return 0;
    }
    private function getCategory($categories, $language)
    {
        foreach ($categories as $category) {
            if ($category['language']['name'] == $language) {
                return $category['genus'];
            }
        }
        return '-----';
    }
    private function getEvolutionChain(Pokemon $pokemon, $url_evolution_chain)
    {
        $respEvolution = Http::get($url_evolution_chain);
        $jsonEvolution = $respEvolution->json();
        $baseForm = $jsonEvolution['chain'];
        $secondForm = [];
        $thirdForm = [];

        if ($baseForm['evolves_to']) {
            $secondForm = $baseForm['evolves_to'];
            foreach ($secondForm as $form) {
                if ($form['evolves_to']) {
                    foreach ($form['evolves_to'] as $nextForm) {
                        $thirdForm[] = $nextForm;
                    }
                }
            }
        }
        $evolutions[1] = [0 => $baseForm['species']['name']];
        $evolutions[2] = array_map(function ($form) {
            return $form['species']['name'];
        }, $secondForm);
        $evolutions[3] = array_map(function ($form) {
            return $form['species']['name'];
        }, $thirdForm);

        foreach ($evolutions as $form => $names) {
            foreach ($names as $name) {
                if ($name == $pokemon->name) {
                    $pokemon->evolution_stage = $form;
                }
            }
        }

        switch ($pokemon->evolution_stage) {
            case 1:
                $pokemon->evolve_from = [];
                $pokemon->evolve_to = $evolutions[2];
                break;
            case 2:
                $pokemon->evolve_from = $evolutions[1];
                $pokemon->evolve_to = $evolutions[3];
                break;
            case 3:
                $pokemon->evolve_from = $evolutions[2];
                $pokemon->evolve_to = [];
                break;
        }
        return $pokemon;
    }
    public function challengePokemon()
    {
        $jsonPoke = Storage::disk($this->disk)->get($this->listPokemon);
        $arrayPoke = json_decode($jsonPoke, true);
        $pokesUndiscovered = array_filter($arrayPoke, function ($poke) {
            return !$poke['discovered'];
        });
        if (count($pokesUndiscovered) == 0) return 'Parabens vc descobriu todos pokemons!';
        shuffle($pokesUndiscovered);
        $pokeDrawn = $pokesUndiscovered[0];
        $namesPokes[] = $pokeDrawn['name'];
        for ($i = 0; $i < 11; $i++) {
            shuffle($arrayPoke);
            if ($arrayPoke[$i]['name'] === $pokeDrawn['name']) {
                $namesPokes[] = $arrayPoke[$i + 1]['name'];
            } else {
                $namesPokes[] = $arrayPoke[$i]['name'];
            }
        }
        shuffle($namesPokes);
        return view('pokemonChallenge', ['pokemonDrawn' => $pokeDrawn, 'pokemonNames' => $namesPokes]);
    }
    public function resetList()
    {
        Storage::disk($this->disk)->delete($this->listPokemon);
        return redirect()->route('getPokemonList');
    }
    public function resetSprites()
    {
        Storage::disk($this->disk)->deleteDirectory($this->sprites);
        return redirect()->route('getPokemonList');
    }
}
