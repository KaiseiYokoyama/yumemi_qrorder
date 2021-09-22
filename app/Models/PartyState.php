<?php

namespace App\Models;

class PartyState
{
    /**
     * 初期状態
     */
    const Pending = 0;
    /**
     * 会計待ち
     */
    const AccountWaiting = 1;
    /**
     * 会計済み
     */
    const Accounted = 2;
}
