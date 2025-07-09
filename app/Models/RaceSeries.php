<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class RaceSeries extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'matchday_id',
        'category_id',
        'name',
        'series_number',
        'max_pilots',
        'transfer_to_final',
        'transfer_to_semifinal',
        'transfer_to_quarterfinal',
        'notes'
    ];

    protected $casts = [
        'series_number' => 'integer',
        'max_pilots' => 'integer',
        'transfer_to_final' => 'integer',
        'transfer_to_semifinal' => 'integer',
        'transfer_to_quarterfinal' => 'integer',
    ];

    /**
     * Relación con la jornada
     */
    public function matchday()
    {
        return $this->belongsTo(Matchday::class);
    }

    /**
     * Relación con la categoría
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relación con las mangas
     */
    public function heats()
    {
        return $this->hasMany(RaceHeat::class)->orderBy('heat_number');
    }

    /**
     * Relación con todos los pilotos de la serie (a través de las mangas)
     */
    public function pilots()
    {
        return $this->belongsToMany(Pilot::class, 'race_lineups', 'race_heat_id', 'pilot_id')
                    ->join('race_heats', 'race_lineups.race_heat_id', '=', 'race_heats.id')
                    ->where('race_heats.race_series_id', $this->id)
                    ->distinct();
    }

    /**
     * Obtener los pilotos únicos de esta serie
     */
    public function getUniquePilots()
    {
        $pilotIds = [];
        foreach ($this->heats as $heat) {
            foreach ($heat->lineups as $lineup) {
                $pilotIds[] = $lineup->pilot_id;
            }
        }
        
        return Pilot::whereIn('id', array_unique($pilotIds))
                   ->with('club')
                   ->orderBy('last_name')
                   ->get();
    }

    /**
     * Verificar si la serie está completa (tiene al menos una manga con pilotos)
     */
    public function isComplete()
    {
        return $this->heats()
                   ->whereHas('lineups')
                   ->exists();
    }

    /**
     * Obtener el total de transferencias configuradas
     */
    public function getTotalTransfers()
    {
        return $this->transfer_to_final + $this->transfer_to_semifinal + $this->transfer_to_quarterfinal;
    }

    /**
     * Obtener descripción detallada de las transferencias
     */
    public function getTransferDescription()
    {
        $transfers = [];
        
        if ($this->transfer_to_final > 0) {
            $transfers[] = "{$this->transfer_to_final} a la final";
        }
        
        if ($this->transfer_to_semifinal > 0) {
            $transfers[] = "{$this->transfer_to_semifinal} a semifinal";
        }
        
        if ($this->transfer_to_quarterfinal > 0) {
            $transfers[] = "{$this->transfer_to_quarterfinal} a cuartos";
        }
        
        if (empty($transfers)) {
            return "0 avanzan";
        }
        
        if (count($transfers) === 1) {
            return "avanzan " . $transfers[0];
        }
        
        $lastTransfer = array_pop($transfers);
        return "avanzan " . implode(', ', $transfers) . " y " . $lastTransfer;
    }

    /**
     * Scope para series de una jornada específica
     */
    public function scopeForMatchday($query, $matchdayId)
    {
        return $query->where('matchday_id', $matchdayId);
    }

    /**
     * Scope para series de una categoría específica
     */
    public function scopeForCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
