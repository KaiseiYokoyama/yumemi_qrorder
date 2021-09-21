<?php

namespace App\Http\Controllers;

use App\Models\Party;
use App\Services\MenuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenubookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * 食べる人が滞在している店の全てのメニューを返す
     */
    public function index(MenuService $service)
    {
        /* @var Party $party */
        $party = Auth::user();

        return $service->getMenuByRestaurantId($party->restaurant_id);
    }
}
