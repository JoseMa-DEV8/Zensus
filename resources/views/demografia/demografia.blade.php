@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4 text-center" style="color:rgb(103, 104, 107);">
    <h1 class="text-3xl font-bold mb-8">Datos Demográficos Españoles</h1>

    {{-- Filtros --}}
    <form method="GET" class="flex flex-wrap justify-center gap-4 mb-10">
        <select name="provincia_codigo" onchange="this.form.submit()" class="text-black p-2 rounded">
            <option value="">Todas las provincias</option>
            @foreach ($provincias as $provincia)
                <option value="{{ $provincia->codigo }}" {{ request('provincia_codigo') == $provincia->codigo ? 'selected' : '' }}>
                    {{ $provincia->nombre }}
                </option>
            @endforeach
        </select>

        <select name="municipio_codigo" onchange="this.form.submit()" class="text-black p-2 rounded" {{ !$provinciaSeleccionada ? 'disabled' : '' }}>
            <option value="">Todos los municipios</option>
            @foreach ($municipios as $municipio)
                <option value="{{ $municipio->com }}" {{ request('municipio_codigo') == $municipio->com ? 'selected' : '' }}>
                    {{ $municipio->nombre }}
                </option>
            @endforeach
        </select>
    </form>

    @if ($anioMasReciente)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-left">
            <div class="rounded-lg p-6" style="background: #f1f4f7;">
                <h3 class="text-lg font-bold mb-2" style="color: #1f2937;">📈 Evolución de la Población</h3>
                <canvas id="graficoPoblacion"></canvas>
            </div>
            <div class="rounded-lg p-6" style="background: #f1f4f7;">
                <h3 class="text-lg font-bold mb-2" style="color: #1f2937;">♀️♂️ Distribución por Sexo</h3>
                <canvas id="sexoChart"></canvas>
            </div>
            <div class="rounded-lg p-6" style="background: #f1f4f7;">
                <h3 class="text-lg font-bold mb-2" style="color: #1f2937;">📊 Grupos de Edad</h3>
                <canvas id="gruposEdadChart"></canvas>
            </div>
            <div class="rounded-lg p-6" style="background: #f1f4f7;">
                <h3 class="text-lg font-bold mb-2" style="color: #1f2937;">👥 Pirámide Poblacional</h3>
                <canvas id="piramideChart"></canvas>
            </div>
        </div>

        <div class="mt-10 rounded-lg p-6" style="background: #f1f4f7;">
            <h3 class="text-lg font-bold mb-4 text-center" style="color: #1f2937;">📉 Evolución Tasa de Paro por Provincia</h3>
            <canvas id="tasaParoChart"></canvas>
        </div>
    @else
        <p class="mt-10 text-gray-500">No hay datos disponibles para esta selección.</p>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('graficoPoblacion'), {
        type: 'line',
        data: {
            labels: {!! json_encode($graficoPoblacion['labels']) !!},
            datasets: [{
                label: 'Población Total',
                data: {!! json_encode($graficoPoblacion['valores']) !!},
                borderColor: 'rgba(59,130,246,0.8)',
                backgroundColor: 'rgba(59,130,246,0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            plugins: { legend: { labels: { color: '#1f2937' } } },
            scales: { y: { ticks: { color: '#1f2937' } }, x: { ticks: { color: '#1f2937' } } }
        }
    });

    new Chart(document.getElementById('sexoChart'), {
        type: 'doughnut',
        data: {
            labels: ['Mujeres', 'Hombres'],
            datasets: [{
                data: [{{ $totalMujeres }}, {{ $totalHombres }}],
                backgroundColor: ['rgba(96,165,250,0.6)', 'rgba(100,116,139,0.6)']
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'top',
                    labels: { color: '#1f2937' }
                }
            },
            cutout: '65%'
        }
    });

    new Chart(document.getElementById('gruposEdadChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($grupoEdadLabels) !!},
            datasets: [{
                label: 'Personas',
                data: {!! json_encode($grupoEdadValores) !!},
                backgroundColor: 'rgba(96,165,250,0.6)'
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { color: '#1f2937' } }, x: { ticks: { color: '#1f2937' } } }
        }
    });

    new Chart(document.getElementById('piramideChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($piramideLabels) !!},
            datasets: [
                {
                    label: 'Hombres',
                    data: {!! json_encode($piramideHombres) !!},
                    backgroundColor: 'rgba(100,116,139,0.6)'
                },
                {
                    label: 'Mujeres',
                    data: {!! json_encode($piramideMujeres) !!},
                    backgroundColor: 'rgba(96,165,250,0.6)'
                }
            ]
        },
        options: {
            indexAxis: 'y',
            scales: {
                x: {
                    stacked: true,
                    ticks: {
                        callback: value => Math.abs(value),
                        color: '#1f2937'
                    }
                },
                y: {
                    stacked: true,
                    ticks: { color: '#1f2937' }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx.dataset.label}: ${Math.abs(ctx.raw)}`
                    }
                },
                legend: { labels: { color: '#1f2937' } }
            }
        }
    });

    new Chart(document.getElementById('tasaParoChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($tasaLineLabels) !!},
            datasets: {!! json_encode($tasaLineSeries) !!}.map(ds => ({
                ...ds,
                borderColor: 'rgba(59,130,246,0.8)',
                backgroundColor: 'rgba(59,130,246,0.2)'
            }))
        },
        options: {
            plugins: { legend: { labels: { color: '#1f2937' } } },
            scales: { y: { ticks: { color: '#1f2937' } }, x: { ticks: { color: '#1f2937' } } }
        }
    });
</script>
@endsection
