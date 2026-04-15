<?php

use App\Http\Middleware\EnsureUserRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], $e instanceof HttpException ? $e->getStatusCode() : 500);
            }

            if (view()->exists('errors.'.($e instanceof HttpException ? $e->getStatusCode() : 500))) {
                return response()->view('errors.'.($e instanceof HttpException ? $e->getStatusCode() : 500), ['exception' => $e], $e instanceof HttpException ? $e->getStatusCode() : 500);
            }

            $message = $e->getMessage() ?: 'An error occurred';
            $statusCode = $e instanceof HttpException ? $e->getStatusCode() : 500;

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => $message], $statusCode);
            }

            return response("<!DOCTYPE html>
<html><head><title>Error</title><style>
body{font-family:Segoe UI,sans-serif;display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0;background:#f8f8f8}
.card{background:#fff;padding:32px;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.1);max-width:500px;width:90%;text-align:center}
.error{color:#dc3545;font-size:1.2rem;font-weight:bold;margin-bottom:16px}
.message{color:#333;margin-bottom:24px}
button{background:#1f4e79;color:#fff;border:none;padding:12px 24px;border-radius:8px;cursor:pointer}
button:hover{background:#153553}
</style></head><body>
<div class='card'>
<div class='error'>Error!</div>
<div class='message'>".htmlspecialchars($message)."</div>
<button onclick='history.back()'>Go Back</button>
</div></body></html>", $statusCode);
        });
    })->create();
