<?php

declare(strict_types=1);

use App\Exceptions\StoreNotFoundException;
use App\Exceptions\SuspendedStoreException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        /*
         * Validation errors.
         */
        $exceptions->render(
            function (
                ValidationException $exception,
                Request $request
            ) {
                if (! $request->is('api/*')) {
                    return null;
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'data' => null,
                    'errors' => $exception->errors(),
                ], 422);
            }
        );

        /*
         * Unknown API route or failed Route Model Binding.
         */
        $exceptions->render(
            function (
                NotFoundHttpException $exception,
                Request $request
            ) {
                if (! $request->is('api/*')) {
                    return null;
                }

                return response()->json([
                    'success' => false,
                    'message' => 'The requested resource was not found.',
                    'data' => null,
                ], 404);
            }
        );

        /*
         * Store does not exist.
         */
        $exceptions->render(
            function (
                StoreNotFoundException $exception,
                Request $request
            ) {
                if (! $request->is('api/*')) {
                    return null;
                }

                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                    'data' => null,
                ], 404);
            }
        );

        /*
         * Suspended store business conflict.
         */
        $exceptions->render(
            function (
                SuspendedStoreException $exception,
                Request $request
            ) {
                if (! $request->is('api/*')) {
                    return null;
                }

                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                    'data' => null,
                ], 409);
            }
        );

        /*
         * Any unexpected API error must still
         * return JSON instead of an HTML page.
         */
        $exceptions->render(
            function (
                \Throwable $exception,
                Request $request
            ) {
                if (! $request->is('api/*')) {
                    return null;
                }

                return response()->json([
                    'success' => false,
                    'message' => 'An unexpected server error occurred.',
                    'data' => null,
                ], 500);
            }
        );
    })
    ->create();
