<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * CU05 — Mostrar vista de reportes con filtros avanzados.
     */
    public function index(Request $request)
    {
        $query = ServiceRequest::with(['student', 'assignedUser', 'category', 'resource'])
            ->orderByDesc('created_at');

        // Filtro por rango de fechas
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por prioridad
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filtro por responsable
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filtro por categoría
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $requests   = $query->paginate(20)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $tecnicos   = User::where('role', 'tecnico')->orderBy('name')->get();

        // Resumen para la vista
        $summary = [
            'total'      => $query->toBase()->getCountForPagination(),
            'pendiente'  => (clone $query)->where('status', 'pendiente')->count(),
            'en_proceso' => (clone $query)->where('status', 'en_proceso')->count(),
            'atendida'   => (clone $query)->where('status', 'atendida')->count(),
            'cerrada'    => (clone $query)->where('status', 'cerrada')->count(),
        ];

        return view('reports.index', compact('requests', 'categories', 'tecnicos', 'summary'));
    }

    /**
     * CU05 — Exportar resultados filtrados a CSV.
     */
    public function export(Request $request)
    {
        $query = ServiceRequest::with(['student', 'assignedUser', 'category', 'resource'])
            ->orderByDesc('created_at');

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $records = $query->get();

        $filename = 'reporte_campus_connect_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel UTF-8 compatibility
            fputs($handle, "\xEF\xBB\xBF");

            // CSV headers
            fputcsv($handle, [
                'Código', 'Título', 'Categoría', 'Prioridad', 'Estado',
                'Solicitante', 'Responsable', 'Recurso',
                'Fecha Creación', 'Fecha Atención', 'Fecha Cierre',
            ]);

            foreach ($records as $r) {
                fputcsv($handle, [
                    $r->ticket_code,
                    $r->title,
                    $r->category?->name ?? '-',
                    $r->priority_label,
                    $r->status_label,
                    $r->student?->name ?? '-',
                    $r->assignedUser?->name ?? 'Sin asignar',
                    $r->resource?->name ?? '-',
                    $r->created_at?->format('d/m/Y H:i'),
                    $r->attended_at?->format('d/m/Y H:i') ?? '-',
                    $r->closed_at?->format('d/m/Y H:i') ?? '-',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
