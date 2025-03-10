<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'device_id', 'fingerprint', 'ip_address', 'location', 'is_active', 'last_activity'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_activity' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
