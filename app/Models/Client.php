<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
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
