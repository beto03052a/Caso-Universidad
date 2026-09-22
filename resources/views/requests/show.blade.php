@extends('layouts.app')
@section('title', $serviceRequest->ticket_code)
@section('page-title', 'Detalle de Solicitud')

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb" style="font-size:.8rem;">
        <li class="breadcrumb-item"><a href="{{ route('requests.index') }}">Solicitudes</a></li>
        <li class="breadcrumb-item active">{{ $serviceRequest->ticket_code }}</li>
    </ol>
</nav>

<div class="row g-3">

    {{-- ─── Columna izquierda: Datos de la solicitud ─────────────────────── --}}
    <div class="col-lg-8">

        {{-- Header card --}}
        <div class="card border-0 rounded-3 mb-3" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <div>
                        <code class="text-muted" style="font-size:.8rem;">{{ $serviceRequest->ticket_code }}</code>
                        <h5 class="mb-0 mt-1 fw-semibold">{{ $serviceRequest->title }}</h5>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge {{ $serviceRequest->priority_badge_class }} fs-6">
                            {{ $serviceRequest->priority_label }}
                        </span>
                        <span class="badge {{ $serviceRequest->status_badge_class }} fs-6">
                            {{ $serviceRequest->status_label }}
                        </span>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block fw-semibold mb-1">Solicitante</small>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:30px;height:30px;border-radius:50%;background:#E0F2FE;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:600;color:#0284C7;">
                                {{ strtoupper(substr($serviceRequest->student?->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-size:.875rem;font-weight:500;">{{ $serviceRequest->student?->name }}</div>
                                <div style="font-size:.75rem;color:#64748B;">{{ $serviceRequest->student?->email }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fw-semibold mb-1">Categoría</small>
                        <div style="font-size:.875rem;">{{ $serviceRequest->category?->name ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fw-semibold mb-1">Recurso Afectado</small>
                        @if($serviceRequest->resource)
                            <div style="font-size:.875rem;">
                                <span class="badge bg-light text-dark border">{{ $serviceRequest->resource->code }}</span>
                                {{ $serviceRequest->resource->name }}
                            </div>
                        @else
                            <span class="text-muted" style="font-size:.875rem;">Sin recurso especificado</span>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fw-semibold mb-1">Fecha de Creación</small>
                        <div style="font-size:.875rem;">{{ $serviceRequest->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @if($serviceRequest->attended_at)
                    <div class="col-sm-6">
                        <small class="text-muted d-block fw-semibold mb-1">Fecha de Atención</small>
                        <div style="font-size:.875rem;">{{ $serviceRequest->attended_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif
                    @if($serviceRequest->closed_at)
                    <div class="col-sm-6">
                        <small class="text-muted d-block fw-semibold mb-1">Fecha de Cierre</small>
                        <div style="font-size:.875rem;">{{ $serviceRequest->closed_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif
                </div>

                <div>
                    <small class="text-muted d-block fw-semibold mb-1">Descripción</small>
                    <p style="font-size:.875rem;color:#374151;white-space:pre-wrap;">{{ $serviceRequest->description }}</p>
                </div>
            </div>
        </div>

        {{-- Evidencias --}}
        @if($serviceRequest->evidences->isNotEmpty())
        <div class="card border-0 rounded-3 mb-3" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
            <div class="card-header bg-transparent border-0">
                <h6 class="mb-0 fw-semibold" style="font-size:.875rem;">
                    <i class="bi bi-paperclip me-1 text-primary"></i>Evidencias Adjuntas ({{ $serviceRequest->evidences->count() }})
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($serviceRequest->evidences as $ev)
                    <div class="col-auto">
                        <div class="border rounded-2 p-2 d-flex align-items-center gap-2" style="font-size:.8rem;">
                            @if($ev->isImage())
                                <i class="bi bi-image text-success fs-5"></i>
                            @else
                                <i class="bi bi-file-earmark-pdf text-danger fs-5"></i>
                            @endif
                            <div>
                                <div>{{ basename($ev->file_path) }}</div>
                                <small class="text-muted">{{ $ev->file_type }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Timeline de historial --}}
        <div class="card border-0 rounded-3" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
            <div class="card-header bg-transparent border-0">
                <h6 class="mb-0 fw-semibold" style="font-size:.875rem;">
                    <i class="bi bi-clock-history me-1 text-primary"></i>Historial de Trazabilidad
                </h6>
            </div>
            <div class="card-body">
                @if($serviceRequest->histories->isEmpty())
                    <p class="text-muted text-center py-3" style="font-size:.875rem;">Sin registros en el historial.</p>
                @else
                <div class="timeline">
                    @foreach($serviceRequest->histories as $h)
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <strong style="font-size:.82rem;">{{ $h->action }}</strong>
                            <small class="text-muted">{{ $h->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        @if($h->previous_status && $h->new_status && $h->previous_status !== $h->new_status)
                        <div class="mb-1">
                            <span class="badge bg-secondary" style="font-size:.68rem;">{{ $h->previous_status }}</span>
                            <i class="bi bi-arrow-right mx-1" style="font-size:.7rem;"></i>
                            <span class="badge bg-primary" style="font-size:.68rem;">{{ $h->new_status }}</span>
                        </div>
                        @endif
                        @if($h->comment)
                            <p class="text-muted mb-1" style="font-size:.8rem;">{{ $h->comment }}</p>
                        @endif
                        <small class="text-muted" style="font-size:.72rem;">
                            <i class="bi bi-person me-1"></i>{{ $h->user?->name ?? 'Sistema' }}
                        </small>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ─── Columna derecha: Acciones ────────────────────────────────────── --}}
    <div class="col-lg-4">

        {{-- Asignar Responsable --}}
        @if(auth()->user()->isAdmin())
        <div class="card border-0 rounded-3 mb-3" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
            <div class="card-header bg-transparent border-0">
                <h6 class="mb-0 fw-semibold" style="font-size:.875rem;">
                    <i class="bi bi-person-check me-1 text-success"></i>Asignar Responsable
                </h6>
            </div>
            <div class="card-body">
                @if($serviceRequest->assignedUser)
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div style="width:32px;height:32px;border-radius:50%;background:#D1FAE5;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:600;color:#065F46;">
                        {{ strtoupper(substr($serviceRequest->assignedUser->name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:.82rem;font-weight:500;">{{ $serviceRequest->assignedUser->name }}</div>
                        <div style="font-size:.72rem;color:#64748B;">Técnico asignado</div>
                    </div>
                </div>
                @endif

                @if(!in_array($serviceRequest->status, ['cerrada']))
                <form method="POST" action="{{ route('requests.assign', $serviceRequest->id) }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label form-label-sm">Técnico Responsable</label>
                        <select name="assigned_to" id="select-tecnico" class="form-select form-select-sm" required>
                            <option value="">-- Seleccionar técnico --</option>
                            @foreach($tecnicos as $tec)
                                <option value="{{ $tec->id }}" {{ $serviceRequest->assigned_to == $tec->id ? 'selected' : '' }}>
                                    {{ $tec->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm w-100" id="btn-assign">
                        <i class="bi bi-person-check me-1"></i>
                        {{ $serviceRequest->assigned_to ? 'Reasignar' : 'Asignar' }}
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endif

        {{-- Actualizar Estado --}}
        @if(!empty($allowedStatuses) && !in_array($serviceRequest->status, ['cerrada']))
        <div class="card border-0 rounded-3 mb-3" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
            <div class="card-header bg-transparent border-0">
                <h6 class="mb-0 fw-semibold" style="font-size:.875rem;">
                    <i class="bi bi-arrow-right-circle me-1 text-primary"></i>Actualizar Estado
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('requests.updateStatus', $serviceRequest->id) }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label form-label-sm">Nuevo Estado</label>
                        <select name="status" id="select-status" class="form-select form-select-sm" required>
                            <option value="">-- Seleccionar estado --</option>
                            @php
                                $statusLabels = ['en_proceso'=>'En Proceso','atendida'=>'Atendida','cerrada'=>'Cerrada'];
                            @endphp
                            @foreach($allowedStatuses as $s)
                                <option value="{{ $s }}">{{ $statusLabels[$s] ?? $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label form-label-sm">Comentario (opcional)</label>
                        <textarea name="comment" id="comment-status" class="form-control form-control-sm" rows="3" placeholder="Describe la acción realizada..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100" id="btn-update-status">
                        <i class="bi bi-check-circle me-1"></i>Actualizar Estado
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Info card estado actual --}}
        <div class="card border-0 rounded-3" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
            <div class="card-body">
                <h6 class="fw-semibold mb-3" style="font-size:.875rem;">Resumen de la Solicitud</h6>
                <dl class="mb-0" style="font-size:.82rem;">
                    <dt class="text-muted fw-normal">Estado actual</dt>
                    <dd><span class="badge {{ $serviceRequest->status_badge_class }}">{{ $serviceRequest->status_label }}</span></dd>
                    <dt class="text-muted fw-normal">Prioridad</dt>
                    <dd><span class="badge {{ $serviceRequest->priority_badge_class }}">{{ $serviceRequest->priority_label }}</span></dd>
                    <dt class="text-muted fw-normal">Responsable</dt>
                    <dd>{{ $serviceRequest->assignedUser?->name ?? 'Sin asignar' }}</dd>
                    <dt class="text-muted fw-normal">Creada</dt>
                    <dd>{{ $serviceRequest->created_at->diffForHumans() }}</dd>
                    @if($serviceRequest->histories->count())
                    <dt class="text-muted fw-normal">Registros historial</dt>
                    <dd>{{ $serviceRequest->histories->count() }} acciones</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>

@endsection
