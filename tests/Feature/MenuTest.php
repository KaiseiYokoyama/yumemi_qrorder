<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Party;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use DatabaseMigrations;

    static $new_menu = [
        'name' => 'ハンバーグ',
        'price' => '660',
        'image_url' => 'https://pbs.twimg.com/media/EsK3YCMVgAUJ2yb?format=jpg&name=large',
    ];

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
    public function test_get_post_認可失敗_403が返ってくる()
    {
        // GET
        $this->get('/api/menu')
            ->assertStatus(Response::HTTP_FORBIDDEN);

        // POST
        $this->postJson('/api/menu', MenuTest::$new_menu)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_get_認可成功_200が返ってくる_店舗ごとに違ったメニューが帰ってくる()
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

    public function test_post_メニューを追加する_追加したメニューのレコードが帰ってくる()
    {
        $party = Party::query()->find(1);
        $uuid = $party->value('uuid');
        $cookie = ['session_secret' => $uuid];

        // cookieは暗号化しない
        $response = $this->postJsonWithCookie('/api/menu', $cookie, MenuTest::$new_menu);

        // HTTP status 201 created
        $response->assertStatus(Response::HTTP_CREATED)
            // 内容が正しい
            ->assertJson(MenuTest::$new_menu);
    }

    // NOTE: TestCaseをextendした方がいいかも
    public function postJsonWithCookie($uri, $cookie, $data): TestResponse
    {
        return $this->call(
            'post',
            $uri,
            [],
            $cookie,
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            json_encode($data)
        );
    }
}
