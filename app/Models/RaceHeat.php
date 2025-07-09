<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class RaceHeat extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'race_series_id',
        'heat_number',
        'name',
        'scheduled_time',
        'status',
        'notes'
    ];

    protected $casts = [
        'heat_number' => 'integer',
        'scheduled_time' => 'datetime',
    ];

    /**
     * Relación con la serie
     */
    public function series()
    {
        return $this->belongsTo(RaceSeries::class, 'race_series_id');
    }

    /**
     * Relación con las posiciones/lineup
     */
    public function lineups()
    {
        return $this->hasMany(RaceLineup::class)->orderBy('gate_position');
    }

    /**
     * Relación con los pilotos (a través de lineups)
     */
    public function pilots()
    {
        return $this->belongsToMany(Pilot::class, 'race_lineups')
                    ->withPivot(['gate_position', 'finish_position', 'lap_time', 'dnf', 'dsq', 'notes'])
                    ->orderByPivot('gate_position');
    }

    /**
     * Obtener el estado formateado
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'scheduled' => 'Programada',
            'in_progress' => 'En Progreso',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada'
        ];
        
        return $statuses[$this->status] ?? 'Desconocido';
    }

    /**
     * Obtener el color del badge según el estado
     */
    public function getStatusBadgeColorAttribute()
    {
        $colors = [
            'scheduled' => 'secondary',
            'in_progress' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger'
        ];
        
        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Verificar si la manga está completa (todos los pilotos tienen posición de llegada)
     */
    public function isComplete()
    {
        $totalPilots = $this->lineups()->count();
        $finishedPilots = $this->lineups()
                              ->whereNotNull('finish_position')
                              ->orWhere('dnf', true)
                              ->orWhere('dsq', true)
                              ->count();
        
        return $totalPilots > 0 && $totalPilots === $finishedPilots;
    }

    /**
     * Obtener la próxima posición de partidor disponible
     */
    public function getNextGatePosition()
    {
        $usedPositions = $this->lineups()->pluck('gate_position')->toArray();
        
        for ($i = 1; $i <= 8; $i++) {
            if (!in_array($i, $usedPositions)) {
                return $i;
            }
        }
        
        return null; // Serie llena
    }

    /**
     * Verificar si hay espacio para más pilotos
     */
    public function hasSpace()
    {
        return $this->lineups()->count() < 8;
    }

    /**
     * Scope para mangas de una serie específica
     */
    public function scopeForSeries($query, $seriesId)
    {
        return $query->where('race_series_id', $seriesId);
    }

    /**
     * Scope para mangas completadas
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
