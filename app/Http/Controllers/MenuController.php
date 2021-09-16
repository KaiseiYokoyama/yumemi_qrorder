<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteMenuRequest;
use App\Http\Requests\StoreMenuRequest;
use App\Models\Menu;
use App\Models\Party;
use App\Services\MenuService;
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
    public function store(StoreMenuRequest $request) {
        // TODO: 店員さんであることの認証
        $dummyRestaurantId = 1;

        // validate request
        $validated = $request->validated();

        return MenuService::addNewMenu(
            $dummyRestaurantId,
            $validated['name'],
            $validated['price'],
            $validated['image_url'],
        );
    }

    public function delete(DeleteMenuRequest $request)
    {
        // TODO: 店員さんの認証
        $dummyRestaurantId = 1;

        // validate request
        $validated = $request->validated();

        $target_menu = Menu::query()->find($validated['id']);

        // 指定されたidを持つメニューがない時
        if (is_null($target_menu)) {
            throw new HttpException(Response::HTTP_BAD_REQUEST);
        }

        if ($target_menu->restaurant_id === $dummyRestaurantId) {
            // メニューを削除
            $target_menu->delete();
            // 削除したメニューのレコードを返す
            return $target_menu;
        } else {
            // 他店のメニューを削除しようとしていた時
            // NOTE: 404の方が良い？（メニューの存在を漏らさない）
            throw new HttpException(Response::HTTP_FORBIDDEN);
        }
    }
}
