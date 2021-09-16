<?php

namespace Tests;

use App\Models\Party;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected ?Party $party;

    protected function login(): void
    {
        $this->party = Party::query()->find(1);
        $uuid = $this->party->uuid;

        $this
            ->withCredentials()
            ->withUnencryptedCookie('session_secret', $uuid)
        ;
    }

    protected function logout(): void
    {
        $this->withCredentials = false;
    }
}
