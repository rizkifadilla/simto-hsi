<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'contact_person',
        'phone',
        'latitude',
        'longitude',
        "check_in_time",
        "check_out_time",
        "attendance_radius"
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
