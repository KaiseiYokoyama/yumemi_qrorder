<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\OrderedItem;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\CustomerTestCase;

class OrderedItemTest extends CustomerTestCase
{
    use DatabaseMigrations;

    /**
     * テスト成功：
     */
    public function test_get_post_認証失敗_403()
    {
        // 認証を切る
        $this->withCredentials = false;

        // GET
        $this->getJson('/api/ordered_item')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        // POST
        $restaurant = $this->party->restaurant_id;
        $order = Menu::all()
            ->where('restaurant_id', $restaurant)
            ->take(3);
        $order = $order->pluck('id');
        $orderJSON = [
            'menu_ids' => $order,
        ];
        $this->postJson('/api/ordered_item',$orderJSON)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * テスト成功：正しい内容の注文した品物一覧が取得できる&200が返ってくる
     */
    public function test_get_正しい内容の注文した品物一覧が取得できる_200()
    {
        $response = $this->get('/api/ordered_item');

        $orderedItems = OrderedItem::query()
            ->where('party_id', $this->partyId)
            ->get();

        $response->assertStatus(200)
            ->assertSimilarJson($orderedItems->jsonSerialize());
    }

    /**
     * テスト成功：POSTしたordered_itemのレコードが返ってくる&201が返ってくる
     */
    public function test_post_メニューを注文する_postしたordered_itemのレコードが返ってくる_201()
    {
        $restaurant = $this->party->restaurant_id;
        $order = Menu::all()
            ->where('restaurant_id', $restaurant)
            ->take(3);

        $order = $order->pluck('id');
        $orderJSON = [
            'menu_ids' => $order,
        ];

        $request = $this->postJson('/api/ordered_item', $orderJSON);

        $orderedItems = OrderedItem::query()
            ->whereIn('menu_id', $order)
            ->get();

        $request->assertStatus(Response::HTTP_CREATED)
            ->assertSimilarJson($orderedItems->jsonSerialize());
    }

    /**
     * テスト成功：403が返ってくる
     */
    public function test_post_別の店舗のメニューを注文する_403()
    {
        $restaurant = $this->party->restaurant_id;
        $order = Menu::all()
            ->where('restaurant_id', '<>', $restaurant)
            ->take(3);

        $order = $order->pluck('id');
        $orderJSON = [
            'menu_ids' => $order,
        ];

        $request = $this->postJson('/api/ordered_item', $orderJSON);

        $request->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * テスト成功：400が返ってくる
     */
    public function test_post_存在しないメニューの注文を試みる_400()
    {
        // 存在しないメニューを指定
        $orderMenu = [
            'menu_ids' => [20210917],
        ];

        $this->postJson('/api/ordered_item', $orderMenu)
            ->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * テスト成功：422が返ってくる
     */
    public function test_post_バリデーション違反_メニュー注文失敗_422()
    {
        $order = [-1, -2];
        $orderJSON = [
            'menu_ids' => $order,
        ];

        $this->postJson('/api/ordered_item', $orderJSON)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $order = [];
        $orderJSON = [
            'menu_ids' => $order,
        ];

        $this->postJson('/api/ordered_item', $orderJSON)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
