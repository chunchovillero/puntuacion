<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pilot extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'category_id',
        'first_name',
        'last_name',
        'nickname',
        'description',
        'birth_date',
        'age',
        'gender',
        'phone',
        'email',
        'emergency_contact_name',
        'emergency_contact_phone',
        'photo_path',
        'bike_brand',
        'bike_model',
        'bike_year',
        'specialties',
        'achievements',
        'joined_club_date',
        'status',
        'weight',
        'height',
        'blood_type',
        'medical_conditions',
        'insurance_provider',
        'insurance_number',
        'social_media',
        'ranking_points'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'joined_club_date' => 'date',
        'specialties' => 'array',
        'achievements' => 'array',
        'social_media' => 'array',
        'weight' => 'decimal:2',
        'height' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($pilot) {
            if ($pilot->birth_date && empty($pilot->age)) {
                $pilot->age = Carbon::parse($pilot->birth_date)->age;
            }
            if (empty($pilot->joined_club_date)) {
                $pilot->joined_club_date = now();
            }
        });

        static::updating(function ($pilot) {
            if ($pilot->isDirty('birth_date')) {
                $pilot->age = Carbon::parse($pilot->birth_date)->age;
            }
        });

        static::created(function ($pilot) {
            $pilot->club->updatePilotCount();
        });

        static::deleted(function ($pilot) {
            $pilot->club->updatePilotCount();
        });
    }

    // Relaciones
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Mutadores
    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = ucwords(strtolower($value));
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = ucwords(strtolower($value));
    }

    // Accesorios
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getDisplayNameAttribute()
    {
        return $this->nickname ? $this->nickname : $this->full_name;
    }

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/pilots/' . $this->photo) : asset('images/default-pilot.png');
    }

    public function getAgeFromBirthDateAttribute()
    {
        return $this->birth_date ? Carbon::parse($this->birth_date)->age : null;
    }

    public function getBikeInfoAttribute()
    {
        $bike = [];
        if ($this->bike_brand) $bike[] = $this->bike_brand;
        if ($this->bike_model) $bike[] = $this->bike_model;
        if ($this->bike_year) $bike[] = $this->bike_year;
        
        return implode(' ', $bike);
    }

    public function getExperienceLevelTextAttribute()
    {
        $levels = [
            'beginner' => 'Principiante',
            'intermediate' => 'Intermedio',
            'advanced' => 'Avanzado',
            'expert' => 'Experto',
            'professional' => 'Profesional'
        ];
        
        return $levels[$this->experience_level] ?? 'No definido';
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'injured' => 'Lesionado',
            'suspended' => 'Suspendido'
        ];
        
        return $statuses[$this->status] ?? 'No definido';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeBySpecialty($query, $specialty)
    {
        return $query->whereJsonContains('specialties', $specialty);
    }

    public function scopeByClub($query, $clubId)
    {
        return $query->where('club_id', $clubId);
    }

    public function scopeByAge($query, $minAge = null, $maxAge = null)
    {
        if ($minAge) {
            $query->where('age', '>=', $minAge);
        }
        if ($maxAge) {
            $query->where('age', '<=', $maxAge);
        }
        return $query;
    }

    // Métodos adicionales
    public function addAchievement($achievement)
    {
        $achievements = $this->achievements ?? [];
        $achievements[] = [
            'title' => $achievement,
            'date' => now()->toDateString()
        ];
        $this->achievements = $achievements;
        $this->save();
    }

    public function addRankingPoints($points)
    {
        $this->ranking_points += $points;
        $this->save();
    }

    // Métodos para categorías
    public function getSuggestedCategoriesAttribute()
    {
        if (!$this->age) return collect();
        
        return Category::getForAgeAndGender($this->age, $this->gender);
    }

    public function canBeInCategory($categoryId)
    {
        $category = Category::find($categoryId);
        if (!$category) return false;
        
        // Verificar edad
        if (!$category->isAgeInRange($this->age)) {
            return false;
        }
        
        // Verificar género (si la categoría tiene restricción de género)
        if ($category->gender && $category->gender !== $this->gender) {
            return false;
        }
        
        return true;
    }

    public function assignAppropriateCategory()
    {
        $suggestedCategories = $this->suggested_categories;
        
        if ($suggestedCategories->isNotEmpty()) {
            // Preferir categorías específicas por género sobre categorías mixtas
            $preferredCategory = $suggestedCategories
                ->where('gender', $this->gender)
                ->first();
                
            if (!$preferredCategory) {
                $preferredCategory = $suggestedCategories->first();
            }
            
            $this->category_id = $preferredCategory->id;
            $this->save();
            
            return $preferredCategory;
        }
        
        return null;
    }
}
