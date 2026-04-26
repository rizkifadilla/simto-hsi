<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'employee_id',
        'full_name',
        'nik_ktp',
        'phone',
        'email',
        'position',
        'division',
        'placement',
        'join_date',
        'contract_start',
        'contract_end',
        'contract_extension_count',
        'status',
        'absent_using_distance',
        'notes',
        'face_descriptor'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
