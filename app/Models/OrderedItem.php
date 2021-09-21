<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderedItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'party_id',
    ];

    protected $table = 'ordered_items';
}
