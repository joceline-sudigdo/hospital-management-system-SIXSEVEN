<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('appointments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('patient_id')->constrained()->onDelete('cascade');
        $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
        $table->foreignId('schedule_id')->constrained()->onDelete('restrict');
        $table->date('appointment_date');
        $table->enum('status', ['pending','confirmed','cancelled','completed'])->default('pending');
        $table->text('complaint')->nullable();
        $table->timestamps();
    });
}
};
