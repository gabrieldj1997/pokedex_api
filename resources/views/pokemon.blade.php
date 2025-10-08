<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __($pokemon['name']) }}
        </h2>
    </x-slot>

    <div class="container">
        <div class="bg-white shadow-sm sm:rounded-l m-3 p-3">
            <div class="row d-flex justify-content-around">
                <div class="col-md-6">
                    <img src="/pokemon_files/sprites/{{ $pokemon['id'] }}.png" alt="">
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Altura:</label>
                            <p>{{ $pokemon['height'] }}</p>
                        </div>
                        <div class="col-md-6">
                            <label>Categoria:</label>
                            <p>{{ $pokemon['category'] }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Peso:</label>
                            <p>{{ $pokemon['weight'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row d-flex justify-content-around">
                <div class="col-md-6">
                    <div id="myGrid" style="height: 500px"></div>
                </div>
                <div class="col-md-6">
                    <canvas id="statsGraphics"></canvas>
                </div>
            </div>
            <div class="row d-flex justify-content-around">
                <div class="col-md-12">
                    <!-- evoluções -->
                </div>
            </div>
        </div>
    </div>
    <script>
        const labels = ['ps', 'attack', 'defense', 'attack_special', 'defense_special', 'speed'];
        const data = {
            labels: labels,
            datasets: [{
                label: null,
                data: [
                    {{ $pokemon['ps'] }},
                    {{ $pokemon['attack'] }},
                    {{ $pokemon['defense'] }},
                    {{ $pokemon['attack_special'] }},
                    {{ $pokemon['defense_special'] }},
                    {{ $pokemon['speed'] }},
                ],
                backgroundColor: [
                    '#a10000ff',
                    '#a10000ff',
                    '#a10000ff',
                    '#a10000ff',
                    '#a10000ff',
                    '#a10000ff',
                ],
                borderColor: [
                    '#a10000ff',
                    '#a10000ff',
                    '#a10000ff',
                    '#a10000ff',
                    '#a10000ff',
                    '#a10000ff',
                ],
                borderWidth: 1
            }]
        };
        const config = {
            type: 'bar',
            data: data,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 200
                    }
                }
            },
        };
        const ctx = document.getElementById('statsGraphics');
        new Chart(
            ctx,
            config
        );
    </script>
</x-app-layout>