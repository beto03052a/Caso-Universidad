<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TEST 1: Un admin puede autenticarse y ser redirigido al dashboard.
     */
    public function test_admin_can_login_successfully(): void
    {
        $admin = User::create([
            'name'     => 'Admin Test',
            'email'    => 'admin@campusconnect.edu',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        $response = $this->post(route('login.post'), [
            'email'    => 'admin@campusconnect.edu',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * Un estudiante NO puede acceder al panel administrativo.
     */
    public function test_student_is_blocked_from_admin_panel(): void
    {
        User::create([
            'name'     => 'Estudiante Test',
            'email'    => 'estudiante@test.edu',
            'password' => Hash::make('password'),
            'role'     => 'estudiante',
        ]);

        $response = $this->post(route('login.post'), [
            'email'    => 'estudiante@test.edu',
            'password' => 'password',
        ]);

        // Should redirect back with error (not to dashboard)
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Credenciales incorrectas devuelven error de validación.
     */
    public function test_invalid_credentials_return_error(): void
    {
        $response = $this->post(route('login.post'), [
            'email'    => 'noexiste@campusconnect.edu',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Un técnico también puede iniciar sesión.
     */
    public function test_tecnico_can_login_successfully(): void
    {
        $tecnico = User::create([
            'name'     => 'Técnico Test',
            'email'    => 'tecnico@campusconnect.edu',
            'password' => Hash::make('password'),
            'role'     => 'tecnico',
        ]);

        $response = $this->post(route('login.post'), [
            'email'    => 'tecnico@campusconnect.edu',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($tecnico);
    }
}
