<?php

namespace Manta\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Option extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'created_by',
        'updated_by',
        'deleted_by',
        'company_id',
        'host',
        'locale',
        'pid',
        'model',
        'key',
        'value',
    ];

    private static $userId = null;

    public static function set(string $key, $value, ?string $model = null): void
    {
        // Initialize once and reuse if the method is called multiple times in a request
        if (self::$userId === null) {
            if (!Auth::guard('staff')->check()) {
                return; // Exit early if no staff user is authenticated
            }

            self::$userId = Auth::guard('staff')->id();
        }

        self::updateOrCreate(
            ['key' => $key, 'model' => $model],
            ['updated_by' => self::$userId, 'value' => $value]
        );
    }

    public static function get(string $key, ?string $model = null)
    {
        static $defaults = [
            'DEFAULT_LATITUDE' => 'DEFAULT_LATITUDE',
            'DEFAULT_LONGITUDE' => 'DEFAULT_LONGITUDE',
            'GOOGLE_MAPS_ZOOM' => 'GOOGLE_MAPS_ZOOM',
        ];

        $item = self::where(['key' => $key, 'model' => $model])->first();

        if (!$item && isset($defaults[$key])) {
            $value = env($defaults[$key]);
            self::set($key, $value, $model);
            return $value;
        }

        return $item->value ?? null;
    }
}
