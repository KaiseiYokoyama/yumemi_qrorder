<?php

namespace App\Services;

use App\Models\Menu;

class MenuService
{

    public function addNewMenu(
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

    public function getMenu(
        int $menuId
    )
    {
        return Menu::query()->find($menuId);
    }

    public function deleteMenu(
        int $restaurantId,
        int $menuId
    )
    {
        $targetMenu = $this->getMenu($menuId);

        // 指定されたidを持つメニューがない時
        if (is_null($targetMenu)) {
            throw new NotFoundException();
        }

        if ($targetMenu->restaurant_id === $restaurantId) {
            // メニューを削除
            $targetMenu->delete();
            // 削除したメニューのレコードを返す
            return $targetMenu;
        } else {
            // 他店のメニューを削除しようとしていた時
            throw new ForbiddenException();
        }
    }

    public function getMenuByRestaurantId(
        int $restaurant_id
    ) {
        return Menu::query()
            ->where('restaurant_id', $restaurant_id)
            ->get();
    }
}
