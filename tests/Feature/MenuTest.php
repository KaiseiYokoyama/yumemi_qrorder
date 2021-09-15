<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Party;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:refresh --seed');
    }

    /**
     * session_secretの指定がない時は認可失敗になる
     *
     * @return void
     */
    public function test_認可失敗_403が返ってくる()
    {
        $response = $this->get('/api/menu');

        $response->assertStatus(403);
    }

    public function test_認可成功_200が返ってくる_店舗ごとに違ったメニューが帰ってくる()
    {
        $party = Party::query()->find(1);
        $uuid = $party->value('uuid');
        $cookie = ['session_secret' => $uuid];
        // cookieは暗号化しない
        $response = $this->call('get', '/api/menu', [], $cookie);

        // HTTP status 200
        $response->assertStatus(200);

        // 返答したデータがあっているかどうか
        $menus = Menu::query()->where('restaurant_id', $party->value('restaurant_id'))
            ->get();
        $response->assertSimilarJson($menus->jsonSerialize());
    }
}
