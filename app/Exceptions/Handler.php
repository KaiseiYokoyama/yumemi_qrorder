<?php

namespace App\Exceptions;

use App\Services\ForbiddenException;
use App\Services\NotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

//        $this->renderable(function (ForbiddenException $e) {
//            throw new HttpException(Response::HTTP_FORBIDDEN);
//        });

        $this->renderable(function (ForbiddenException $e) {
            return \response()->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_FORBIDDEN);
        });

        $this->renderable(function (NotFoundException $e) {
            return \response()->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        });
    }
}
