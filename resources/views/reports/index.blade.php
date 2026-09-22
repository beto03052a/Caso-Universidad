@extends('layouts.app')
@section('title', 'Reportes')
@section('page-title', 'Módulo de Reportes')

@section('content')

{{-- ── Filtros Avanzados ──────────────────────────────────────────────────── --}}
<div class="card border-0 rounded-3 mb-3" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
    <div class="card-header bg-transparent border-0">
        <h6 class="mb-0 fw-semibold" style="font-size:.875rem;">
            <i class="bi bi-funnel me-1 text-primary"></i>Filtros Avanzados
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.index') }}" id="form-report-filter" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label form-label-sm">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" id="fecha-inicio" class="form-control form-control-sm" value="{{ request('fecha_inicio') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm">Fecha Fin</label>
                <input type="date" name="fecha_fin" id="fecha-fin" class="form-control form-control-sm" value="{{ request('fecha_fin') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm">Estado</label>
                <select name="status" id="report-status" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach(['pendiente'=>'Pendiente','en_proceso'=>'En Proceso','atendida'=>'Atendida','cerrada'=>'Cerrada'] as $v => $l)
                        <option value="{{ $v }}" {{ request('status') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm">Prioridad</label>
                <select name="priority" id="report-priority" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    @foreach(['baja'=>'Baja','media'=>'Media','alta'=>'Alta','critica'=>'Crítica'] as $v => $l)
                        <option value="{{ $v }}" {{ request('priority') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm">Responsable</label>
                <select name="assigned_to" id="report-assignee" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($tecnicos as $tec)
                        <option value="{{ $tec->id }}" {{ request('assigned_to') == $tec->id ? 'selected' : '' }}>{{ $tec->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm">Categoría</label>
                <select name="category_id" id="report-category" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary btn-sm" id="btn-apply-filters">
                    <i class="bi bi-search me-1"></i>Aplicar Filtros
                </button>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x me-1"></i>Limpiar
                </a>
                <a href="{{ route('reports.export', request()->query()) }}"
                   class="btn btn-success btn-sm ms-auto" id="btn-export-csv">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>Exportar CSV
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()" id="btn-print">
                    <i class="bi bi-printer me-1"></i>Imprimir
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Tarjetas de resumen ──────────────────────────────────────────────────── --}}
<div class="row g-2 mb-3">
    @php
        $summaryItems = [
            ['label'=>'Total filtrado', 'value'=>$requests->total(), 'color'=>'#2563EB', 'icon'=>'bi-list-ul'],
            ['label'=>'Pendientes',     'value'=>$requests->where('status','pendiente')->count(),  'color'=>'#CA8A04', 'icon'=>'bi-hourglass'],
            ['label'=>'En Proceso',     'value'=>$requests->where('status','en_proceso')->count(), 'color'=>'#2563EB', 'icon'=>'bi-arrow-repeat'],
            ['label'=>'Atendidas',      'value'=>$requests->where('status','atendida')->count(),   'color'=>'#16A34A', 'icon'=>'bi-check-circle'],
            ['label'=>'Cerradas',       'value'=>$requests->where('status','cerrada')->count(),    'color'=>'#374151', 'icon'=>'bi-lock'],
        ];
    @endphp
    @foreach($summaryItems as $item)
    <div class="col">
        <div class="card border-0 rounded-3 text-center py-2" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
            <div style="font-size:1.5rem;font-weight:700;color:{{ $item['color'] }};">{{ $item['value'] }}</div>
            <div class="text-muted" style="font-size:.72rem;">{{ $item['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Tabla de resultados ──────────────────────────────────────────────────── --}}
<div class="card border-0 rounded-3" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold" style="font-size:.875rem;">
            Resultados — {{ $requests->total() }} registro(s)
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="report-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Solicitante</th>
                        <th>Responsable</th>
                        <th>Recurso</th>
                        <th>Creada</th>
                        <th>Atendida</th>
                        <th>Cerrada</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td class="py-2"><code style="font-size:.72rem;">{{ $req->ticket_code }}</code></td>
                        <td class="py-2" style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:.82rem;">{{ $req->title }}</td>
                        <td class="py-2"><small>{{ $req->category?->name ?? '-' }}</small></td>
                        <td class="py-2"><span class="badge {{ $req->priority_badge_class }}">{{ $req->priority_label }}</span></td>
                        <td class="py-2"><span class="badge {{ $req->status_badge_class }}">{{ $req->status_label }}</span></td>
                        <td class="py-2"><small>{{ $req->student?->name ?? '-' }}</small></td>
                        <td class="py-2"><small>{{ $req->assignedUser?->name ?? 'Sin asignar' }}</small></td>
                        <td class="py-2"><small>{{ $req->resource?->name ?? '-' }}</small></td>
                        <td class="py-2"><small class="text-muted">{{ $req->created_at?->format('d/m/Y') }}</small></td>
                        <td class="py-2"><small class="text-muted">{{ $req->attended_at?->format('d/m/Y') ?? '-' }}</small></td>
                        <td class="py-2"><small class="text-muted">{{ $req->closed_at?->format('d/m/Y') ?? '-' }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            No hay registros para los filtros seleccionados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($requests->hasPages())
    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Mostrando {{ $requests->firstItem() }}–{{ $requests->lastItem() }} de {{ $requests->total() }}
        </small>
        {{ $requests->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection

@push('styles')
<style>
@media print {
    #sidebar, #topbar, .card-header form, .btn { display: none !important; }
    #main-content { margin-left: 0 !important; }
    .card { box-shadow: none !important; }
}
</style>
@endpush
