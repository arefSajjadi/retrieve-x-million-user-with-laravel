<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    protected $table = "jobs";

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function providers(): HasMany
    {
        return $this->hasMany(Provider::class, 'job_id');
    }

    // Defining An Accessors
    public function getNameAttribute(): string
    {
        return 'Name is ' . $this->title;
    }

    public function getProviderListAttribute(): array
    {
        return $this->providers()->pluck('full_name')->toArray();
    }
}
