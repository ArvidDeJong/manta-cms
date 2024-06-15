<?php

namespace Manta\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Productcat extends Model
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
        'productcat_id',
        'title',
        'title_2',
        'slug',
        'seo_title',
        'seo_description',
        'summary',
        'tags',
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

    // Definieer de relatie met child Productcats
    public function children()
    {
        return $this->hasMany(Productcat::class, 'pid');
    }

    // Boot method to define model events
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($productcat) {
            // Verwijder alle child productcats voordat de parent wordt verwijderd
            $productcat->children()->each(function ($child) {
                $child->delete();
            });
        });
    }

    public function product(): HasOne
    {
        return $this->hasOne(Productcatjoin::class)
            ->join('products', 'products.id', '=', 'productcatjoins.product_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Productcatjoin::class)
            ->join('products', 'products.id', '=', 'productcatjoins.product_id');
    }

    /** @return HasOne  */
    public function upload(): HasOne
    {
        return $this->hasOne(Upload::class, 'model_id')->where('model', get_class($this));
    }

    /** @return HasMany  */
    public function uploads(): HasMany
    {
        return $this->hasMany(Upload::class, 'model_id')->where('model', get_class($this));
    }

    /** @return HasOne  */
    public function image(): HasOne
    {
        return $this->hasOne(Upload::class, 'model_id')->where(['model' => get_class($this), 'image' => 1]);
    }

    /** @return HasMany  */
    public function images(): HasMany
    {
        return $this->hasMany(Upload::class, 'model_id')->where(['model' => get_class($this), 'image' => 1]);
    }

    public static function getItems($menu_id = null, $pid = null)
    {
        $menuItems = Productcat::where('menu_id', $menu_id)->where('pid', $pid)->orderBy('sort', 'ASC')->get();

        $menuList = [];

        foreach ($menuItems as $menuItem) {
            $node = [
                'name' => $menuItem->title,
                'id' => $menuItem->id,
            ];

            $children = self::getItems($menu_id, $menuItem->id);

            if (!empty($children)) {
                $node['children'] = $children;
            }

            $menuList[] = $node;
        }

        return $menuList;
    }

    public static function updateSortOrder($menuItems)
    {
        foreach ($menuItems as $index => $menuItem) {
            $menu = Productcat::find($menuItem['id']);
            if ($menu) {
                $menu->sort = $index + 1; // Adding 1 to index because sort starts from 1
                $menu->save();

                // Recursively update children
                if (!empty($menuItem['children'])) {
                    self::updateSortOrder($menuItem['children']);
                }
            }
        }
    }

    public static function updateParent($menuItems, $parentId = null)
    {
        foreach ($menuItems as $menuItem) {
            $menu = Productcat::find($menuItem['id']);
            if ($menu) {
                $menu->pid = $parentId;
                $menu->save();

                // Recursively update children
                if (!empty($menuItem['children'])) {
                    self::updateParent($menuItem['children'], $menu->id);
                }
            }
        }
    }

    public static function generateNestedMenu($menuItems)
    {
        $html = '<ul class="ml-4 list-disc">';
        foreach ($menuItems as $menuItem) {
            $html .= '<li>';
            $html .= '<span>' . $menuItem['name'] . '</span>';
            if (isset($menuItem['children']) && !empty($menuItem['children'])) {
                $html .= self::generateNestedMenu($menuItem['children']);
            }
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
}
