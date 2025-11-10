<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materiel extends Model
{
    protected $fillable = ['nom_materiel', 'disponible'];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'item_id')->where('item_type', self::class);
    }
}