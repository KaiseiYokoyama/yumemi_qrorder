<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Models\OrderedItem;
use App\Models\Party;
use App\Services\ForbiddenException;
use App\Services\NotFoundException;
use App\Services\OrderedItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderedItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * 食べる人が注文した全ての料理を返す
     */
    public function index(OrderedItemService $orderedItemService)
    {
        /* @var Party $party */
        $party = Auth::user();

        return $orderedItemService->getOrderedByPartyId($party->id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateOrderRequest  $request
     */
    public function store(CreateOrderRequest $request, OrderedItemService $orderedItemService)
    {
        /* @var Party $party */
        $party = Auth::user();

        // validate request
        $validated = $request->validated();

        try {
            $orderedItemRecords = $orderedItemService->addNewOrder(
                $party,
                $validated['menu_ids']
            );
            return \response()->json($orderedItemRecords, Response::HTTP_CREATED);
        } catch (NotFoundException $e) {
//            throw new HttpException(Response::HTTP_BAD_REQUEST);
            return \response()->json([
                'error' => 'Some ordered item is not found.'
            ], Response::HTTP_NOT_FOUND);
        } catch (ForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN);
        }
    }
}
