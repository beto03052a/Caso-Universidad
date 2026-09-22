<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * CU02 — Dashboard administrativo con métricas consolidadas.
     */
    public function index()
    {
        // ─── KPI Cards ────────────────────────────────────────────────────────
        $totalRequests  = ServiceRequest::count();
        $totalPending   = ServiceRequest::where('status', 'pendiente')->count();
        $totalInProcess = ServiceRequest::where('status', 'en_proceso')->count();
        $totalAttended  = ServiceRequest::whereIn('status', ['atendida', 'cerrada'])->count();
        $avgHours       = ServiceRequest::avgAttentionHours();

        // ─── Distribución por Categoría ───────────────────────────────────────
        $byCategory = Category::withCount('serviceRequests')
            ->orderByDesc('service_requests_count')
            ->get();

        // ─── Distribución por Prioridad ───────────────────────────────────────
        $byPriority = ServiceRequest::select('priority', DB::raw('count(*) as total'))
            ->groupBy('priority')
            ->orderByRaw("CASE priority WHEN 'critica' THEN 1 WHEN 'alta' THEN 2 WHEN 'media' THEN 3 ELSE 4 END")
            ->get();

        // ─── Distribución por Estado ──────────────────────────────────────────
        $byStatus = ServiceRequest::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [$item->status => $item->total]);

        // ─── Carga de trabajo por Técnico asignado ────────────────────────────
        $workloadByTecnico = User::where('role', 'tecnico')
            ->withCount([
                'assignedRequests',
                'assignedRequests as pending_count'   => fn($q) => $q->where('status', 'pendiente'),
                'assignedRequests as inprocess_count' => fn($q) => $q->where('status', 'en_proceso'),
                'assignedRequests as attended_count'  => fn($q) => $q->whereIn('status', ['atendida', 'cerrada']),
            ])
            ->get();

        // ─── Solicitudes recientes (últimas 8) ────────────────────────────────
        $recentRequests = ServiceRequest::with(['student', 'category'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return view('dashboard.index', compact(
            'totalRequests',
            'totalPending',
            'totalInProcess',
            'totalAttended',
            'avgHours',
            'byCategory',
            'byPriority',
            'byStatus',
            'workloadByTecnico',
            'recentRequests'
        ));
    }
}
