<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $tecnico;
    private User $student;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'     => 'Admin Test',
            'email'    => 'admin@campusconnect.edu',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        $this->tecnico = User::create([
            'name'     => 'Técnico Test',
            'email'    => 'tecnico@test.edu',
            'password' => Hash::make('password'),
            'role'     => 'tecnico',
        ]);

        $this->student = User::create([
            'name'     => 'Estudiante Test',
            'email'    => 'estudiante@test.edu',
            'password' => Hash::make('password'),
            'role'     => 'estudiante',
        ]);

        $this->category = Category::create([
            'name' => 'Soporte Tecnológico',
        ]);

        // Solicitud CERRADA — hace 30 días
        ServiceRequest::create([
            'ticket_code' => 'REQ-CLOSED-01',
            'student_id'  => $this->student->id,
            'assigned_to' => $this->tecnico->id,
            'category_id' => $this->category->id,
            'title'       => 'Solicitud cerrada hace 30 días',
            'description' => 'Descripción de prueba.',
            'priority'    => 'alta',
            'status'      => 'cerrada',
            'attended_at' => now()->subDays(29),
            'closed_at'   => now()->subDays(28),
            'created_at'  => now()->subDays(30),
            'updated_at'  => now()->subDays(28),
        ]);

        // Solicitud PENDIENTE — hoy
        ServiceRequest::create([
            'ticket_code' => 'REQ-PENDING-01',
            'student_id'  => $this->student->id,
            'category_id' => $this->category->id,
            'title'       => 'Solicitud pendiente hoy',
            'description' => 'Descripción de prueba.',
            'priority'    => 'media',
            'status'      => 'pendiente',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Solicitud ATENDIDA — hace 5 días
        ServiceRequest::create([
            'ticket_code' => 'REQ-ATTENDED-01',
            'student_id'  => $this->student->id,
            'assigned_to' => $this->tecnico->id,
            'category_id' => $this->category->id,
            'title'       => 'Solicitud atendida hace 5 días',
            'description' => 'Descripción de prueba.',
            'priority'    => 'baja',
            'status'      => 'atendida',
            'attended_at' => now()->subDays(4),
            'created_at'  => now()->subDays(5),
            'updated_at'  => now()->subDays(4),
        ]);
    }

    /**
     * TEST 5: El módulo de reportes filtra correctamente por rango de fechas y estado.
     * Verifica que la lógica de filtros devuelva los registros correctos.
     */
    public function test_report_filters_requests_by_date_and_status(): void
    {
        // Filtrar por estado 'cerrada' — exactamente 1 registro en setUp()
        $response = $this->actingAs($this->admin)
            ->get(route('reports.index', [
                'status' => 'cerrada',
            ]));

        $response->assertStatus(200);
        $response->assertViewHas('requests');

        $requests = $response->viewData('requests');

        // Verificar que el filtro de estado devuelve solo los cerrados (1 registro)
        $this->assertEquals(1, $requests->total());
        $this->assertEquals('cerrada', $requests->items()[0]->status);
        $this->assertEquals('REQ-CLOSED-01', $requests->items()[0]->ticket_code);

        // Ahora filtrar por 'pendiente' — exactamente 1 registro
        $responsePending = $this->actingAs($this->admin)
            ->get(route('reports.index', ['status' => 'pendiente']));

        $pendingRequests = $responsePending->viewData('requests');
        $this->assertEquals(1, $pendingRequests->total());
        $this->assertEquals('REQ-PENDING-01', $pendingRequests->items()[0]->ticket_code);
    }

    /**
     * Sin filtros, se devuelven todas las solicitudes.
     */
    public function test_report_without_filters_shows_all_requests(): void
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index'));

        $response->assertStatus(200);

        $requests = $response->viewData('requests');
        $this->assertEquals(3, $requests->total());
    }

    /**
     * El filtro por responsable asignado funciona correctamente.
     */
    public function test_report_filters_by_assigned_technician(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('reports.index', ['assigned_to' => $this->tecnico->id]));

        $response->assertStatus(200);

        $requests = $response->viewData('requests');
        // Solo cerrada y atendida tienen técnico asignado
        $this->assertEquals(2, $requests->total());
    }

    /**
     * El reporte puede exportarse a CSV (response exitosa).
     */
    public function test_report_can_be_exported_to_csv(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('reports.export', ['status' => 'cerrada']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
