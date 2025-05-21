<?php

namespace App\Http\Middleware;

use App\Services\LoggingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Throwable;

class ErrorHandlingMiddleware
{
    /**
     * @var LoggingService
     */
    protected $loggingService;

    /**
     * ErrorHandlingMiddleware constructor.
     *
     * @param LoggingService $loggingService
     */
    public function __construct(LoggingService $loggingService)
    {
        $this->loggingService = $loggingService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (Throwable $exception) {
            // Log the exception
            $this->loggingService->exception($exception, 'Unhandled exception in request', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Handle API requests
            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->handleApiException($request, $exception);
            }

            // Handle web requests
            return $this->handleWebException($request, $exception);
        }
    }

    /**
     * Handle API exceptions
     *
     * @param Request $request
     * @param Throwable $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleApiException(Request $request, Throwable $exception)
    {
        $statusCode = $this->getStatusCode($exception);
        
        return response()->json([
            'success' => false,
            'message' => $this->getMessage($exception),
            'error_code' => $exception->getCode() ?: $statusCode,
            'errors' => $this->getErrors($exception),
            'trace' => App::environment('production') ? null : $exception->getTrace(),
        ], $statusCode);
    }

    /**
     * Handle web exceptions
     *
     * @param Request $request
     * @param Throwable $exception
     * @return \Illuminate\Http\Response
     */
    protected function handleWebException(Request $request, Throwable $exception)
    {
        // Rethrow the exception to let Laravel's exception handler handle it
        throw $exception;
    }

    /**
     * Get appropriate status code for the exception
     *
     * @param Throwable $exception
     * @return int
     */
    protected function getStatusCode(Throwable $exception): int
    {
        if (method_exists($exception, 'getStatusCode')) {
            return $exception->getStatusCode();
        }

        if (method_exists($exception, 'getCode') && $exception->getCode() >= 100 && $exception->getCode() < 600) {
            return $exception->getCode();
        }

        // Map common exceptions to status codes
        $statusCodes = [
            'Illuminate\Auth\AuthenticationException' => 401,
            'Illuminate\Auth\Access\AuthorizationException' => 403,
            'Illuminate\Database\Eloquent\ModelNotFoundException' => 404,
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException' => 404,
            'Illuminate\Validation\ValidationException' => 422,
            'Illuminate\Database\QueryException' => 500,
        ];

        foreach ($statusCodes as $class => $code) {
            if ($exception instanceof $class) {
                return $code;
            }
        }

        return 500;
    }

    /**
     * Get appropriate message for the exception
     *
     * @param Throwable $exception
     * @return string
     */
    protected function getMessage(Throwable $exception): string
    {
        if (App::environment('production')) {
            $statusCode = $this->getStatusCode($exception);
            
            // In production, don't expose internal error messages
            if ($statusCode >= 500) {
                return 'Server Error';
            }
        }

        return $exception->getMessage() ?: 'An error occurred';
    }

    /**
     * Get errors from the exception
     *
     * @param Throwable $exception
     * @return array
     */
    protected function getErrors(Throwable $exception): array
    {
        // For validation exceptions, return the validation errors
        if (method_exists($exception, 'errors')) {
            return $exception->errors();
        }

        return [];
    }
}
