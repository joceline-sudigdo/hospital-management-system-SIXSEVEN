<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'specialization', 'phone', 'photo',
    ];

    // Relasi ke User (belongsTo = dokter punya 1 user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Schedule
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    // Relasi ke Appointment
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}