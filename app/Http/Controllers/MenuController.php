<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    // TODO: リソースコントローラを使ってリファクタリング

    /**
     *
     */
    public function getAll(): Collection
    {
        $model_query = Menu::query();
        $menus = $model_query->get();

        return $menus;
    }
}
