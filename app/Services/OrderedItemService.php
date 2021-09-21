<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\OrderedItem;
use App\Models\Party;

class OrderedItemService
{
    public function getOrderedByPartyId(
        int $partyId
    )
    {
        return OrderedItem::query()
            ->where('party_id', $partyId)
            ->get();
    }

    /**
     * 新たな注文を記録する
     *
     * @param Party $party
     * @param array $menuIds
     */
    public function addNewOrder(
        Party $party,
        array $menuIds
    )
    {
        $orderedMenus = Menu::query()
            ->whereIn('id', $menuIds)
            ->get();

        if (count($orderedMenus) !== count($menuIds)) {
            // 注文に存在しないメニューが混ざっていた時
            throw new NotFoundException();
        }

        foreach ($orderedMenus->pluck('restaurant_id') as $restaurant_id) {
            // よその店舗のメニューを注文しようとした時
            if ($party->restaurant_id !== $restaurant_id) {
                throw new ForbiddenException();
            }
        }

        $orderedMenuRecords = [];

        foreach ($menuIds as $menuId) {
            array_push(
                $orderedMenuRecords,
                OrderedItem::create([
                    'party_id' => $party->id,
                    'menu_id' => $menuId,
                ])
            );
        }

        return $orderedMenuRecords;
    }
}
