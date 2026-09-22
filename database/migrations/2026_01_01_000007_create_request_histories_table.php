<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('request_id')
                  ->constrained('service_requests')
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->string('action'); // "Cambio de estado", "Asignación de técnico", "Comentario"
            $table->text('comment')->nullable();
            $table->string('previous_status')->nullable();
            $table->string('new_status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_histories');
    }
};
