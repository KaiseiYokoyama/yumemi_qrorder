<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    /**
     * モデルの属性のデフォルト値
     *
     * @var null[]
     */
    protected $attributes = [
        'image_url' => null
    ];

    protected $fillable = [
        'restaurant_id',
        'name',
        'price',
        'image_url',
    ];

    protected $table = 'menus';
}
