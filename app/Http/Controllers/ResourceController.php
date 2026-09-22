<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ResourceController extends Controller
{
    /**
     * CU04 — Listar todos los recursos institucionales.
     */
    public function index(Request $request)
    {
        $query = Resource::orderBy('name');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $resources = $query->paginate(12)->withQueryString();

        return view('resources-mgmt.index', compact('resources'));
    }

    /**
     * CU04 — Mostrar formulario de creación.
     */
    public function create()
    {
        return view('resources-mgmt.create');
    }

    /**
     * CU04 — Almacenar nuevo recurso.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'code'        => ['required', 'string', 'max:20', 'unique:resources,code'],
            'type'        => ['required', Rule::in(Resource::TYPES)],
            'status'      => ['required', Rule::in(Resource::STATUSES)],
            'description' => ['nullable', 'string'],
        ]);

        Resource::create($validated);

        return redirect()->route('resources.index')
            ->with('success', 'Recurso creado correctamente.');
    }

    /**
     * CU04 — Mostrar formulario de edición.
     */
    public function edit(Resource $resource)
    {
        return view('resources-mgmt.edit', compact('resource'));
    }

    /**
     * CU04 — Actualizar recurso existente.
     */
    public function update(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'code'        => ['required', 'string', 'max:20', Rule::unique('resources', 'code')->ignore($resource->id)],
            'type'        => ['required', Rule::in(Resource::TYPES)],
            'status'      => ['required', Rule::in(Resource::STATUSES)],
            'description' => ['nullable', 'string'],
        ]);

        $resource->update($validated);

        return redirect()->route('resources.index')
            ->with('success', 'Recurso actualizado correctamente.');
    }

    /**
     * CU04 — Eliminar recurso (soft check: si tiene solicitudes activas, denegar).
     */
    public function destroy(Resource $resource)
    {
        $activeRequests = $resource->serviceRequests()
            ->whereIn('status', ['pendiente', 'en_proceso'])
            ->count();

        if ($activeRequests > 0) {
            return back()->with('error', "No se puede eliminar '{$resource->name}' porque tiene {$activeRequests} solicitud(es) activa(s).");
        }

        $resource->delete();

        return redirect()->route('resources.index')
            ->with('success', 'Recurso eliminado correctamente.');
    }
}
