<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NetworkConnection extends Model
{
    use HasFactory;

    protected $table = 'network_connections';

    protected $fillable = [
        'source',
        'destination',
        'port',
        'status',
        'description'
    ];

    public function getStatusAttribute($value): string
    {
        return $value ? 'ACCESSIBLE' : 'NOT ACCESSIBLE';
    }
}
