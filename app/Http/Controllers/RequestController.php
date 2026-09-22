<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\RequestHistory;
use App\Models\Resource;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    /**
     * CU03 — Bandeja de solicitudes con filtros.
     */
    public function index(Request $request)
    {
        $query = ServiceRequest::with(['student', 'assignedUser', 'category', 'resource'])
            ->orderByDesc('created_at');

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ticket_code', 'like', "%{$search}%");
            });
        }

        $requests   = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $tecnicos   = User::where('role', 'tecnico')->orderBy('name')->get();

        return view('requests.index', compact('requests', 'categories', 'tecnicos'));
    }

    /**
     * CU03 — Detalle de una solicitud con historial y evidencias.
     */
    public function show(int $id)
    {
        $serviceRequest = ServiceRequest::with([
            'student',
            'assignedUser',
            'category',
            'resource',
            'evidences',
            'histories.user',
        ])->findOrFail($id);

        $tecnicos = User::where('role', 'tecnico')->orderBy('name')->get();
        $allowedStatuses = $serviceRequest->allowedNextStatuses();

        return view('requests.show', compact('serviceRequest', 'tecnicos', 'allowedStatuses'));
    }

    /**
     * CU03 — Asignar técnico responsable a una solicitud.
     */
    public function assign(Request $request, int $id)
    {
        $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        $serviceRequest = ServiceRequest::findOrFail($id);
        $tecnico        = User::findOrFail($request->assigned_to);

        // Solo técnicos pueden ser asignados
        if (!$tecnico->isTecnico()) {
            return back()->with('error', 'El usuario seleccionado no es un técnico.');
        }

        $previousAssignee = $serviceRequest->assignedUser;
        $serviceRequest->update(['assigned_to' => $tecnico->id]);

        // Registrar en historial
        RequestHistory::create([
            'request_id'      => $serviceRequest->id,
            'user_id'         => auth()->id(),
            'action'          => 'Asignación de técnico',
            'comment'         => "Responsable asignado: {$tecnico->name}" . ($previousAssignee ? " (anterior: {$previousAssignee->name})" : ''),
            'previous_status' => $serviceRequest->status,
            'new_status'      => $serviceRequest->status,
        ]);

        return back()->with('success', "Solicitud asignada a {$tecnico->name} correctamente.");
    }

    /**
     * CU03 — Actualizar el estado de una solicitud con validación de transiciones.
     */
    public function updateStatus(Request $request, int $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        $allowed = $serviceRequest->allowedNextStatuses();

        $request->validate([
            'status'  => ['required', 'in:' . implode(',', $allowed)],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $previousStatus = $serviceRequest->status;
        $newStatus      = $request->status;

        $updateData = ['status' => $newStatus];

        if ($newStatus === 'atendida' && !$serviceRequest->attended_at) {
            $updateData['attended_at'] = now();
        }
        if ($newStatus === 'cerrada' && !$serviceRequest->closed_at) {
            $updateData['closed_at'] = now();
        }

        $serviceRequest->update($updateData);

        // Etiquetas legibles
        $statusLabels = [
            'en_proceso' => 'En Proceso',
            'atendida'   => 'Atendida',
            'cerrada'    => 'Cerrada',
        ];

        RequestHistory::create([
            'request_id'      => $serviceRequest->id,
            'user_id'         => auth()->id(),
            'action'          => 'Cambio de estado a ' . ($statusLabels[$newStatus] ?? $newStatus),
            'comment'         => $request->comment,
            'previous_status' => $previousStatus,
            'new_status'      => $newStatus,
        ]);

        return back()->with('success', 'Estado actualizado correctamente.');
    }
}
