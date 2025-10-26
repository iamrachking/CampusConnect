<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Projet extends Model
{
    protected $fillable = ['titre', 'description', 'encadrant'];

    public function encadrant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encadrant');
    }

    public function equipes(): HasMany
    {
        return $this->hasMany(Equipe::class);
    }

    public function livrables(): HasMany
    {
        return $this->hasMany(Livrable::class);
    }
}
