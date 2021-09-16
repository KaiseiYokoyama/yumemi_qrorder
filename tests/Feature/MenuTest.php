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

    public function tearDown(): void
    {
        parent::tearDown();

        // Logging out after each tests
        $this->logout();
    }

    /**
     * session_secretの指定がない時は認可失敗になる
     *
     * @return void
     */
    public function test_get_post_認可失敗_403()
    {
        // GET
        $this->getJson('/api/menu')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        // POST
        $this->postJson('/api/menu', MenuTest::$new_menu)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_get_認可成功_店舗ごとに違ったメニューが帰ってくる_200()
    {
        $this->login();
        $response = $this->getJson('/api/menu');

        // HTTP status 200
        $response->assertStatus(200);

        // 返答したデータがあっているかどうか
        $menus = Menu::query()->where('restaurant_id', $this->party->restaurant_id)
            ->get();
        $response->assertSimilarJson($menus->jsonSerialize());
    }

    public function test_post_メニューを追加する_追加したメニューのレコードが帰ってくる_200()
    {
        $this->login();

        // cookieは暗号化しない
        $response = $this->postJson('/api/menu', MenuTest::$new_menu);

        // HTTP status 201 created
        $response->assertStatus(Response::HTTP_CREATED)
            // 内容が正しい
            ->assertJson(MenuTest::$new_menu);
    }

    public function test_post_バリデーション違反_メニュー追加失敗_422() {
        $this->login();

        $new_menu = [
            'name' => '追加できないハンバーグ',
            'price' => '-100',
            'image_url' => 'https://pbs.twimg.com/media/EsK3YCMVgAUJ2yb?format=jpg&name=large',
        ];

        $this->postJson('/api/menu', $new_menu)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
