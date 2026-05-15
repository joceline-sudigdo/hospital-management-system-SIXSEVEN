<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('medical_records', function (Blueprint $table) {
        $table->id();
        $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
        $table->foreignId('doctor_id')->constrained()->onDelete('restrict');
        $table->text('diagnosis');
        $table->text('prescription')->nullable();
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}
};
