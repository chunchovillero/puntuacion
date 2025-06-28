<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

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
     * Relación con pilotos
     */
    public function pilots()
    {
        return $this->hasMany(Pilot::class);
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
