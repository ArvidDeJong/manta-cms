<?php

namespace Manta\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Hash;

class Newscatjoin extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'created_by',
        'updated_by',
        'deleted_by',
        'news_id',
        'newscat_id',
    ];

    public function category(): HasOne
    {
        return $this->hasOne(Newscatjoin::class, 'newscat_id')
            ->join('newscats', 'newscats.id', '=', 'newscatjoins.newscat_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Newscatjoin::class)
            ->join('newscats', 'newscats.id', '=', 'newscatjoins.newscat_id');
    }

    public function news(): HasOne
    {
        return $this->hasOne(Newscatjoin::class, 'id', 'news_id')
            ->join('newss', 'newss.id', '=', 'newscatjoins.news_id');
    }

    public function newss(): HasMany
    {
        return $this->hasMany(Newscatjoin::class)
            ->join('newss', 'newss.id', '=', 'newscatjoins.news_id');
    }
}
