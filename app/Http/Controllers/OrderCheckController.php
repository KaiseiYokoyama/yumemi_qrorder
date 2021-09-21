<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderByRestaurantRequest;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\DeleteOrderRequest;
use App\Models\Restaurant;
use App\Services\NotFoundException;
use App\Services\OrderCheckService;
use App\Services\OrderedItemService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderCheckController extends Controller
{
    /**
     * 店舗内の食べる人の注文一覧を返す
     */
    public function index(OrderCheckService $service)
    {
        // TODO: 店員さんの認証
        $dummyRestaurantId = 1;
        return $service->getOrderedByRestaurantId($dummyRestaurantId);
    }

//    /**
//     * Show the form for creating a new resource.
//     */
//    public function create(CreateOrderByRestaurantRequest $request, OrderCheckService $service)
//    {
//        //
//    }

    /**
     * 店舗内の食べる人に代わって注文を出す
     */
    public function store(CreateOrderByRestaurantRequest $request, OrderCheckService $service)
    {
        // TODO: 店員さんの認証
        $dummyRestaurantId = 1;
        $dummyRestaurant = Restaurant::query()->find($dummyRestaurantId);

        // validate request
        $validated = $request->validated();

        try {
            $orderedItemRecords = $service->addNewOrderedItems(
                $dummyRestaurant,
                $validated['party'],
                $validated['menu_ids'],
            );

            return \response()->json($orderedItemRecords, Response::HTTP_CREATED);
        } catch (NotFoundException $e) {
            return \response()->json([
                'error' => 'Some ordered item is not found.',
            ], Response::HTTP_NOT_FOUND);
        }
    }

//    /**
//     * Display the specified resource.
//     *
//     * @param int $id
//     * @return \Illuminate\Http\Response
//     */
//    public function show($id)
//    {
//        //
//    }
//
//    /**
//     * Show the form for editing the specified resource.
//     *
//     * @param int $id
//     * @return \Illuminate\Http\Response
//     */
//    public function edit($id)
//    {
//        //
//    }
//
//    /**
//     * Update the specified resource in storage.
//     *
//     * @param \Illuminate\Http\Request $request
//     * @param int $id
//     * @return \Illuminate\Http\Response
//     */
//    public function update(Request $request, $id)
//    {
//        //
//    }
//
    /**
     * 店舗内の食べる人が出した注文を削除する
     */
    public function delete(DeleteOrderRequest $request, OrderCheckService $service)
    {
        // TODO: 店員さんの認証
        $dummyRestaurantId = 1;

        // validate request
        $validated = $request->validated();

        return $service->deleteOrder($dummyRestaurantId, $validated['id']);
    }
}
