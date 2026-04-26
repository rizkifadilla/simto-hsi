<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'cv_file'
    ];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}