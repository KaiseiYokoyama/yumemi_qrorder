<?php

namespace App\Services;

/**
 * 例外：DBから該当するデータを見つけられず、処理を継続できなかった
 */
class NotFoundException extends \RuntimeException
{
}
