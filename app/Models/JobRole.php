<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobRole extends Model
{
    protected $fillable = [
        'name',
        'description',
        'daily_salary',
        'hourly_rate',
        'display_order',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function activeEmployees(): HasMany
    {
        return $this->employees()->where('is_active', true);
    }
}
