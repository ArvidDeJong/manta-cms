<?php

namespace Manta\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Routeseo extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'created_by',
        'updated_by',
        'deleted_by',
        'pid',
        'locale',
        'title',
        'route',
        'seo_title',
        'seo_description',
    ];

    /** @return HasOne  */
    public function upload(): HasOne
    {
        return $this->hasOne(Upload::class, 'model_id')->where('model', get_class($this))->orderBy('sort', 'ASC');
    }

    /** @return HasMany  */
    public function uploads(): HasMany
    {
        return $this->hasMany(Upload::class, 'model_id')->where('model', get_class($this))->orderBy('sort', 'ASC');
    }

    /** @return HasOne  */
    public function document(): HasOne
    {
        return $this->hasOne(Upload::class, 'model_id')->where(['model' => get_class($this), 'image' => 0])->orderBy('sort', 'ASC');
    }

    /** @return HasMany  */
    public function documents(): HasMany
    {
        return $this->hasMany(Upload::class, 'model_id')->where(['model' => get_class($this), 'image' => 0])->orderBy('sort', 'ASC');
    }

    /** @return HasOne  */
    public function image(): HasOne
    {
        return $this->hasOne(Upload::class, 'model_id')->where(['model' => get_class($this), 'image' => 1])->orderBy('sort', 'ASC');
    }

    /** @return HasMany  */
    public function images(): HasMany
    {
        return $this->hasMany(Upload::class, 'model_id')->where(['model' => get_class($this), 'image' => 1])->orderBy('sort', 'ASC');
    }
}
