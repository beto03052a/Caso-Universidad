@extends('layouts.app')
@section('title', 'Recursos')
@section('page-title', 'Gestión de Recursos Institucionales')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <small class="text-muted">Aulas, laboratorios, equipos y servicios institucionales.</small>
    </div>
    <a href="{{ route('resources.create') }}" class="btn btn-primary btn-sm" id="btn-new-resource">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Recurso
    </a>
</div>

{{-- Filtros --}}
<div class="card border-0 rounded-3 mb-3" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('resources.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Buscar por nombre o código..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="type" class="form-select form-select-sm">
                    <option value="">Todos los tipos</option>
                    <option value="infraestructura" {{ request('type') == 'infraestructura' ? 'selected' : '' }}>Infraestructura</option>
                    <option value="equipamiento"    {{ request('type') == 'equipamiento'    ? 'selected' : '' }}>Equipamiento</option>
                    <option value="servicio"        {{ request('type') == 'servicio'        ? 'selected' : '' }}>Servicio</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Todos los estados</option>
                    <option value="operativo"        {{ request('status') == 'operativo'        ? 'selected' : '' }}>Operativo</option>
                    <option value="en_mantenimiento" {{ request('status') == 'en_mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                    <option value="fuera_servicio"   {{ request('status') == 'fuera_servicio'   ? 'selected' : '' }}>Fuera de Servicio</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Filtrar</button>
                <a href="{{ route('resources.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Grid de recursos --}}
<div class="row g-3">
    @forelse($resources as $resource)
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 rounded-3 h-100" style="box-shadow:0 1px 6px rgba(0,0,0,.07);transition:transform .2s,box-shadow .2s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,.10)'" onmouseout="this.style.transform='';this.style.boxShadow='0 1px 6px rgba(0,0,0,.07)'">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="badge bg-light text-dark border mb-1" style="font-size:.7rem;">{{ $resource->code }}</span>
                        <h6 class="mb-0 fw-semibold" style="font-size:.875rem;">{{ $resource->name }}</h6>
                    </div>
                    <span class="badge {{ $resource->status_badge_class }}" style="font-size:.7rem;">{{ $resource->status_label }}</span>
                </div>
                <p class="text-muted mb-2" style="font-size:.78rem;min-height:36px;">{{ $resource->description ?? 'Sin descripción.' }}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge bg-light text-dark border" style="font-size:.7rem;">
                        <i class="bi bi-tag me-1"></i>{{ $resource->type_label }}
                    </span>
                    <div class="d-flex gap-1">
                        <a href="{{ route('resources.edit', $resource) }}" class="btn btn-sm btn-outline-primary" id="btn-edit-{{ $resource->id }}">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @if(auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('resources.destroy', $resource) }}" onsubmit="return confirm('¿Eliminar el recurso {{ addslashes($resource->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" id="btn-delete-{{ $resource->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center text-muted py-5">
            <i class="bi bi-buildings fs-1 d-block mb-2"></i>
            No hay recursos registrados. <a href="{{ route('resources.create') }}">Crear el primero</a>.
        </div>
    </div>
    @endforelse
</div>

@if($resources->hasPages())
<div class="mt-3 d-flex justify-content-center">
    {{ $resources->links('pagination::bootstrap-5') }}
</div>
@endif

@endsection
