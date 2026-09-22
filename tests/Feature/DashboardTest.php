<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
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

        $this->category = Category::create([
            'name'        => 'Soporte Tecnológico',
            'description' => 'Test category',
        ]);
    }

    /**
     * TEST 2: El dashboard carga con código 200 y muestra las métricas correctas.
     */
    public function test_dashboard_displays_correct_metrics(): void
    {
        $student = User::create([
            'name'     => 'Estudiante Test',
            'email'    => 'estudiante@test.edu',
            'password' => Hash::make('password'),
            'role'     => 'estudiante',
        ]);

        // Crear 3 solicitudes pendientes
        for ($i = 0; $i < 3; $i++) {
            ServiceRequest::create([
                'ticket_code' => "REQ-TEST-{$i}",
                'student_id'  => $student->id,
                'category_id' => $this->category->id,
                'title'       => "Solicitud pendiente {$i}",
                'description' => 'Descripción de prueba',
                'priority'    => 'media',
                'status'      => 'pendiente',
            ]);
        }

        // Crear 2 solicitudes cerradas
        for ($i = 0; $i < 2; $i++) {
            ServiceRequest::create([
                'ticket_code' => "REQ-CLOSED-{$i}",
                'student_id'  => $student->id,
                'category_id' => $this->category->id,
                'title'       => "Solicitud cerrada {$i}",
                'description' => 'Descripción de prueba',
                'priority'    => 'baja',
                'status'      => 'cerrada',
                'attended_at' => now()->subDays(2),
                'closed_at'   => now()->subDay(),
            ]);
        }

        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertStatus(200);

        // Verificar que la vista tiene los datos correctos
        $response->assertViewHas('totalRequests', 5);
        $response->assertViewHas('totalPending', 3);
        $response->assertViewHas('totalAttended', 2); // cerradas count as attended
    }

    /**
     * Un usuario no autenticado no puede acceder al dashboard.
     */
    public function test_unauthenticated_user_is_redirected_from_dashboard(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }
}
