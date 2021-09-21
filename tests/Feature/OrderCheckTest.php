<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\OrderedItem;
use App\Models\Party;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Nette\Utils\DateTime;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class OrderCheckTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * テスト成功：認証失敗、401が返ってくる
     */
    public function test_get_認証失敗_403()
    {
        // TODO 店員さんの認証
        self::markTestIncomplete();

        // GET
        $this->getJson('/api/order_check')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * テスト成功：正しい内容の注文された品物一覧が取得できる&200が返ってくる
     */
    public function test_get_正しい内容の注文された品物一覧が取得できる_200()
    {
        $parties = Party::query()
            ->where('restaurant_id', 1)
            ->get();

        $orders = OrderedItem::query()
            ->whereIn('party_id', $parties->pluck('id'))
            ->get();

        $this->get('/api/order_check')
            ->assertStatus(Response::HTTP_OK)
            ->assertSimilarJson($orders->jsonSerialize());
    }

    /**
     * テスト成功：POSTしたordered_itemのレコードが返ってくる&201が返ってくる
     */
    public function test_post_postしたordered_itemのレコードが返ってくる_201()
    {
        $party = Party::query()
            ->where('restaurant_id', 1)
            ->first();
        $menus = Menu::query()
            ->where('restaurant_id', $party->restaurant_id)
            ->take(3)
            ->get();

        $orderJSON = [
            'party' => $party->id,
            'menu_ids' => $menus->pluck('id'),
        ];

        $request = $this->postJson('/api/order_check', $orderJSON);

        $orderedItems = OrderedItem::query()
            ->where('party_id', $party->id)
            ->whereIn('menu_id', $menus->pluck('id'))
            ->latest('id')
            ->take(count($menus->pluck('id')))
            ->get();

        $request->assertStatus(Response::HTTP_CREATED)
            ->assertSimilarJson($orderedItems->jsonSerialize());
    }

    /**
     * テスト成功：403が返ってくる
     */
    public function test_post_別の店舗のメニューを注文する_403()
    {
        $party = Party::query()
            ->where('restaurant_id', 1)
            ->first();
        $menus = Menu::query()
            ->where('restaurant_id', '<>', $party->restaurant_id)
            ->take(3)
            ->get();

        $orderJSON = [
            'party' => $party->id,
            'menu_ids' => $menus->pluck('id'),
        ];

        $this->postJson('/api/order_check', $orderJSON)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * テスト成功：403が返ってくる
     */
    public function test_post_別の店舗の食べる人の注文を出す_403()
    {
        $party = Party::query()
            ->where('restaurant_id', '<>', 1)
            ->first();

        $menus = Menu::query()
            ->where('restaurant_id', 1)
            ->take(3)
            ->get();

        $orderJSON = [
            'party' => $party->id,
            'menu_ids' => $menus->pluck('id'),
        ];

        $this->postJson('/api/order_check', $orderJSON)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * テスト成功：404が返ってくる
     */
    public function test_post_存在しないメニューの注文を試みる_404()
    {
        $party = Party::query()
            ->where('restaurant_id', 1)
            ->first();

        // 存在しないメニューを指定
        $orderMenu = [
            'party' => $party->id,
            'menu_ids' => [20210917],
        ];

        $this->postJson('/api/order_check', $orderMenu)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * テスト成功：422が返ってくる
     */
    public function test_post_バリデーション違反_メニュー注文失敗_422()
    {
        $party = Party::query()
            ->where('restaurant_id', 1)
            ->first();

        $order = [-1, -2];
        $orderJSON = [
            'party' => $party->id,
            'menu_ids' => $order,
        ];

        $this->postJson('/api/order_check', $orderJSON)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $order = [];
        $orderJSON = [
            'menu_ids' => $order,
        ];

        $this->postJson('/api/order_check', $orderJSON)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * テスト成功：削除したordered_itemのレコードが返ってくる&200が返ってくる
     */
    public function test_delete_削除したordered_itemのレコードが返ってくる_200()
    {
        $party = Party::query()
            ->where('restaurant_id', 1)
            ->first();
        $ordered_item = OrderedItem::query()
            ->where('party_id', $party->id)
            ->first();

        $deleteJSON = [
            'id' => $ordered_item->id,
        ];

        $this->deleteJson('/api/order_check', $deleteJSON)
            ->assertStatus(Response::HTTP_OK)
            ->assertSimilarJson($ordered_item->jsonSerialize());
    }

    /**
     * テスト成功：403が返ってくる
     */
    public function test_delete_別の店舗の食べる人の注文を消す_403()
    {
        $party = Party::query()
            ->where('restaurant_id', '<>', 1)
            ->first();
        $ordered_item = OrderedItem::query()
            ->where('party_id', $party->id)
            ->first();

        $deleteJSON = [
            'id' => $ordered_item->id,
        ];

        $this->deleteJson('/api/order_check', $deleteJSON)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * テスト成功：404が返ってくる
     */
    public function test_delete_存在しない注文を消す_404()
    {
        // 存在しない注文を指定
        $deleteJSON = [
            'id' => 20210921,
        ];

        $this->deleteJson('/api/order_check', $deleteJSON)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * テスト成功：422が返ってくる
     */
    public function test_delete_バリデーション違反_422()
    {
        $deleteJSON = [
            'id' => -1,
        ];

        $this->deleteJson('/api/order_check', $deleteJSON)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
