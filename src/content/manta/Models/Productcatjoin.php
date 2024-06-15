<?php

namespace Manta\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Hash;

class Productcatjoin extends Model
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
        'product_id',
        'productcat_id',
    ];

    public function category(): HasOne
    {
        return $this->hasOne(Productcatjoin::class, 'productcat_id')
            ->join('productcats', 'productcats.id', '=', 'productcatjoins.productcat_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Productcatjoin::class)
            ->join('productcats', 'productcats.id', '=', 'productcatjoins.productcat_id');
    }


    public function products(): HasMany
    {
        return $this->hasMany(Productcatjoin::class)
            ->join('products', 'products.id', '=', 'productcatjoins.product_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
