<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Resource;
use App\Models\ServiceRequest;
use App\Models\RequestHistory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. USUARIOS ──────────────────────────────────────────────────────

        $admin = User::create([
            'name'     => 'Administrador General',
            'email'    => 'admin@campusconnect.edu',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        $tecnicos = [
            User::create(['name' => 'Carlos Mendoza', 'email' => 'carlos.mendoza@campusconnect.edu', 'password' => Hash::make('password'), 'role' => 'tecnico']),
            User::create(['name' => 'Diana Ríos',     'email' => 'diana.rios@campusconnect.edu',     'password' => Hash::make('password'), 'role' => 'tecnico']),
            User::create(['name' => 'Ernesto Vargas', 'email' => 'ernesto.vargas@campusconnect.edu', 'password' => Hash::make('password'), 'role' => 'tecnico']),
        ];

        $estudiantes = [
            User::create(['name' => 'Lucía Torres',   'email' => 'lucia.torres@estudiante.edu',   'password' => Hash::make('password'), 'role' => 'estudiante']),
            User::create(['name' => 'Miguel Herrera', 'email' => 'miguel.herrera@estudiante.edu', 'password' => Hash::make('password'), 'role' => 'estudiante']),
            User::create(['name' => 'Sofía Castro',   'email' => 'sofia.castro@estudiante.edu',   'password' => Hash::make('password'), 'role' => 'estudiante']),
            User::create(['name' => 'Andrés Muñoz',   'email' => 'andres.munoz@estudiante.edu',   'password' => Hash::make('password'), 'role' => 'estudiante']),
            User::create(['name' => 'Valentina Ruz',  'email' => 'valentina.ruz@estudiante.edu',  'password' => Hash::make('password'), 'role' => 'estudiante']),
        ];

        // ─── 2. CATEGORÍAS ────────────────────────────────────────────────────

        $catSoporte = Category::create(['name' => 'Soporte Tecnológico', 'description' => 'Problemas con equipos de cómputo, redes y software institucional.']);
        $catMantto  = Category::create(['name' => 'Mantenimiento',       'description' => 'Reparación y mantenimiento preventivo de instalaciones y equipos.']);
        $catInfra   = Category::create(['name' => 'Infraestructura',     'description' => 'Solicitudes relacionadas con espacios físicos, aulas y edificios.']);
        $catEquipo  = Category::create(['name' => 'Equipamiento',        'description' => 'Solicitudes de adquisición o reemplazo de mobiliario y equipos.']);

        $categorias = [$catSoporte, $catMantto, $catInfra, $catEquipo];

        // ─── 3. RECURSOS INSTITUCIONALES ──────────────────────────────────────

        $recursos = [
            Resource::create(['name' => 'Laboratorio de Cómputo 1',      'code' => 'LAB-01', 'type' => 'infraestructura', 'status' => 'operativo',        'description' => 'Laboratorio principal con 40 equipos Dell.']),
            Resource::create(['name' => 'Laboratorio de Cómputo 2',      'code' => 'LAB-02', 'type' => 'infraestructura', 'status' => 'en_mantenimiento', 'description' => 'Laboratorio secundario en mantenimiento.']),
            Resource::create(['name' => 'Aula Magna Edificio Central',   'code' => 'AUL-01', 'type' => 'infraestructura', 'status' => 'operativo',        'description' => 'Auditorio principal con capacidad 200 personas.']),
            Resource::create(['name' => 'Proyector Aula 302',            'code' => 'EQP-01', 'type' => 'equipamiento',    'status' => 'fuera_servicio',   'description' => 'Proyector Epson EX5260 - requiere reparación.']),
            Resource::create(['name' => 'Red Wi-Fi Campus Norte',        'code' => 'SRV-01', 'type' => 'servicio',        'status' => 'operativo',        'description' => 'Infraestructura de red inalámbrica campus norte.']),
            Resource::create(['name' => 'Sala de Servidores Edificio B', 'code' => 'SRV-02', 'type' => 'infraestructura', 'status' => 'operativo',        'description' => 'Centro de datos con UPS y sistema de refrigeración.']),
        ];

        // ─── 4. SOLICITUDES (20+) CON ESTADOS/PRIORIDADES VARIADOS ───────────

        $solicitudes = [
            // CERRADAS (simulando 30-90 días atrás)
            ['student' => $estudiantes[0], 'tecnico' => $tecnicos[0], 'category' => $catSoporte, 'resource' => $recursos[0], 'title' => 'Equipo sin acceso a internet',          'desc' => 'El equipo #12 del Lab-01 no conecta a la red desde el lunes.', 'priority' => 'alta',   'status' => 'cerrada',    'daysAgo' => 85, 'attendedDays' => 83, 'closedDays' => 82],
            ['student' => $estudiantes[1], 'tecnico' => $tecnicos[1], 'category' => $catMantto,  'resource' => $recursos[2], 'title' => 'Aire acondicionado dañado Aula Magna',  'desc' => 'El AC central del auditorio deja de enfriar a los 30 min.', 'priority' => 'critica', 'status' => 'cerrada',    'daysAgo' => 70, 'attendedDays' => 68, 'closedDays' => 65],
            ['student' => $estudiantes[2], 'tecnico' => $tecnicos[2], 'category' => $catInfra,   'resource' => $recursos[2], 'title' => 'Fuga de agua en techo Edificio B',      'desc' => 'Se detecta humedad en el cielo raso del piso 2.', 'priority' => 'alta',   'status' => 'cerrada',    'daysAgo' => 60, 'attendedDays' => 58, 'closedDays' => 55],
            ['student' => $estudiantes[3], 'tecnico' => $tecnicos[0], 'category' => $catEquipo,  'resource' => $recursos[3], 'title' => 'Proyector sin imagen',                   'desc' => 'El proyector del aula 302 no proyecta aunque el equipo enciende.', 'priority' => 'media',  'status' => 'cerrada',    'daysAgo' => 50, 'attendedDays' => 48, 'closedDays' => 47],
            ['student' => $estudiantes[4], 'tecnico' => $tecnicos[1], 'category' => $catSoporte, 'resource' => $recursos[4], 'title' => 'Wi-Fi campus norte sin señal',           'desc' => 'Desde el edificio D no hay cobertura Wi-Fi.', 'priority' => 'alta',   'status' => 'cerrada',    'daysAgo' => 45, 'attendedDays' => 44, 'closedDays' => 43],
            ['student' => $estudiantes[0], 'tecnico' => $tecnicos[2], 'category' => $catMantto,  'resource' => $recursos[1], 'title' => 'Teclados dañados Lab-02',                'desc' => 'Al menos 8 teclados tienen teclas pegadas o rotas.', 'priority' => 'baja',   'status' => 'cerrada',    'daysAgo' => 40, 'attendedDays' => 38, 'closedDays' => 37],
            ['student' => $estudiantes[1], 'tecnico' => $tecnicos[0], 'category' => $catSoporte, 'resource' => $recursos[5], 'title' => 'Servidor caído Sistema Académico',       'desc' => 'El portal de calificaciones no responde desde las 08:00.', 'priority' => 'critica', 'status' => 'cerrada',    'daysAgo' => 35, 'attendedDays' => 35, 'closedDays' => 34],

            // ATENDIDAS (simulando 15-30 días atrás)
            ['student' => $estudiantes[2], 'tecnico' => $tecnicos[1], 'category' => $catInfra,   'resource' => $recursos[2], 'title' => 'Sillas rotas Aula Magna',                'desc' => 'Se contabilizan 15 sillas con estructura dañada.', 'priority' => 'baja',   'status' => 'atendida',   'daysAgo' => 28, 'attendedDays' => 25, 'closedDays' => null],
            ['student' => $estudiantes[3], 'tecnico' => $tecnicos[2], 'category' => $catSoporte, 'resource' => $recursos[0], 'title' => 'Software de diseño sin licencia',        'desc' => 'Adobe Creative Cloud expiró en 5 equipos del Lab-01.', 'priority' => 'alta',   'status' => 'atendida',   'daysAgo' => 22, 'attendedDays' => 20, 'closedDays' => null],
            ['student' => $estudiantes[4], 'tecnico' => $tecnicos[0], 'category' => $catEquipo,  'resource' => $recursos[3], 'title' => 'Cable HDMI proyector roto',              'desc' => 'El cable HDMI del proyector del aula 302 está cortado.', 'priority' => 'media',  'status' => 'atendida',   'daysAgo' => 20, 'attendedDays' => 18, 'closedDays' => null],
            ['student' => $estudiantes[0], 'tecnico' => $tecnicos[1], 'category' => $catMantto,  'resource' => $recursos[5], 'title' => 'UPS sala servidores sin batería',        'desc' => 'La UPS principal no tiene autonomía, se apaga al corte.', 'priority' => 'critica', 'status' => 'atendida',   'daysAgo' => 18, 'attendedDays' => 16, 'closedDays' => null],
            ['student' => $estudiantes[1], 'tecnico' => $tecnicos[2], 'category' => $catInfra,   'resource' => $recursos[2], 'title' => 'Iluminación deficiente Aula 201',        'desc' => 'Tres luminarias del aula 201 están fundidas.', 'priority' => 'baja',   'status' => 'atendida',   'daysAgo' => 15, 'attendedDays' => 14, 'closedDays' => null],

            // EN PROCESO (asignadas, 5-14 días atrás)
            ['student' => $estudiantes[2], 'tecnico' => $tecnicos[0], 'category' => $catSoporte, 'resource' => $recursos[1], 'title' => 'Actualización SO Lab-02',               'desc' => 'Solicitud de actualizar equipos Lab-02 a Windows 11.', 'priority' => 'media',  'status' => 'en_proceso', 'daysAgo' => 12, 'attendedDays' => null, 'closedDays' => null],
            ['student' => $estudiantes[3], 'tecnico' => $tecnicos[1], 'category' => $catMantto,  'resource' => $recursos[4], 'title' => 'Router Wi-Fi norte parpadeante',         'desc' => 'El router principal del campus norte reinicia cada hora.', 'priority' => 'alta',   'status' => 'en_proceso', 'daysAgo' => 10, 'attendedDays' => null, 'closedDays' => null],
            ['student' => $estudiantes[4], 'tecnico' => $tecnicos[2], 'category' => $catEquipo,  'resource' => null,         'title' => 'Solicitud escritorios ergonómicos',      'desc' => 'La sala de profesores requiere 10 escritorios nuevos.', 'priority' => 'baja',   'status' => 'en_proceso', 'daysAgo' => 8,  'attendedDays' => null, 'closedDays' => null],
            ['student' => $estudiantes[0], 'tecnico' => $tecnicos[0], 'category' => $catSoporte, 'resource' => $recursos[5], 'title' => 'Acceso VPN sin funcionamiento',          'desc' => 'Estudiantes de postgrado no pueden conectarse a VPN.', 'priority' => 'alta',   'status' => 'en_proceso', 'daysAgo' => 7,  'attendedDays' => null, 'closedDays' => null],

            // PENDIENTES (sin asignar, últimos 7 días)
            ['student' => $estudiantes[1], 'tecnico' => null, 'category' => $catInfra,   'resource' => $recursos[2], 'title' => 'Puerta de emergencia bloqueada',          'desc' => 'La salida de emergencia del piso 3 no abre correctamente.', 'priority' => 'critica', 'status' => 'pendiente',  'daysAgo' => 5, 'attendedDays' => null, 'closedDays' => null],
            ['student' => $estudiantes[2], 'tecnico' => null, 'category' => $catSoporte, 'resource' => $recursos[0], 'title' => 'Impresora Lab-01 sin tóner',              'desc' => 'La impresora HP del Lab-01 indica tóner agotado.', 'priority' => 'media',  'status' => 'pendiente',  'daysAgo' => 4, 'attendedDays' => null, 'closedDays' => null],
            ['student' => $estudiantes[3], 'tecnico' => null, 'category' => $catMantto,  'resource' => $recursos[1], 'title' => 'Cerradura Lab-02 dañada',                 'desc' => 'La cerradura electrónica del Lab-02 no responde al carnet.', 'priority' => 'alta',   'status' => 'pendiente',  'daysAgo' => 3, 'attendedDays' => null, 'closedDays' => null],
            ['student' => $estudiantes[4], 'tecnico' => null, 'category' => $catEquipo,  'resource' => null,         'title' => 'Solicitud de cámaras para aulas',         'desc' => 'Se requieren 5 cámaras de videoconferencia para aulas nuevas.', 'priority' => 'baja',   'status' => 'pendiente',  'daysAgo' => 2, 'attendedDays' => null, 'closedDays' => null],
            ['student' => $estudiantes[0], 'tecnico' => null, 'category' => $catSoporte, 'resource' => $recursos[4], 'title' => 'Wi-Fi sin cobertura en biblioteca',        'desc' => 'La planta alta de la biblioteca no recibe señal Wi-Fi.', 'priority' => 'alta',   'status' => 'pendiente',  'daysAgo' => 1, 'attendedDays' => null, 'closedDays' => null],
        ];

        $counter = 1;
        foreach ($solicitudes as $data) {
            $createdAt   = Carbon::now()->subDays($data['daysAgo']);
            $attendedAt  = $data['attendedDays'] !== null ? Carbon::now()->subDays($data['attendedDays']) : null;
            $closedAt    = $data['closedDays']   !== null ? Carbon::now()->subDays($data['closedDays'])   : null;
            $ticketCode  = 'REQ-2026-' . str_pad($counter, 4, '0', STR_PAD_LEFT);

            $sr = ServiceRequest::create([
                'ticket_code' => $ticketCode,
                'student_id'  => $data['student']->id,
                'assigned_to' => $data['tecnico'] ? $data['tecnico']->id : null,
                'category_id' => $data['category']->id,
                'resource_id' => $data['resource'] ? $data['resource']->id : null,
                'title'       => $data['title'],
                'description' => $data['desc'],
                'priority'    => $data['priority'],
                'status'      => $data['status'],
                'attended_at' => $attendedAt,
                'closed_at'   => $closedAt,
                'created_at'  => $createdAt,
                'updated_at'  => $createdAt,
            ]);

            // Generar historial de trazabilidad según el estado
            $actor = $data['tecnico'] ?? $admin;

            // Siempre: creación de solicitud
            RequestHistory::create([
                'request_id'      => $sr->id,
                'user_id'         => $data['student']->id,
                'action'          => 'Solicitud creada',
                'comment'         => 'Solicitud enviada por el estudiante.',
                'previous_status' => null,
                'new_status'      => 'pendiente',
                'created_at'      => $createdAt,
                'updated_at'      => $createdAt,
            ]);

            if (in_array($data['status'], ['en_proceso', 'atendida', 'cerrada'])) {
                $assignedAt = $createdAt->copy()->addHours(2);
                RequestHistory::create([
                    'request_id'      => $sr->id,
                    'user_id'         => $admin->id,
                    'action'          => 'Asignación de técnico',
                    'comment'         => "Técnico {$actor->name} asignado para atender la solicitud.",
                    'previous_status' => 'pendiente',
                    'new_status'      => 'en_proceso',
                    'created_at'      => $assignedAt,
                    'updated_at'      => $assignedAt,
                ]);
            }

            if (in_array($data['status'], ['atendida', 'cerrada']) && $attendedAt) {
                RequestHistory::create([
                    'request_id'      => $sr->id,
                    'user_id'         => $actor->id,
                    'action'          => 'Cambio de estado a Atendida',
                    'comment'         => 'La solicitud fue atendida satisfactoriamente.',
                    'previous_status' => 'en_proceso',
                    'new_status'      => 'atendida',
                    'created_at'      => $attendedAt,
                    'updated_at'      => $attendedAt,
                ]);
            }

            if ($data['status'] === 'cerrada' && $closedAt) {
                RequestHistory::create([
                    'request_id'      => $sr->id,
                    'user_id'         => $admin->id,
                    'action'          => 'Solicitud cerrada',
                    'comment'         => 'Solicitud cerrada y archivada por el administrador.',
                    'previous_status' => 'atendida',
                    'new_status'      => 'cerrada',
                    'created_at'      => $closedAt,
                    'updated_at'      => $closedAt,
                ]);
            }

            $counter++;
        }

        $this->command->info("✅ DatabaseSeeder completado: {$counter} solicitudes, 9 usuarios, 4 categorías, 6 recursos.");
    }
}
