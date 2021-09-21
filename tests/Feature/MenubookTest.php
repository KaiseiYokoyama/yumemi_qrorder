<?php

namespace Tests\Feature;

use App\Models\Menu;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\CustomerTestCase;
use Tests\TestCase;

class MenubookTest extends CustomerTestCase
{
    use DatabaseMigrations;

    /**
     * テスト成功：認証に失敗したため、401が返ってくる
     */
    public function test_get_認証失敗_401()
    {
        // 認証を切る
        $this->withCredentials = false;

        // GET
        $this->getJson('/api/menubook')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * テスト成功：食べる人が滞在している店のメニュー一覧が返ってくる&200が返ってくる
     */
    public function test_get_滞在している店のメニュー一覧が取得できる_200()
    {
        $menus = Menu::query()
            ->where('restaurant_id', $this->party->restaurant_id)
            ->get();

        $this->get('/api/menubook')
            ->assertStatus(Response::HTTP_OK)
            ->assertSimilarJson($menus->jsonSerialize());
    }
}
