<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\Loggable;

class Championship extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'name',
        'year',
        'description',
        'start_date',
        'end_date',
        'total_matchdays',
        'status',
        'rules',
        'entry_fee',
        'prizes',
        'active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rules' => 'array',
        'entry_fee' => 'decimal:2',
        'active' => 'boolean',
        'total_matchdays' => 'integer',
        'year' => 'integer'
    ];

    /**
     * Relación con jornadas
     */
    public function matchdays()
    {
        return $this->hasMany(Matchday::class)->orderBy('number', 'asc');
    }

    /**
     * Relación con jornadas activas/programadas
     */
    public function scheduledMatchdays()
    {
        return $this->hasMany(Matchday::class)->whereIn('status', ['scheduled', 'in_progress']);
    }

    /**
     * Relación con jornadas completadas
     */
    public function completedMatchdays()
    {
        return $this->hasMany(Matchday::class)->where('status', 'completed');
    }

    /**
     * Relación con los registros de pilotos en este campeonato
     */
    public function registrations()
    {
        return $this->hasMany(ChampionshipRegistration::class);
    }

    /**
     * Relación con los registros activos de pilotos
     */
    public function activeRegistrations()
    {
        return $this->hasMany(ChampionshipRegistration::class)->where('status', 'active');
    }

    /**
     * Relación con los pilotos registrados (muchos a muchos a través de registrations)
     */
    public function registeredPilots()
    {
        return $this->belongsToMany(Pilot::class, 'championship_registrations')
                   ->withPivot('bib_number', 'status', 'registration_date', 'notes')
                   ->withTimestamps();
    }

    /**
     * Relación con los pilotos activamente registrados
     */
    public function activePilots()
    {
        return $this->belongsToMany(Pilot::class, 'championship_registrations')
                   ->wherePivot('status', 'active')
                   ->withPivot('bib_number', 'status', 'registration_date', 'notes')
                   ->withTimestamps();
    }

    /**
     * Scope para obtener campeonatos activos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para obtener campeonatos por año
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope para obtener campeonatos por estado
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Obtener el estado formateado
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'planned' => 'Planeado',
            'active' => 'Activo',
            'completed' => 'Completado',
            'cancelled' => 'Cancelado'
        ];
        
        return $statuses[$this->status] ?? 'Desconocido';
    }

    /**
     * Obtener el progreso del campeonato
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->total_matchdays == 0) {
            return 0;
        }
        
        $completed = $this->completedMatchdays()->count();
        return round(($completed / $this->total_matchdays) * 100, 1);
    }

    /**
     * Verificar si el campeonato está en curso
     */
    public function getIsActiveAttribute()
    {
        $now = Carbon::now()->toDateString();
        
        if ($this->start_date && $this->end_date) {
            return $now >= $this->start_date->toDateString() && 
                   $now <= $this->end_date->toDateString() &&
                   $this->status === 'active';
        }
        
        return $this->status === 'active';
    }

    /**
     * Obtener la próxima jornada
     */
    public function getNextMatchdayAttribute()
    {
        return $this->matchdays()
                   ->where('status', 'scheduled')
                   ->where('date', '>=', Carbon::now()->toDateString())
                   ->orderBy('date')
                   ->first();
    }

    /**
     * Actualizar el total de jornadas
     */
    public function updateTotalMatchdays()
    {
        $this->total_matchdays = $this->matchdays()->count();
        $this->save();
    }

    /**
     * Actualizar el estado del campeonato basado en las jornadas
     */
    public function updateStatus()
    {
        $totalMatchdays = $this->matchdays()->count();
        $completedMatchdays = $this->completedMatchdays()->count();
        
        if ($totalMatchdays == 0) {
            $this->status = 'planned';
        } elseif ($completedMatchdays == $totalMatchdays) {
            $this->status = 'completed';
        } elseif ($completedMatchdays > 0) {
            $this->status = 'active';
        } else {
            // Verificar si hay jornadas en progreso o programadas
            $hasActiveMatchdays = $this->matchdays()
                                      ->whereIn('status', ['scheduled', 'in_progress'])
                                      ->exists();
            
            $this->status = $hasActiveMatchdays ? 'active' : 'planned';
        }
        
        $this->save();
    }

    /**
     * Obtener años disponibles
     */
    public static function getAvailableYears()
    {
        return static::distinct()
                    ->orderBy('year', 'desc')
                    ->pluck('year')
                    ->toArray();
    }
}
