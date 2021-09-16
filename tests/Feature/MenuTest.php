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
     * テスト成功：session_secretの指定がないので、GETもPOSTも認可失敗&403が返ってくる
     */
    public function test_get_post_delete_認可失敗_403()
    {
        // GET
        $this->get('/api/menu')
            ->assertStatus(Response::HTTP_FORBIDDEN);

        // POST
        $this->postJson('/api/menu', MenuTest::$new_menu)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        // DELETE
        $this->deleteJsonWithCookie(
            '/api/menu',
            [], // 空のcookie
            ['id' => 1,]   // 適当なデータを指定
        )
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * テスト成功：食べにきた人の滞在しているお店の全てのメニューが返ってくる&200が返ってくる
     */
    public function test_get_認可成功_店舗ごとに違ったメニューが返ってくる_200()
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

    /**
     * テスト成功：POSTしたメニューのレコードが返ってくる&200が返ってくる
     */
    public function test_post_メニューを追加する_追加したメニューのレコードが帰ってくる_200()
    {
        $party = Party::query()->find(1);
        $uuid = $party->uuid;
        $cookie = ['session_secret' => $uuid];

        // cookieは暗号化しない
        $response = $this->postJsonWithCookie('/api/menu', $cookie, MenuTest::$new_menu);

        // HTTP status 201 created
        $response->assertStatus(Response::HTTP_CREATED)
            // 内容が正しい
            ->assertJson(MenuTest::$new_menu);
    }

    /**
     * テスト成功：422が返ってくる
     */
    public function test_post_バリデーション違反_メニュー追加失敗_422()
    {
        $party = Party::query()->find(1);
        $uuid = $party->uuid;
        $cookie = ['session_secret' => $uuid];

        $new_menu = [
            'name' => '追加できないハンバーグ',
            'price' => '-100',
            'image_url' => 'https://pbs.twimg.com/media/EsK3YCMVgAUJ2yb?format=jpg&name=large',
        ];

        $this->postJsonWithCookie('/api/menu', $cookie, $new_menu)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * テスト成功：削除したメニューのレコードが返ってくる&200が返ってくる
     */
    public function test_delete_メニューを削除する_削除したメニューのレコードが帰ってくる_200()
    {
        $target = Menu::query()->find(1);

        $party = Party::query()
            ->where('restaurant_id', $target->restaurant_id)
            ->first();
        $uuid = $party->uuid;
        $cookie = ['session_secret' => $uuid];

        $this->deleteJsonWithCookie('/api/menu', $cookie, [
            'id' => $target->id,
        ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson($target->jsonSerialize());

        self::assertNull(Menu::query()->find(1));
    }

    /**
     * テスト成功：400が返ってくる
     */
    public function test_delete_存在しないメニューの削除を試みる_400()
    {
        $party = Party::query()->find(1);
        $uuid = $party->uuid;
        $cookie = ['session_secret' => $uuid];

        // 存在しないメニューを指定
        $delete_menu = ['id' => 20210916];

        $this->deleteJsonWithCookie('/api/menu', $cookie, $delete_menu)
            ->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * テスト成功：403が返ってくる
     */
    public function test_delete_他店のメニューの削除を試みる_403()
    {
        // 店舗1の適当なメニューを1件取得
        $target = Menu::query()->where('restaurant_id', 1)->first();

        // 店舗2のcookieを取得
        $party = Party::query()
            ->where('restaurant_id', 2)
            ->first();
        $uuid = $party->uuid;
        $cookie = ['session_secret' => $uuid];

        // よその店のメニューを削除するリクエストを送る
        $this->deleteJsonWithCookie('/api/menu', $cookie, [
            'id' => $target->id,
        ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * テスト成功：422が返ってくる
     */
    public function test_delete_バリデーション違反_422()
    {
        $party = Party::query()->find(1);
        $uuid = $party->uuid;
        $cookie = ['session_secret' => $uuid];

        // バリデーション違反のidを指定
        $delete_menu = ['id' => -1];

        $this->deleteJsonWithCookie('/api/menu', $cookie, $delete_menu)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    // NOTE: TestCaseをextendした方がいいかも
    public function sendJsonWithCookie($method, $uri, $cookie, $data): TestResponse
    {
        return $this->call(
            $method,
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

    public function postJsonWithCookie($uri, $cookie, $data): TestResponse
    {
        return $this->sendJsonWithCookie('post', $uri, $cookie, $data);
    }

    public function deleteJsonWithCookie($uri, $cookie, $data): TestResponse
    {
        return $this->sendJsonWithCookie('delete', $uri, $cookie, $data);
    }
}
