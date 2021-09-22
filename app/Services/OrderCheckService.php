<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\OrderedItem;
use App\Models\Party;
use App\Models\PartyState;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;

class OrderCheckService
{
    public function getOrderedByRestaurantId(
        int $restaurantId
    )
    {
        $partyIds = Party::query()
            ->where('restaurant_id', $restaurantId)
            ->where('state', PartyState::Pending)
            ->get()
            ->pluck('id');
        return OrderedItem::query()
            ->whereIn('party_id', $partyIds)
            ->get();
    }

    public function addNewOrderedItems(
        Restaurant $restaurant,
        int        $partyId,
        array      $menuIds
    )
    {
        // 自分の店以外のpartyへの注文は受けつけない
        if (!Party::query()
            ->where('id', $partyId)
            ->where('restaurant_id', $restaurant->id)
            ->exists()) {
            throw new ForbiddenException("Forbidden party specified");
        }

        $orderedMenus = [];
        foreach ($menuIds as $menuId) {
            $menu = Menu::query()->find($menuId);

            if (is_null($menu)) {
                // 存在しないメニューが指定されていた時
                throw new NotFoundException();
            }

            if ($menu->restaurant_id != $restaurant->id) {
                // 他店のメニューが指定されていた時
                throw new ForbiddenException("Forbidden menu specified");
            }

            array_push($orderedMenus, $menu);
        }

        $orderedMenuRecords = [];

        foreach ($orderedMenus as $orderMenu) {
            array_push(
                $orderedMenuRecords,
                OrderedItem::create([
                    'party_id' => $partyId,
                    'menu_id' => $orderMenu->id,
                ])
            );
        }

        return $orderedMenuRecords;
    }

    public function deleteOrder(
        int $restaurantId,
        int $orderId
    ) {
        $targetOrder = OrderedItem::query()->find($orderId);

        if (is_null($targetOrder)) {
            // 指定されたidを持つordered_itemがない時
            throw new NotFoundException("The ordered item $orderId is not found.");
        }

        $party = Party::query()->find($targetOrder->party_id);

        if ($party->restaurant_id === $restaurantId) {
            $targetOrder->delete();
            // 削除した注文のレコードを返す
            return $targetOrder;
        } else {
            // 他店の食べる人の注文を削除しようとしていた時
            throw new ForbiddenException("Forbidden party specified.");
        }
    }
}
