<?php

namespace Manta\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;

class House extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'created_by',
        'updated_by',
        'deleted_by',
        'company_id',
        'host',
        'pid',
        'locale',
        'active',
        'sort',
        'administration',
        'identifier',
        'reference_nr',
        'author',
        'show_from',
        'show_till',
        'status',
        'currency',
        'company',
        'title',
        'title_2',
        'title_3',
        'slug',
        'sex',
        'firstname',
        'lastname',
        'email',
        'phone',
        'address',
        'address_nr',
        'zipcode',
        'city',
        'province',
        'country',
        'seo_title',
        'seo_description',
        'excerpt',
        'content',
        'summary',
        'object_kind',
        'date_available',
        'type',       // sale - rent
        'sale_price',
        'rent_price',
        'mortgage_price',
        'surface',
        'living_area',
        'rooms',
        'bedrooms',
        'bathrooms',
        'latitude',
        'longitude',
        'url_1',
        'url_2',
        'pros',
        'cons',
        'neerby',
        'tags',
        'images_array',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'pros' => 'array',
        'cons' => 'array',
        'neerby' => 'array',
        'tags' => 'array',
        'show_from' => 'date',
        'show_till' => 'date',
        // 'date_available' => 'date',
        'images_array' => 'array',
    ];

    /**
     * Dates to be treated as Carbon instances.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at', // This is needed for SoftDeletes
        'show_from',
        'show_till',
        // 'date_available',
    ];

    // public static function config()
    // {
    //     $path = app_path('Livewire/Manta/House/Houseconfig.json');

    //     if (!File::exists($path)) {
    //         $path = app_path('Livewire/Manta/House/Houseconfig_default.json');
    //         // throw new \Exception("Configuration file not found: $path");
    //     }

    //     $json = File::get($path);
    //     return json_decode($json, true);
    // }

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
