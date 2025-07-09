<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\Loggable;

class Club extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'phone',
        'email',
        'website',
        'facebook',
        'instagram',
        'twitter',
        'founded_date',
        'status',
        'total_pilots',
        'achievements',
        'contact_persons'
    ];

    protected $casts = [
        'founded_date' => 'date',
        'contact_persons' => 'array',
        'achievements' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($club) {
            if (empty($club->slug)) {
                $club->slug = Str::slug($club->name);
            }
        });

        static::updating(function ($club) {
            if ($club->isDirty('name')) {
                $club->slug = Str::slug($club->name);
            }
        });
    }

    // Relaciones
    public function pilots()
    {
        return $this->hasMany(Pilot::class);
    }

    public function activePilots()
    {
        return $this->hasMany(Pilot::class)->where('status', 'active');
    }

    // Mutadores
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords(strtolower($value));
    }

    // Accesorios
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/logos/' . $this->logo) : asset('images/default-club-logo.png');
    }

    public function getFullAddressAttribute()
    {
        $address = [];
        if ($this->address) $address[] = $this->address;
        if ($this->city) $address[] = $this->city;
        if ($this->state) $address[] = $this->state;
        if ($this->country) $address[] = $this->country;
        
        return implode(', ', $address);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByLocation($query, $city = null, $state = null)
    {
        if ($city) {
            $query->where('city', 'like', "%{$city}%");
        }
        if ($state) {
            $query->where('state', 'like', "%{$state}%");
        }
        return $query;
    }

    // Métodos adicionales
    public function updatePilotCount()
    {
        $this->total_pilots = $this->pilots()->count();
        $this->save();
    }
}
