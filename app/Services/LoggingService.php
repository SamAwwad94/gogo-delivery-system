<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Centralized logging service for the application
 */
class LoggingService
{
    /**
     * Log levels
     */
    const LEVEL_EMERGENCY = 'emergency';
    const LEVEL_ALERT = 'alert';
    const LEVEL_CRITICAL = 'critical';
    const LEVEL_ERROR = 'error';
    const LEVEL_WARNING = 'warning';
    const LEVEL_NOTICE = 'notice';
    const LEVEL_INFO = 'info';
    const LEVEL_DEBUG = 'debug';

    /**
     * Log channels
     */
    const CHANNEL_STACK = 'stack';
    const CHANNEL_SINGLE = 'single';
    const CHANNEL_DAILY = 'daily';
    const CHANNEL_SLACK = 'slack';
    const CHANNEL_EMERGENCY = 'emergency';

    /**
     * Log contexts
     */
    const CONTEXT_USER = 'user';
    const CONTEXT_REQUEST = 'request';
    const CONTEXT_RESPONSE = 'response';
    const CONTEXT_EXCEPTION = 'exception';
    const CONTEXT_PERFORMANCE = 'performance';
    const CONTEXT_DATABASE = 'database';
    const CONTEXT_CACHE = 'cache';
    const CONTEXT_API = 'api';
    const CONTEXT_AUTH = 'auth';

    /**
     * Log an emergency message
     *
     * @param string $message
     * @param array $context
     * @param string $channel
     * @return void
     */
    public function emergency(string $message, array $context = [], string $channel = self::CHANNEL_EMERGENCY): void
    {
        $this->log(self::LEVEL_EMERGENCY, $message, $context, $channel);
    }

    /**
     * Log an alert message
     *
     * @param string $message
     * @param array $context
     * @param string $channel
     * @return void
     */
    public function alert(string $message, array $context = [], string $channel = self::CHANNEL_STACK): void
    {
        $this->log(self::LEVEL_ALERT, $message, $context, $channel);
    }

    /**
     * Log a critical message
     *
     * @param string $message
     * @param array $context
     * @param string $channel
     * @return void
     */
    public function critical(string $message, array $context = [], string $channel = self::CHANNEL_STACK): void
    {
        $this->log(self::LEVEL_CRITICAL, $message, $context, $channel);
    }

    /**
     * Log an error message
     *
     * @param string $message
     * @param array $context
     * @param string $channel
     * @return void
     */
    public function error(string $message, array $context = [], string $channel = self::CHANNEL_STACK): void
    {
        $this->log(self::LEVEL_ERROR, $message, $context, $channel);
    }

    /**
     * Log a warning message
     *
     * @param string $message
     * @param array $context
     * @param string $channel
     * @return void
     */
    public function warning(string $message, array $context = [], string $channel = self::CHANNEL_STACK): void
    {
        $this->log(self::LEVEL_WARNING, $message, $context, $channel);
    }

    /**
     * Log a notice message
     *
     * @param string $message
     * @param array $context
     * @param string $channel
     * @return void
     */
    public function notice(string $message, array $context = [], string $channel = self::CHANNEL_STACK): void
    {
        $this->log(self::LEVEL_NOTICE, $message, $context, $channel);
    }

    /**
     * Log an info message
     *
     * @param string $message
     * @param array $context
     * @param string $channel
     * @return void
     */
    public function info(string $message, array $context = [], string $channel = self::CHANNEL_STACK): void
    {
        $this->log(self::LEVEL_INFO, $message, $context, $channel);
    }

    /**
     * Log a debug message
     *
     * @param string $message
     * @param array $context
     * @param string $channel
     * @return void
     */
    public function debug(string $message, array $context = [], string $channel = self::CHANNEL_STACK): void
    {
        $this->log(self::LEVEL_DEBUG, $message, $context, $channel);
    }

    /**
     * Log an exception
     *
     * @param Throwable $exception
     * @param string $message
     * @param array $context
     * @param string $channel
     * @return void
     */
    public function exception(Throwable $exception, string $message = '', array $context = [], string $channel = self::CHANNEL_STACK): void
    {
        $exceptionContext = [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ];

        $context = array_merge($context, [self::CONTEXT_EXCEPTION => $exceptionContext]);
        $message = $message ?: 'Exception: ' . $exception->getMessage();

        $this->error($message, $context, $channel);
    }

    /**
     * Log a database query
     *
     * @param string $query
     * @param array $bindings
     * @param float $time
     * @param string $channel
     * @return void
     */
    public function query(string $query, array $bindings = [], float $time = 0.0, string $channel = self::CHANNEL_STACK): void
    {
        $context = [
            self::CONTEXT_DATABASE => [
                'query' => $query,
                'bindings' => $bindings,
                'time' => $time,
            ],
        ];

        $this->debug('Database query executed', $context, $channel);
    }

    /**
     * Log an API request
     *
     * @param string $method
     * @param string $url
     * @param array $headers
     * @param mixed $body
     * @param string $channel
     * @return void
     */
    public function apiRequest(string $method, string $url, array $headers = [], $body = null, string $channel = self::CHANNEL_STACK): void
    {
        $context = [
            self::CONTEXT_API => [
                'method' => $method,
                'url' => $url,
                'headers' => $headers,
                'body' => $body,
            ],
        ];

        $this->info("API Request: {$method} {$url}", $context, $channel);
    }

    /**
     * Log an API response
     *
     * @param string $method
     * @param string $url
     * @param int $statusCode
     * @param array $headers
     * @param mixed $body
     * @param float $time
     * @param string $channel
     * @return void
     */
    public function apiResponse(string $method, string $url, int $statusCode, array $headers = [], $body = null, float $time = 0.0, string $channel = self::CHANNEL_STACK): void
    {
        $context = [
            self::CONTEXT_API => [
                'method' => $method,
                'url' => $url,
                'status_code' => $statusCode,
                'headers' => $headers,
                'body' => $body,
                'time' => $time,
            ],
        ];

        $level = $statusCode >= 400 ? self::LEVEL_ERROR : self::LEVEL_INFO;
        $this->log($level, "API Response: {$method} {$url} - {$statusCode}", $context, $channel);
    }

    /**
     * Log an authentication event
     *
     * @param string $event
     * @param mixed $user
     * @param array $context
     * @param string $channel
     * @return void
     */
    public function auth(string $event, $user = null, array $context = [], string $channel = self::CHANNEL_STACK): void
    {
        $authContext = [
            'event' => $event,
        ];

        if ($user) {
            $authContext['user'] = [
                'id' => $user->id ?? null,
                'email' => $user->email ?? null,
                'name' => $user->name ?? null,
            ];
        }

        $context = array_merge($context, [self::CONTEXT_AUTH => $authContext]);
        $this->info("Auth: {$event}", $context, $channel);
    }

    /**
     * Log a message with the specified level
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @param string $channel
     * @return void
     */
    protected function log(string $level, string $message, array $context = [], string $channel = self::CHANNEL_STACK): void
    {
        // Add request information to context if available
        if (request()) {
            $context[self::CONTEXT_REQUEST] = [
                'id' => request()->id ?? null,
                'ip' => request()->ip(),
                'method' => request()->method(),
                'url' => request()->fullUrl(),
                'user_agent' => request()->userAgent(),
            ];

            // Add authenticated user to context if available
            if (auth()->check()) {
                $context[self::CONTEXT_USER] = [
                    'id' => auth()->id(),
                    'email' => auth()->user()->email,
                    'name' => auth()->user()->name,
                ];
            }
        }

        // Add timestamp
        $context['timestamp'] = now()->toIso8601String();

        // Log to the specified channel
        Log::channel($channel)->{$level}($message, $context);
    }
}
