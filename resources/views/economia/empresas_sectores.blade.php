@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4 text-white text-center">
    <h1 class="text-3xl font-bold mb-8 text-gray-800">Empresas por municipio y sector</h1>

    {{-- Filtros principales --}}
    <form method="GET" class="flex flex-col md:flex-row gap-4 justify-center mb-6">
        <select name="provincia_codigo" onchange="this.form.submit()" class="text-black p-2 rounded">
            @foreach ($provincias as $provincia)
                <option value="{{ $provincia->codigo }}" {{ $provincia->codigo == request('provincia_codigo') ? 'selected' : '' }}>
                    {{ $provincia->nombre }}
                </option>
            @endforeach
        </select>

        <select name="municipio_codigo" onchange="this.form.submit()" class="text-black p-2 rounded">
            @foreach ($municipios as $municipio)
                <option value="{{ $municipio->com }}" {{ $municipio->com == request('municipio_codigo') ? 'selected' : '' }}>
                    {{ $municipio->nombre }}
                </option>
            @endforeach
        </select>
    </form>

    {{-- Gráfico de evolución --}}
    @if ($municipioSeleccionado && $provinciaSeleccionada && count($datos))
        <div class="mb-12 p-6 rounded-2xl shadow" style="background-color: #f1f4f7;">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">
                Evolución en {{ $municipioSeleccionado->nombre }} ({{ $provinciaSeleccionada->nombre }})
            </h2>
            <canvas id="graficoEvolucion" height="100"></canvas>
        </div>
    @endif

    {{-- Gráfico de top municipios --}}
    @if ($topMunicipios->count())
        <div class="mt-12 p-6 rounded-2xl shadow" style="background-color: #f1f4f7;">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">
                Top 5 municipios con más empresas en {{ $provinciaSeleccionada->nombre }} ({{ $ultimoAnyo }})
            </h2>
            <canvas id="graficoTop" height="100"></canvas>
        </div>
    @endif

    {{-- Comparación entre provincias --}}
    <div class="mt-12 p-6 rounded-2xl shadow" style="background-color: #f1f4f7;">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Comparar evolución entre dos provincias</h2>
        <form method="GET" class="flex flex-wrap gap-4 justify-center mb-6">
            <div>
                <select name="provincia_a" onchange="this.form.submit()" class="text-black p-2 rounded">
                    <option value="">Selecciona provincia</option>
                    @foreach ($provincias as $provincia)
                        <option value="{{ $provincia->codigo }}" {{ request('provincia_a') == $provincia->codigo ? 'selected' : '' }}>
                            {{ $provincia->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="provincia_b" onchange="this.form.submit()" class="text-black p-2 rounded">
                    <option value="">Selecciona provincia</option>
                    @foreach ($provincias as $provincia)
                        <option value="{{ $provincia->codigo }}" {{ request('provincia_b') == $provincia->codigo ? 'selected' : '' }}>
                            {{ $provincia->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        @if ($datosComparacionA->count() || $datosComparacionB->count())
            <canvas id="graficoComparacion" height="100"></canvas>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if (count($datos))
        const datosEvolucion = @json($datos);
        const labelsEvolucion = datosEvolucion.map(item => item.anyo);
        const valoresEvolucion = datosEvolucion.map(item => item.valor);

        new Chart(document.getElementById('graficoEvolucion').getContext('2d'), {
            type: 'line',
            data: {
                labels: labelsEvolucion,
                datasets: [{
                    label: 'Nº Empresas',
                    data: valoresEvolucion,
                    borderColor: 'rgb(0, 148, 183)',
                    backgroundColor: 'rgba(0, 148, 183, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    @endif

    @if ($topMunicipios->count())
        const topMunicipios = @json($topMunicipios);
        const labelsTop = topMunicipios.map(item => item.municipio ? item.municipio.nombre : 'Desconocido');
        const valoresTop = topMunicipios.map(item => item.total);

        new Chart(document.getElementById('graficoTop').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labelsTop,
                datasets: [{
                    label: 'Empresas',
                    data: valoresTop,
                    backgroundColor: 'rgba(0, 148, 183, 0.2)',
                    borderColor: 'rgb(0, 148, 183)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    @endif

    @if ($datosComparacionA->count() || $datosComparacionB->count())
        const datosA = @json($datosComparacionA);
        const datosB = @json($datosComparacionB);
        const nombreProvinciaA = "{{ $nombreProvinciaA ?? 'Provincia A' }}";
        const nombreProvinciaB = "{{ $nombreProvinciaB ?? 'Provincia B' }}";

        const allLabels = [...new Set([...datosA.map(d => d.anyo), ...datosB.map(d => d.anyo)])].sort();

        const valoresA = allLabels.map(a => {
            const d = datosA.find(e => e.anyo == a);
            return d ? d.valor : 0;
        });

        const valoresB = allLabels.map(a => {
            const d = datosB.find(e => e.anyo == a);
            return d ? d.valor : 0;
        });

        new Chart(document.getElementById('graficoComparacion').getContext('2d'), {
            type: 'line',
            data: {
                labels: allLabels,
                datasets: [
                    {
                        label: nombreProvinciaA,
                        data: valoresA,
                        borderColor: 'rgb(0, 148, 183)',
                        backgroundColor: 'rgba(0, 148, 183, 0.2)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: nombreProvinciaB,
                        data: valoresB,
                        borderColor: 'rgb(99, 102, 106)',
                        backgroundColor: 'rgba(99, 102, 106, 0.2)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    @endif
</script>
@endsection
