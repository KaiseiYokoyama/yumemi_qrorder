<?php

namespace App\Services;

use App\Models\Menu;

class MenuService
{
    public static function addNewMenu(
        int    $restaurantId,
        string $name,
        int    $price,
        string $imageUrl
    )
    {
        return Menu::create([
            'restaurant_id' => $restaurantId,
            'name' => $name,
            'price' => $price,
            'image_url' => $imageUrl,
        ]);
    }
}
