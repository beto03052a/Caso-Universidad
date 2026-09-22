@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard — Métricas Consolidadas')

@section('content')

{{-- ── KPI Cards ──────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Total Solicitudes --}}
    <div class="col-6 col-xl-3">
        <div class="card kpi-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:#EFF6FF;">
                    <i class="bi bi-ticket-perforated-fill" style="color:#2563EB;"></i>
                </div>
                <div>
                    <div class="kpi-value">{{ $totalRequests }}</div>
                    <div class="kpi-label">Total Solicitudes</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pendientes --}}
    <div class="col-6 col-xl-3">
        <div class="card kpi-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:#FEF9C3;">
                    <i class="bi bi-hourglass-split" style="color:#CA8A04;"></i>
                </div>
                <div>
                    <div class="kpi-value" id="kpi-pending">{{ $totalPending }}</div>
                    <div class="kpi-label">Pendientes</div>
                </div>
            </div>
        </div>
    </div>

    {{-- En Proceso --}}
    <div class="col-6 col-xl-3">
        <div class="card kpi-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:#EFF6FF;">
                    <i class="bi bi-arrow-repeat" style="color:#2563EB;"></i>
                </div>
                <div>
                    <div class="kpi-value">{{ $totalInProcess }}</div>
                    <div class="kpi-label">En Proceso</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Atendidas + Cerradas --}}
    <div class="col-6 col-xl-3">
        <div class="card kpi-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="kpi-icon" style="background:#F0FDF4;">
                    <i class="bi bi-check-circle-fill" style="color:#16A34A;"></i>
                </div>
                <div>
                    <div class="kpi-value" id="kpi-attended">{{ $totalAttended }}</div>
                    <div class="kpi-label">Atendidas / Cerradas</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Segunda fila: tiempo promedio + gráfico por estado ───────────────────── --}}
<div class="row g-3 mb-4">
    {{-- Tiempo Promedio de Atención --}}
    <div class="col-md-4">
        <div class="card border-0 rounded-3 h-100" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
            <div class="card-header bg-transparent border-0 pb-0">
                <h6 class="mb-0 fw-semibold text-muted" style="font-size:.8rem;letter-spacing:.05em;text-transform:uppercase;">
                    <i class="bi bi-stopwatch text-primary me-1"></i>Tiempo de Atención
                </h6>
            </div>
            <div class="card-body d-flex flex-column justify-content-center align-items-center text-center py-3">
                <div class="d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;border-radius:12px;background:#EFF6FF;">
                    <i class="bi bi-clock-history text-primary" style="font-size:1.5rem;"></i>
                </div>
                <div class="mb-1" style="font-size:2.75rem;font-weight:700;color:#2563EB;line-height:1.1;">
                    {{ $avgHours }}<span style="font-size:1.6rem;font-weight:600;margin-left:2px;">h</span>
                </div>
                <div class="text-dark fw-semibold" style="font-size:.875rem;">Tiempo Promedio de Resolución</div>
                <div class="text-muted mt-1" style="font-size:.75rem;">Desde creación hasta atención o cierre</div>
            </div>
        </div>
    </div>

    {{-- Distribución por Estado (Chart.js donut) --}}
    <div class="col-md-8">
        <div class="card border-0 rounded-3 h-100" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
            <div class="card-header bg-transparent border-0 pb-0">
                <h6 class="mb-0 fw-semibold text-muted" style="font-size:.8rem;letter-spacing:.05em;text-transform:uppercase;">
                    <i class="bi bi-pie-chart text-primary me-1"></i>Distribución por Estado
                </h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center gap-4 flex-wrap py-3">
                {{-- Contenedor con tamaño explícito para evitar deformación del canvas --}}
                <div style="position: relative; width: 170px; height: 170px; flex-shrink: 0;">
                    <canvas id="statusChart"></canvas>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                        <div style="font-size: 1.4rem; font-weight: 700; color: #0F172A; line-height: 1;">{{ $totalRequests }}</div>
                        <div style="font-size: 0.65rem; color: #64748B; font-weight: 600; text-transform: uppercase;">Total</div>
                    </div>
                </div>

                {{-- Leyenda detallada con porcentaje y cantidad --}}
                <div class="d-flex flex-column gap-2" style="min-width: 220px;">
                    @php
                        $statusColors = [
                            'pendiente'  => ['#94A3B8','Pendiente'],
                            'en_proceso' => ['#2563EB','En Proceso'],
                            'atendida'   => ['#16A34A','Atendida'],
                            'cerrada'    => ['#0F172A','Cerrada'],
                        ];
                    @endphp
                    @foreach($statusColors as $key => [$color, $label])
                        @php
                            $count = $byStatus[$key] ?? 0;
                            $pct = $totalRequests > 0 ? round(($count / $totalRequests) * 100) : 0;
                        @endphp
                        <div class="d-flex align-items-center justify-content-between gap-3 p-1 rounded hover-bg">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:10px;height:10px;border-radius:50%;background:{{ $color }};flex-shrink:0;"></div>
                                <span class="text-dark fw-500" style="font-size:.82rem;">{{ $label }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark" style="font-size:.82rem;">{{ $count }}</span>
                                <span class="badge bg-light text-secondary border" style="font-size:.7rem;width:42px;text-align:center;">{{ $pct }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Distribuciones detalladas ─────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    {{-- Por Categoría --}}
    <div class="col-md-4">
        <div class="card border-0 rounded-3 h-100" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
            <div class="card-header bg-transparent border-0 pb-0">
                <h6 class="mb-0 fw-semibold" style="font-size:.875rem;">
                    <i class="bi bi-tag me-1 text-primary"></i>Por Categoría
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Categoría</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                        @foreach($byCategory as $cat)
                        <tr>
                            <td class="py-2">{{ $cat->name }}</td>
                            <td class="py-2 text-end">
                                <span class="badge bg-primary rounded-pill">{{ $cat->service_requests_count }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Por Prioridad --}}
    <div class="col-md-4">
        <div class="card border-0 rounded-3 h-100" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
            <div class="card-header bg-transparent border-0 pb-0">
                <h6 class="mb-0 fw-semibold" style="font-size:.875rem;">
                    <i class="bi bi-flag me-1 text-danger"></i>Por Prioridad
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Prioridad</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                        @foreach($byPriority as $p)
                        @php
                            $colors = ['critica'=>'bg-danger','alta'=>'bg-warning text-dark','media'=>'bg-primary','baja'=>'bg-info text-dark'];
                            $labels = ['critica'=>'Crítica','alta'=>'Alta','media'=>'Media','baja'=>'Baja'];
                        @endphp
                        <tr>
                            <td class="py-2">
                                <span class="badge {{ $colors[$p->priority] ?? 'bg-secondary' }}">
                                    {{ $labels[$p->priority] ?? $p->priority }}
                                </span>
                            </td>
                            <td class="py-2 text-end fw-semibold">{{ $p->total }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Carga por Técnico --}}
    <div class="col-md-4">
        <div class="card border-0 rounded-3 h-100" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
            <div class="card-header bg-transparent border-0 pb-0">
                <h6 class="mb-0 fw-semibold" style="font-size:.875rem;">
                    <i class="bi bi-person-gear me-1 text-success"></i>Carga por Técnico
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Técnico</th><th class="text-end">Total</th><th class="text-end">Activas</th></tr></thead>
                    <tbody>
                        @forelse($workloadByTecnico as $t)
                        <tr>
                            <td class="py-2" style="font-size:.82rem;">{{ $t->name }}</td>
                            <td class="py-2 text-end">{{ $t->assigned_requests_count }}</td>
                            <td class="py-2 text-end">
                                <span class="badge bg-warning text-dark">{{ $t->pending_count + $t->inprocess_count }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">Sin técnicos registrados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ── Solicitudes Recientes ─────────────────────────────────────────────────── --}}
<div class="card border-0 rounded-3" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold" style="font-size:.875rem;">
            <i class="bi bi-clock-history me-1 text-primary"></i>Solicitudes Recientes
        </h6>
        <a href="{{ route('requests.index') }}" class="btn btn-sm btn-outline-primary" style="font-size:.78rem;">
            Ver todas <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Solicitante</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentRequests as $req)
                    <tr style="cursor:pointer;" onclick="window.location='{{ route('requests.show', $req->id) }}'">
                        <td class="py-2"><code style="font-size:.75rem;">{{ $req->ticket_code }}</code></td>
                        <td class="py-2" style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $req->title }}</td>
                        <td class="py-2"><small>{{ $req->category?->name }}</small></td>
                        <td class="py-2"><span class="badge {{ $req->priority_badge_class }}">{{ $req->priority_label }}</span></td>
                        <td class="py-2"><span class="badge {{ $req->status_badge_class }}">{{ $req->status_label }}</span></td>
                        <td class="py-2"><small>{{ $req->student?->name }}</small></td>
                        <td class="py-2"><small class="text-muted">{{ $req->created_at->diffForHumans() }}</small></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const statusData = {
        labels: ['Pendiente', 'En Proceso', 'Atendida', 'Cerrada'],
        datasets: [{
            data: [
                {{ $byStatus['pendiente']  ?? 0 }},
                {{ $byStatus['en_proceso'] ?? 0 }},
                {{ $byStatus['atendida']   ?? 0 }},
                {{ $byStatus['cerrada']    ?? 0 }},
            ],
            backgroundColor: ['#94A3B8','#2563EB','#16A34A','#0F172A'],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    };

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: statusData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return ' ' + ctx.label + ': ' + ctx.raw + ' solicitudes';
                        }
                    }
                }
            },
            animation: { animateScale: true },
        }
    });
</script>
@endpush
