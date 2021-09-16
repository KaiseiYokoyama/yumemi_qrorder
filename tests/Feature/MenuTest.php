<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Party;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
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
        $uuid = $party->uuid;
        $cookie = ['session_secret' => $uuid];
        // cookieは暗号化しない
        $response = $this->call('get', '/api/menu', [], $cookie);

        // HTTP status 200
        $response->assertStatus(200);

        // 返答したデータがあっているかどうか
        $menus = Menu::query()->where('restaurant_id', $party->restaurant_id)
            ->get();
        $response->assertSimilarJson($menus->jsonSerialize());
    }

    public function test_メニューを追加する_追加したら()
    {
        $party = Party::query()->find(1);
        $uuid = $party->value('uuid');
        $cookie = ['session_secret' => $uuid];

        $json = [
            'name' => 'ハンバーグ',
            'price' => '660',
            'image_url' => 'https://pbs.twimg.com/media/EsK3YCMVgAUJ2yb?format=jpg&name=large'
        ];
        // cookieは暗号化しない
        $response = $this->call(
            'post',
            '/api/menu',
            [],
            $cookie,
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            json_encode($json)
        );

        // HTTP status 201 created
        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson($json);
    }
}
