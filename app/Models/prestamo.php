<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class prestamo extends Model
{
    use HasFactory;

    protected $filleable = [
        'user_id',
        'libro_id',
        'fecha_prestamo',
        'fecha_devolucion'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function libro()
    {
        return $this->belongsTo(libro::class);
    }
}
