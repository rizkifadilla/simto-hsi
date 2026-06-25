<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Job extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'description',
        'requirement',
        'benefit',
        'location',
        'type',
        'salary_min',
        'salary_max',
        'deadline',
        'is_active'
    ];

    // AUTO SLUG
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($job) {
            $job->slug = Str::slug($job->title) . '-' . time();
        });
    }

    // RELATION
    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}