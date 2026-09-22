<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\RequestHistory;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RequestManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $tecnico;
    private User $student;
    private Category $category;
    private ServiceRequest $serviceRequest;

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

        $this->serviceRequest = ServiceRequest::create([
            'ticket_code' => 'REQ-TEST-0001',
            'student_id'  => $this->student->id,
            'category_id' => $this->category->id,
            'title'       => 'Equipo sin internet',
            'description' => 'El equipo del Lab-01 no conecta.',
            'priority'    => 'alta',
            'status'      => 'pendiente',
        ]);
    }

    /**
     * TEST 3: El admin puede asignar un técnico a una solicitud.
     */
    public function test_admin_can_assign_responsible_to_request(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('requests.assign', $this->serviceRequest->id), [
                'assigned_to' => $this->tecnico->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verificar que se guardó en la BD
        $this->assertDatabaseHas('service_requests', [
            'id'          => $this->serviceRequest->id,
            'assigned_to' => $this->tecnico->id,
        ]);

        // Verificar que se registró en el historial
        $this->assertDatabaseHas('request_histories', [
            'request_id' => $this->serviceRequest->id,
            'user_id'    => $this->admin->id,
            'action'     => 'Asignación de técnico',
        ]);
    }

    /**
     * TEST 4: Al cambiar el estado, se actualiza la solicitud y se inserta en historial.
     */
    public function test_admin_can_update_request_status_and_record_history(): void
    {
        // Estado inicial: pendiente
        $this->assertEquals('pendiente', $this->serviceRequest->status);

        $response = $this->actingAs($this->admin)
            ->post(route('requests.updateStatus', $this->serviceRequest->id), [
                'status'  => 'en_proceso',
                'comment' => 'Iniciando atención del ticket.',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verificar actualización en BD
        $this->assertDatabaseHas('service_requests', [
            'id'     => $this->serviceRequest->id,
            'status' => 'en_proceso',
        ]);

        // Verificar registro en historial
        $this->assertDatabaseHas('request_histories', [
            'request_id'      => $this->serviceRequest->id,
            'user_id'         => $this->admin->id,
            'previous_status' => 'pendiente',
            'new_status'      => 'en_proceso',
        ]);

        // Verificar que el historial tiene el comentario
        $history = RequestHistory::where('request_id', $this->serviceRequest->id)
            ->where('new_status', 'en_proceso')
            ->first();

        $this->assertNotNull($history);
        $this->assertEquals('Iniciando atención del ticket.', $history->comment);
    }

    /**
     * No se puede hacer una transición de estado inválida (ej. pendiente → cerrada).
     */
    public function test_invalid_status_transition_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('requests.updateStatus', $this->serviceRequest->id), [
                'status' => 'cerrada', // saltar estados no permitido
            ]);

        $response->assertSessionHasErrors('status');

        // El estado no debe haber cambiado
        $this->assertDatabaseHas('service_requests', [
            'id'     => $this->serviceRequest->id,
            'status' => 'pendiente',
        ]);
    }

    /**
     * El listado de solicitudes carga correctamente con filtros.
     */
    public function test_requests_index_loads_with_status_filter(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('requests.index', ['status' => 'pendiente']));

        $response->assertStatus(200);
        $response->assertViewHas('requests');
    }
}
