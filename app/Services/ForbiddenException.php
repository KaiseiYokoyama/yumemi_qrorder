<?php

namespace App\Services;

/**
 * 例外：DBに対して何らかの操作を試みたが、権限がなかった
 */
class ForbiddenException extends \RuntimeException
{
}
