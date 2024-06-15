<?php

namespace Manta\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Menuitem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
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
        'menu_id',
        'title',
        'content',
        'route',
        'route_custom',
        'route_target',
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

    /** @return HasOne  */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public static function getItems($menu_id, $pid = null)
    {
        $menuItems = MenuItem::where('menu_id', $menu_id)->where('pid', $pid)->orderBy('sort', 'ASC')->get();

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
            $menu = MenuItem::find($menuItem['id']);
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
            $menu = MenuItem::find($menuItem['id']);
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
}
