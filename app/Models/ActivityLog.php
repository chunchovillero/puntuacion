<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'model_name',
        'old_values',
        'new_values',
        'description',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Relación con el usuario que realizó la acción
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener el modelo relacionado
     */
    public function model()
    {
        if ($this->model_type && $this->model_id) {
            return $this->model_type::find($this->model_id);
        }
        return null;
    }

    /**
     * Crear un log de actividad
     */
    public static function log($action, $model, $oldValues = null, $newValues = null, $description = null)
    {
        $user = auth()->user();
        $request = request();

        return self::create([
            'user_id' => $user ? $user->id : null,
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id ?? null,
            'model_name' => self::getModelName($model),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $description ?? self::generateDescription($action, $model),
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
        ]);
    }

    /**
     * Obtener nombre legible del modelo
     */
    private static function getModelName($model)
    {
        if (method_exists($model, 'getLogName')) {
            return $model->getLogName();
        }

        if (isset($model->name)) {
            return $model->name;
        }

        if (isset($model->full_name)) {
            return $model->full_name;
        }

        if (isset($model->title)) {
            return $model->title;
        }

        return class_basename($model) . ' #' . ($model->id ?? 'nuevo');
    }

    /**
     * Generar descripción automática
     */
    private static function generateDescription($action, $model)
    {
        $modelName = self::getModelName($model);
        $className = class_basename($model);

        switch ($action) {
            case 'create':
                return "Creó {$className}: {$modelName}";
            case 'update':
                return "Editó {$className}: {$modelName}";
            case 'delete':
                return "Eliminó {$className}: {$modelName}";
            default:
                return "Realizó acción '{$action}' en {$className}: {$modelName}";
        }
    }

    /**
     * Scope para filtrar por tipo de modelo
     */
    public function scopeForModel($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Scope para filtrar por usuario
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para filtrar por acción
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }
}
