<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ticket_code')->unique(); // e.g. REQ-2026-0001

            // Solicitante (estudiante)
            $table->foreignId('student_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Técnico asignado (nullable)
            $table->foreignId('assigned_to')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            // Categoría de la solicitud
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->onDelete('cascade');

            // Recurso afectado (nullable)
            $table->foreignId('resource_id')
                  ->nullable()
                  ->constrained('resources')
                  ->onDelete('set null');

            $table->string('title');
            $table->text('description');

            // 'baja', 'media', 'alta', 'critica'
            $table->string('priority')->default('media');

            // 'pendiente', 'en_proceso', 'atendida', 'cerrada'
            $table->string('status')->default('pendiente');

            $table->timestamp('attended_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
