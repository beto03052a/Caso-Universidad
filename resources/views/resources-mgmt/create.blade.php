@extends('layouts.app')
@section('title', 'Nuevo Recurso')
@section('page-title', 'Crear Recurso Institucional')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 rounded-3" style="box-shadow:0 1px 6px rgba(0,0,0,.07);">
            <div class="card-header bg-transparent border-0">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-plus-circle me-1 text-primary"></i>Nuevo Recurso
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('resources.store') }}" id="form-create-resource">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label form-label-sm fw-semibold">Nombre del Recurso <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="Ej. Laboratorio de Cómputo 3" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label form-label-sm fw-semibold">Código Único <span class="text-danger">*</span></label>
                        <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code') }}" placeholder="Ej. LAB-03" style="text-transform:uppercase;" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="type" class="form-label form-label-sm fw-semibold">Tipo <span class="text-danger">*</span></label>
                            <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">-- Seleccionar --</option>
                                <option value="infraestructura" {{ old('type') == 'infraestructura' ? 'selected' : '' }}>Infraestructura</option>
                                <option value="equipamiento"    {{ old('type') == 'equipamiento'    ? 'selected' : '' }}>Equipamiento</option>
                                <option value="servicio"        {{ old('type') == 'servicio'        ? 'selected' : '' }}>Servicio</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label form-label-sm fw-semibold">Estado <span class="text-danger">*</span></label>
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Seleccionar --</option>
                                <option value="operativo"        {{ old('status', 'operativo') == 'operativo'        ? 'selected' : '' }}>Operativo</option>
                                <option value="en_mantenimiento" {{ old('status') == 'en_mantenimiento' ? 'selected' : '' }}>En Mantenimiento</option>
                                <option value="fuera_servicio"   {{ old('status') == 'fuera_servicio'   ? 'selected' : '' }}>Fuera de Servicio</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label form-label-sm fw-semibold">Descripción</label>
                        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Descripción breve del recurso...">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="btn-create-resource">
                            <i class="bi bi-save me-1"></i>Guardar Recurso
                        </button>
                        <a href="{{ route('resources.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
