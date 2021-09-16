<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuRequest;
use App\Models\Menu;
use App\Models\Party;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MenuController extends Controller
{
    // TODO: リソースコントローラを使ってリファクタリング

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * 認証されたお客さんに、滞在している店のメニュー一覧を提供する
     */
    public function getAll(Request $request): Collection
    {
        /* @var Party $party */
        $party = Auth::user();

        $model_query = Menu::query()
            ->where('restaurant_id', $party->restaurant_id);
        $menus = $model_query->get();

        return $menus;
    }

    /**
     * 新たなメニューを格納する
     */
    public function store(StoreMenuRequest $request) {
        // TODO: 店員さんであることの認証

        /* @var Party $party */
        $party = Auth::user();

        // validate request
        $request->validated();

        // JSONであることを確認
        if (!$request->expectsJson()) {
            throw new HttpException(Response::HTTP_BAD_REQUEST);
        }

        $new_menu = new Menu;
        $new_menu->restaurant_id = $party->restaurant_id;
        $new_menu->name = $request->input('name');
        $new_menu->price = $request->input('price');
        $new_menu->image_url = $request->input('image_url');
        $new_menu->save();

        return $new_menu;
    }
}
