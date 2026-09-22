@extends('layouts.app')
@section('title', 'Solicitudes')
@section('page-title', 'Bandeja de Solicitudes')

@section('content')

{{-- ── Filtros ──────────────────────────────────────────────────────────────── --}}
<div class="card border-0 rounded-3 mb-4" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
    <div class="card-header bg-transparent border-0">
        <h6 class="mb-0 fw-semibold" style="font-size:.875rem;">
            <i class="bi bi-funnel me-1 text-primary"></i>Filtros
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('requests.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label form-label-sm">Buscar</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Código o título..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm">Estado</label>
                <select name="status" id="filter-status" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach(['pendiente'=>'Pendiente','en_proceso'=>'En Proceso','atendida'=>'Atendida','cerrada'=>'Cerrada'] as $val => $lab)
                        <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $lab }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm">Prioridad</label>
                <select name="priority" id="filter-priority" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    @foreach(['baja'=>'Baja','media'=>'Media','alta'=>'Alta','critica'=>'Crítica'] as $val => $lab)
                        <option value="{{ $val }}" {{ request('priority') == $val ? 'selected' : '' }}>{{ $lab }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm">Categoría</label>
                <select name="category_id" id="filter-category" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm">Responsable</label>
                <select name="assigned_to" id="filter-assignee" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($tecnicos as $tec)
                        <option value="{{ $tec->id }}" {{ request('assigned_to') == $tec->id ? 'selected' : '' }}>{{ $tec->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm w-100" id="btn-filter">
                    <i class="bi bi-search"></i>
                </button>
                <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ── Tabla ─────────────────────────────────────────────────────────────────── --}}
<div class="card border-0 rounded-3" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold" style="font-size:.875rem;">
            <i class="bi bi-list-ul me-1"></i>{{ $requests->total() }} solicitud(es)
        </h6>
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
                        <th>Responsable</th>
                        <th>Solicitante</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td class="py-2 align-middle"><code style="font-size:.75rem;">{{ $req->ticket_code }}</code></td>
                        <td class="py-2 align-middle" style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $req->title }}
                        </td>
                        <td class="py-2 align-middle"><small>{{ $req->category?->name ?? '-' }}</small></td>
                        <td class="py-2 align-middle">
                            <span class="badge {{ $req->priority_badge_class }}">{{ $req->priority_label }}</span>
                        </td>
                        <td class="py-2 align-middle">
                            <span class="badge {{ $req->status_badge_class }}">{{ $req->status_label }}</span>
                        </td>
                        <td class="py-2 align-middle">
                            <small>{{ $req->assignedUser?->name ?? '<span class="text-muted">Sin asignar</span>' }}</small>
                        </td>
                        <td class="py-2 align-middle"><small>{{ $req->student?->name }}</small></td>
                        <td class="py-2 align-middle"><small class="text-muted">{{ $req->created_at->format('d/m/Y') }}</small></td>
                        <td class="py-2 align-middle">
                            <a href="{{ route('requests.show', $req->id) }}" class="btn btn-sm btn-outline-primary" id="btn-view-{{ $req->id }}">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            No se encontraron solicitudes con los filtros aplicados.
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
