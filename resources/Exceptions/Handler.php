<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e){
        //  rollback segala transaksi
        $rendered = parent::render($request, $e);
        $code   = $rendered->getStatusCode();

        $msg = $e->getMessage() ? $e->getMessage()."-".$e->getLine()."-".$e->getFile() : (match ($code) {
            200, 300 => null,
            400 => 'Sorry, please correct your payload before send',
            404 => 'Source not found',
            401 => "Sorry, you don't have authentication",
            500 => 'Server constrained, please call the administrator',
            403 => "Sorry, you don't have authorization",
            default => 'Unknown status code',
        });

        return response()->json( ['success' => false, 'message'=>$msg], $rendered->getStatusCode());
    }
}
