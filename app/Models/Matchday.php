<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Matchday extends Model
{
    use HasFactory;

    protected $fillable = [
        'championship_id',
        'number',
        'name',
        'date',
        'start_time',
        'end_time',
        'venue',
        'address',
        'organizer_club_id',
        'organizer_name',
        'organizer_contact',
        'organizer_phone',
        'description',
        'categories',
        'entry_fee',
        'requirements',
        'status',
        'results'
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'categories' => 'array',
        'entry_fee' => 'decimal:2',
        'number' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($matchday) {
            $matchday->championship->updateTotalMatchdays();
        });

        static::deleted(function ($matchday) {
            $matchday->championship->updateTotalMatchdays();
        });
        
        static::updated(function ($matchday) {
            if ($matchday->isDirty('status')) {
                $matchday->championship->updateStatus();
            }
        });
    }

    /**
     * Relación con el campeonato
     */
    public function championship()
    {
        return $this->belongsTo(Championship::class);
    }

    /**
     * Relación con el club organizador (opcional)
     */
    public function organizerClub()
    {
        return $this->belongsTo(Club::class, 'organizer_club_id');
    }

    /**
     * Scope para jornadas programadas
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope para jornadas completadas
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope para jornadas por fecha
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope para jornadas futuras
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', Carbon::now()->toDateString())
                    ->where('status', 'scheduled');
    }

    /**
     * Obtener el nombre del organizador
     */
    public function getOrganizerNameAttribute()
    {
        if ($this->organizerClub) {
            return $this->organizerClub->name;
        }
        
        return $this->attributes['organizer_name'] ?? 'AMBMX';
    }

    /**
     * Obtener el nombre completo de la jornada
     */
    public function getFullNameAttribute()
    {
        if ($this->name) {
            return $this->name;
        }
        
        return "Jornada {$this->number}";
    }

    /**
     * Verificar si la jornada es hoy
     */
    public function getIsTodayAttribute()
    {
        return $this->date->isToday();
    }

    /**
     * Verificar si la jornada es futura
     */
    public function getIsUpcomingAttribute()
    {
        return $this->date->isFuture();
    }

    /**
     * Verificar si la jornada es pasada
     */
    public function getIsPastAttribute()
    {
        return $this->date->isPast();
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
            'cancelled' => 'Cancelada',
            'postponed' => 'Pospuesta'
        ];
        
        return $statuses[$this->status] ?? 'Desconocido';
    }

    /**
     * Obtener el color del badge según el estado
     */
    public function getStatusBadgeColorAttribute()
    {
        $colors = [
            'scheduled' => 'primary',
            'in_progress' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
            'postponed' => 'secondary'
        ];
        
        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Obtener la fecha formateada para mostrar
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->format('d/m/Y');
    }

    /**
     * Obtener la hora de inicio formateada
     */
    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time ? Carbon::parse($this->start_time)->format('H:i') : null;
    }

    /**
     * Obtener la hora de fin formateada
     */
    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time ? Carbon::parse($this->end_time)->format('H:i') : null;
    }

    /**
     * Verificar si se puede editar la jornada
     */
    public function getCanEditAttribute()
    {
        return !in_array($this->status, ['completed', 'in_progress']);
    }

    /**
     * Verificar si se puede eliminar la jornada
     */
    public function getCanDeleteAttribute()
    {
        return $this->status === 'scheduled' && $this->is_upcoming;
    }
}
