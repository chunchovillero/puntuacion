<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class RaceLineup extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'race_heat_id',
        'pilot_id',
        'gate_position',
        'finish_position',
        'lap_time',
        'dnf',
        'dsq',
        'notes'
    ];

    protected $casts = [
        'gate_position' => 'integer',
        'finish_position' => 'integer',
        'lap_time' => 'decimal:3',
        'dnf' => 'boolean',
        'dsq' => 'boolean',
    ];

    /**
     * Relación con la manga
     */
    public function heat()
    {
        return $this->belongsTo(RaceHeat::class, 'race_heat_id');
    }

    /**
     * Relación con el piloto
     */
    public function pilot()
    {
        return $this->belongsTo(Pilot::class);
    }

    /**
     * Obtener el resultado formateado
     */
    public function getResultAttribute()
    {
        if ($this->dsq) {
            return 'DSQ';
        }
        
        if ($this->dnf) {
            return 'DNF';
        }
        
        if ($this->finish_position) {
            return $this->finish_position . '°';
        }
        
        return '-';
    }

    /**
     * Obtener el tiempo formateado
     */
    public function getFormattedTimeAttribute()
    {
        if (!$this->lap_time) {
            return '-';
        }
        
        $minutes = floor($this->lap_time / 60);
        $seconds = $this->lap_time % 60;
        
        if ($minutes > 0) {
            return sprintf('%d:%06.3f', $minutes, $seconds);
        }
        
        return sprintf('%.3f', $seconds);
    }

    /**
     * Verificar si el piloto terminó la carrera
     */
    public function didFinish()
    {
        return !$this->dnf && !$this->dsq && $this->finish_position !== null;
    }

    /**
     * Obtener el estado del resultado
     */
    public function getResultStatusAttribute()
    {
        if ($this->dsq) {
            return 'disqualified';
        }
        
        if ($this->dnf) {
            return 'did_not_finish';
        }
        
        if ($this->finish_position) {
            return 'finished';
        }
        
        return 'pending';
    }

    /**
     * Obtener el color del badge según el resultado
     */
    public function getResultBadgeColorAttribute()
    {
        $colors = [
            'finished' => 'success',
            'did_not_finish' => 'warning',
            'disqualified' => 'danger',
            'pending' => 'secondary'
        ];
        
        return $colors[$this->result_status] ?? 'secondary';
    }

    /**
     * Scope para posiciones en una manga específica
     */
    public function scopeForHeat($query, $heatId)
    {
        return $query->where('race_heat_id', $heatId);
    }

    /**
     * Scope para una posición de partidor específica
     */
    public function scopeAtGate($query, $gatePosition)
    {
        return $query->where('gate_position', $gatePosition);
    }

    /**
     * Scope para pilotos que terminaron la carrera
     */
    public function scopeFinished($query)
    {
        return $query->where('dnf', false)
                    ->where('dsq', false)
                    ->whereNotNull('finish_position');
    }

    /**
     * Scope ordenado por posición de llegada
     */
    public function scopeOrderByFinish($query)
    {
        return $query->orderByRaw('CASE 
                                  WHEN dsq = 1 THEN 999 
                                  WHEN dnf = 1 THEN 998 
                                  WHEN finish_position IS NULL THEN 997 
                                  ELSE finish_position 
                                END');
    }
}
