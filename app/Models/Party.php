<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    use HasFactory;

    /**
     * モデルの属性のデフォルト値
     * @var int[]
     */
    protected $attributes = [
        'state' => 0,
    ];
}
