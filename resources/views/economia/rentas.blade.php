@extends('layouts.app')

@section('content')
<div class="w-full text-center text-gray-800">
    @php
        $tituloLugar = null;
        if (request('municipio_id')) {
            $municipioSeleccionado = $municipios->firstWhere('id', request('municipio_id'));
            if ($municipioSeleccionado && $municipioSeleccionado->provincia) {
                $tituloLugar = $municipioSeleccionado->nombre . ' (' . $municipioSeleccionado->provincia->nombre . ')';
            }
        } elseif (request('provincia_id')) {
            $provinciaSeleccionada = $provincias->firstWhere('id', request('provincia_id'));
            if ($provinciaSeleccionada) {
                $tituloLugar = $provinciaSeleccionada->nombre;
            }
        }
    @endphp

    <h1 class="text-4xl font-bold mb-8 text-gray-800">
        💰 Datos de Renta
        @if($tituloLugar)
            – {{ $tituloLugar }}
        @endif
    </h1>

    <form method="GET" class="flex flex-wrap justify-center items-center gap-4 mb-10 text-center">
        <select name="provincia_id" onchange="this.form.submit()" class="text-black p-2 rounded border">
            <option value="">Todas las provincias</option>
            @foreach ($provincias as $provincia)
                <option value="{{ $provincia->id }}" {{ request('provincia_id') == $provincia->id ? 'selected' : '' }}>
                    {{ $provincia->nombre }}
                </option>
            @endforeach
        </select>

        <select name="municipio_id" onchange="this.form.submit()" class="text-black p-2 rounded border" {{ !request('provincia_id') ? 'disabled' : '' }}>
            <option value="">Todos los municipios</option>
            @foreach ($municipios as $municipio)
                <option value="{{ $municipio->id }}" {{ request('municipio_id') == $municipio->id ? 'selected' : '' }}>
                    {{ $municipio->nombre }}
                </option>
            @endforeach
        </select>
    </form>

    <div class="max-w-6xl mx-auto mb-10 space-y-10">
        {{-- Comparativa y evolución general --}}
        <div class="p-6 rounded-2xl shadow" style="background-color: #f1f4f7;">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 rounded-lg shadow flex flex-col items-center">
                    <h2 class="text-lg font-semibold mb-3 text-gray-800">💸 Comparativa de Rentas</h2>
                    <canvas id="rentaComparativaChart" class="w-full h-64"></canvas>
                </div>
                <div class="p-4 rounded-lg shadow flex flex-col items-center">
                    <h2 class="text-lg font-semibold mb-3 text-gray-800">📈 Evolución de Rentas</h2>
                    <canvas id="rentaEvolucionChart" class="w-full h-64"></canvas>
                </div>
            </div>
        </div>

        {{-- Evolución por municipios --}}
        @if (!empty($evolucionMunicipios))
        <div class="p-6 rounded-2xl shadow" style="background-color: #f1f4f7;">
            <h2 class="text-lg font-semibold mb-4 text-center text-gray-800">📊 Evolución de Renta por Municipio</h2>
            <button onclick="toggleAllLines()" class="mb-4 px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-800">Mostrar/Ocultar todas las líneas</button>
            <canvas id="rentaEvolucionMunicipiosChart" class="w-full h-[700px]"></canvas>
        </div>
        @endif

        {{-- Ranking de municipios --}}
        @if (!empty($rankingMunicipios))
        <div class="p-6 rounded-2xl shadow" style="background-color: #f1f4f7;">
            <h2 class="text-lg font-semibold mb-4 text-center text-gray-800">🏆 Ranking Renta Neta por Municipio (Último Año)</h2>
            <canvas id="rentaRankingMunicipiosChart" class="w-full h-96"></canvas>
        </div>
        @endif
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let evolucionMunicipiosChart;

@if (!empty($evolucionMunicipios))
const ctxEvolucion = document.getElementById('rentaEvolucionMunicipiosChart').getContext('2d');
evolucionMunicipiosChart = new Chart(ctxEvolucion, {
    type: 'line',
    data: {
        labels: {!! json_encode($anios) !!},
        datasets: [
            @php
                $colors = ['#60A5FA', '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#6366F1', '#F97316', '#4ADE80', '#EC4899'];
            @endphp
            @foreach($evolucionMunicipios as $index => $muni)
            {
                label: "{{ $muni['nombre'] }}",
                data: {!! json_encode($muni['valores']) !!},
                borderColor: "{{ $colors[$index % count($colors)] }}",
                tension: 0.3,
                fill: false
            },
            @endforeach
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                labels: { color: '#1f2937' },
                filter: (legendItem, chartData) => legendItem.datasetIndex < 5
            },
            tooltip: {
                mode: 'nearest',
                intersect: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    color: '#1f2937',
                    callback: v => v + ' €'
                }
            },
            x: { ticks: { color: '#1f2937' }}
        }
    }
});

function toggleAllLines() {
    evolucionMunicipiosChart.data.datasets.forEach((dataset, index) => {
        evolucionMunicipiosChart.isDatasetVisible(index)
            ? evolucionMunicipiosChart.hide(index)
            : evolucionMunicipiosChart.show(index);
    });
    evolucionMunicipiosChart.update();
}
@endif

@if (!empty($rankingMunicipios))
new Chart(document.getElementById('rentaRankingMunicipiosChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($rankingMunicipios->pluck('municipio')) !!},
        datasets: [{
            label: '€',
            data: {!! json_encode($rankingMunicipios->pluck('valor')) !!},
            backgroundColor: '#3B82F6',
            borderWidth: 0
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { ticks: { color: '#1f2937' }},
            x: {
                min: {{ $minValor ?? 6000 }},
                max: {{ $maxValor ?? 20000 }},
                ticks: {
                    color: '#1f2937',
                    callback: value => value + ' €'
                }
            }
        }
    }
});
@endif
</script>
@endsection
