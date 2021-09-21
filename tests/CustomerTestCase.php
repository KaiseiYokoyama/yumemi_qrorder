<?php

namespace Tests;

use App\Models\Party;

abstract class CustomerTestCase extends TestCase
{
    use CreatesApplication;

    protected int $partyId = 1;
    protected ?Party $party;

    protected function setUp(): void
    {
        parent::setUp();
        // テスト前に毎回ログインする
        $this->login();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // テスト後は毎回ログアウトする
        $this->logout();
    }

    protected function login():void
    {
        $this->party = Party::query()->find($this->partyId);
        $uuid = $this->party->uuid;

        $this->withCredentials()
            ->withUnencryptedCookie('session_secret', $uuid);
    }

    protected function logout(): void
    {
        $this->withCredentials = false;
    }

}
