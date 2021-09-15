<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Party;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MenuController extends Controller
{
    // TODO: リソースコントローラを使ってリファクタリング

    /**
     *
     */
    public function getAll(Request $request): Collection
    {
        $session_secret = $request->cookie('session_secret');
        $party = Party::query()
            ->where('uuid', $session_secret)
            ->first();

        if ($party == null) {
            throw new HttpException(Response::HTTP_FORBIDDEN);
        }


        $model_query = Menu::query()
            ->where('restaurant_id', $party->value('restaurant_id'));
        $menus = $model_query->get();

        return $menus;
    }
}
