<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class Payment extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'matchday_participant_id',
        'transaction_id',
        'order_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'authorization_code',
        'response_code',
        'webpay_response',
        'paid_at',
        'payer_email',
        'payer_name',
        'notes'
    ];

    protected $casts = [
        'webpay_response' => 'array',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    /**
     * Relación con el participante de jornada
     */
    public function matchdayParticipant()
    {
        return $this->belongsTo(MatchdayParticipant::class);
    }

    /**
     * Verificar si el pago está aprobado
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Verificar si el pago está pendiente
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Marcar pago como aprobado
     */
    public function markAsApproved($authorizationCode = null, $responseCode = null)
    {
        $this->update([
            'status' => 'approved',
            'authorization_code' => $authorizationCode,
            'response_code' => $responseCode,
            'paid_at' => now()
        ]);
    }

    /**
     * Marcar pago como rechazado
     */
    public function markAsRejected($responseCode = null)
    {
        $this->update([
            'status' => 'rejected',
            'response_code' => $responseCode
        ]);
    }

    /**
     * Generar ID de orden único
     */
    public static function generateOrderId()
    {
        return 'BMX' . date('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Obtener nombre descriptivo para logging
     */
    public function getLogName()
    {
        return "Pago #{$this->order_id} ($" . number_format($this->amount, 0, ',', '.') . ")";
    }
}
