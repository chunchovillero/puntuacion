<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class Category extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'name',
        'type',
        'gender',
        'age_min',
        'age_max',
        'description',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'age_min' => 'integer',
        'age_max' => 'integer',
    ];

    /**
     * Atributos adicionales que se pueden asignar dinámicamente
     */
    protected $appends = [];
    
    /**
     * Atributos que se pueden asignar en masa dinámicamente
     */
    public $championship_counts = [];

    /**
     * Relación con pilotos
     */
    public function pilots()
    {
        return $this->hasMany(Pilot::class);
    }

    /**
     * Relación con registros de campeonatos
     */
    public function championshipRegistrations()
    {
        return $this->hasMany(\App\Models\ChampionshipRegistration::class);
    }

    /**
     * Relación con series de carreras
     */
    public function raceSeries()
    {
        return $this->hasMany(RaceSeries::class);
    }

    /**
     * Scope para obtener solo categorías activas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Verificar si una edad está dentro del rango de esta categoría
     */
    public function isAgeInRange($age)
    {
        if ($this->age_min && $age < $this->age_min) {
            return false;
        }
        
        if ($this->age_max && $age > $this->age_max) {
            return false;
        }
        
        return true;
    }

    /**
     * Obtener conteos de pilotos por campeonato
     */
    public function getChampionshipCounts($championshipIds = null)
    {
        $query = $this->championshipRegistrations();
        
        if ($championshipIds) {
            $query->whereIn('championship_id', $championshipIds);
        }
        
        return $query->selectRaw('championship_id, COUNT(*) as count')
            ->groupBy('championship_id')
            ->pluck('count', 'championship_id')
            ->toArray();
    }

    /**
     * Obtener categorías apropiadas para una edad y género específicos
     */
    public static function getForAgeAndGender($age, $gender = null)
    {
        $query = static::active();
        
        $query->where(function($q) use ($age) {
            $q->where(function($sq) use ($age) {
                $sq->whereNull('age_min')->orWhere('age_min', '<=', $age);
            })->where(function($sq) use ($age) {
                $sq->whereNull('age_max')->orWhere('age_max', '>=', $age);
            });
        });
        
        if ($gender) {
            $query->where(function($q) use ($gender) {
                $q->where('gender', $gender)->orWhereNull('gender');
            });
        }
        
        return $query->get();
    }

    /**
     * Obtener el nombre formateado con rango de edad
     */
    public function getFormattedNameAttribute()
    {
        $name = $this->name;
        
        if ($this->age_min && $this->age_max) {
            $name .= " ({$this->age_min}-{$this->age_max} años)";
        } elseif ($this->age_min) {
            $name .= " ({$this->age_min}+ años)";
        } elseif ($this->age_max) {
            $name .= " (hasta {$this->age_max} años)";
        }
        
        return $name;
    }
}
