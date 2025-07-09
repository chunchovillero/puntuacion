<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class ChampionshipRegistration extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'championship_id',
        'pilot_id',
        'category_id',
        'bib_number',
        'status',
        'registration_date',
        'notes'
    ];

    protected $casts = [
        'registration_date' => 'date'
    ];

    /**
     * Relación con el campeonato
     */
    public function championship()
    {
        return $this->belongsTo(Championship::class);
    }

    /**
     * Relación con el piloto
     */
    public function pilot()
    {
        return $this->belongsTo(Pilot::class);
    }

    /**
     * Relación con la categoría
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope para registros activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para ordenar por dorsal
     */
    public function scopeOrderByBib($query)
    {
        return $query->orderBy('bib_number');
    }
    
    /**
     * Obtener nombre descriptivo para logging
     */
    public function getLogName()
    {
        $pilot = $this->pilot;
        $championship = $this->championship;
        
        if ($pilot && $championship) {
            return "{$pilot->full_name} en {$championship->name}";
        }
        
        return "Registro #{$this->id}";
    }
}
