<?php

namespace Manta\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class News extends Model
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
        'company_id',
        'host',
        'pid',
        'locale',
        'author',
        'title',
        'title_2',
        'title_3',
        'slug',
        'seo_title',
        'seo_description',
        'tags',
        'summary',
        'excerpt',
        'content',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    public array $upload_cats = ['datasheet' => 'News datasheet'];

    public function category(): HasOne
    {
        return $this->hasOne(Newscatjoin::class)
            ->join('newscats', 'newscats.id', '=', 'newscatjoins.newscat_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Newscatjoin::class)
            ->join('newscats', 'newscats.id', '=', 'newscatjoins.newscat_id');
    }

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

    public function getCategoriesList()
    {
        $return = [];

        foreach ($this->categories as $value) {
            $return[] = $value->title;
        }

        return $return;
    }
}
