<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteMenuRequest;
use App\Http\Requests\StoreMenuRequest;
use App\Models\Menu;
use App\Models\Party;
use App\Services\ForbiddenException;
use App\Services\MenuService;
use App\Services\NotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MenuController extends Controller
{
    // TODO: リソースコントローラを使ってリファクタリング

    /**
     * お店の人に、店のメニュー一覧を提供する
     */
    public function getAll(Request $request): Collection
    {
        // TODO: 店員さんであることの認証
        $dummyRestaurantId = 1;

        $model_query = Menu::query()
            ->where('restaurant_id', $dummyRestaurantId);
        $menus = $model_query->get();

        return $menus;
    }

    /**
     * 新たなメニューを格納する
     */
    public function store(StoreMenuRequest $request, MenuService $menuService) {
        // TODO: 店員さんであることの認証
        $dummyRestaurantId = 1;

        // validate request
        $validated = $request->validated();

        return $menuService->addNewMenu(
            $dummyRestaurantId,
            $validated['name'],
            $validated['price'],
            $validated['image_url'],
        );
    }

    public function delete(DeleteMenuRequest $request, MenuService $menuService)
    {
        // TODO: 店員さんの認証
        $dummyRestaurantId = 1;

        // validate request
        $validated = $request->validated();

        try {
            return $menuService->deleteMenu($dummyRestaurantId, $validated['id']);
        } catch (NotFoundException $e) {
            // 指定されたidを持つメニューがない時
            throw new HttpException(Response::HTTP_BAD_REQUEST);
        }
    }
}
