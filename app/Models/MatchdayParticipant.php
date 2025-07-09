<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class MatchdayParticipant extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'matchday_id',
        'pilot_id',
        'category_id',
        'registration_number',
        'entry_fee_paid',
        'status',
        'notes',
        'registered_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'entry_fee_paid' => 'decimal:2',
    ];

    /**
     * Relación con la jornada
     */
    public function matchday()
    {
        return $this->belongsTo(Matchday::class);
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
     * Relación con los pagos
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Obtener el pago más reciente
     */
    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latest();
    }

    /**
     * Verificar si tiene un pago aprobado
     */
    public function hasPaidEntry()
    {
        return $this->payments()->where('status', 'approved')->exists();
    }

    /**
     * Obtener el estado formateado
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'registered' => 'Inscrito',
            'confirmed' => 'Confirmado',
            'cancelled' => 'Cancelado',
            'no_show' => 'No se presentó'
        ];
        
        return $statuses[$this->status] ?? 'Desconocido';
    }

    /**
     * Obtener el color del badge según el estado
     */
    public function getStatusBadgeColorAttribute()
    {
        $colors = [
            'registered' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'no_show' => 'secondary'
        ];
        
        return $colors[$this->status] ?? 'light';
    }
    
    /**
     * Obtener nombre descriptivo para logging
     */
    public function getLogName()
    {
        $pilot = $this->pilot;
        $matchday = $this->matchday;
        
        if ($pilot && $matchday) {
            return "{$pilot->full_name} en {$matchday->name}";
        }
        
        return "Participante #{$this->id}";
    }
}
