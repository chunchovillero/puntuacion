<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait Loggable
{
    /**
     * Boot the trait
     */
    protected static function bootLoggable()
    {
        static::created(function ($model) {
            ActivityLog::log('create', $model, null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $original = $model->getOriginal();
            $changes = $model->getChanges();
            
            // Solo loggear si hay cambios reales (excluir timestamps automáticos)
            unset($changes['updated_at']);
            if (!empty($changes)) {
                ActivityLog::log('update', $model, $original, $changes);
            }
        });

        static::deleted(function ($model) {
            ActivityLog::log('delete', $model, $model->getAttributes(), null);
        });
    }

    /**
     * Obtener nombre para logging (override en el modelo si es necesario)
     */
    public function getLogName()
    {
        if (isset($this->name)) {
            return $this->name;
        }

        if (isset($this->full_name)) {
            return $this->full_name;
        }

        if (isset($this->title)) {
            return $this->title;
        }

        return class_basename($this) . ' #' . $this->id;
    }

    /**
     * Obtener logs de actividad para este modelo
     */
    public function activityLogs()
    {
        return ActivityLog::where('model_type', get_class($this))
                         ->where('model_id', $this->id)
                         ->with('user')
                         ->orderBy('created_at', 'desc');
    }

    /**
     * Log manual de actividad
     */
    public function logActivity($action, $description = null, $oldValues = null, $newValues = null)
    {
        return ActivityLog::log($action, $this, $oldValues, $newValues, $description);
    }
}
