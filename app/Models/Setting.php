<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class Setting extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'group',
        'options'
    ];

    protected $casts = [
        'options' => 'array',
        'value' => 'string'
    ];

    /**
     * Obtener un valor de configuración por clave
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->getValue() : $default;
    }

    /**
     * Establecer un valor de configuración
     */
    public static function set($key, $value, $type = 'text', $description = null, $group = 'general')
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'description' => $description,
                'group' => $group
            ]
        );
    }

    /**
     * Obtener el valor según el tipo
     */
    public function getValue()
    {
        switch ($this->type) {
            case 'boolean':
                return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $this->value;
            case 'float':
                return (float) $this->value;
            case 'json':
                return json_decode($this->value, true);
            default:
                return $this->value;
        }
    }

    /**
     * Obtener valor por clave
     */
    public static function getByKey($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->getValue() : $default;
    }

    /**
     * Configuraciones por grupos
     */
    public static function getByGroup($group)
    {
        return static::where('group', $group)->orderBy('key')->get();
    }

    /**
     * Todas las configuraciones agrupadas
     */
    public static function getAllGrouped()
    {
        return static::all()->groupBy('group');
    }
}
