<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Список полей, которые никогда не попадают в сессию при ошибках валидации
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Регистрация колбэков для обработки исключений
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Можно добавить кастомную логику логирования
        });
    }

    /**
     * Глобальная обработка исключений приложения
     *
     * @param \Illuminate\Http\Request $request
     * @param Throwable $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        // Глобальная обработка бизнес-исключений (например, RuntimeException)
        if ($exception instanceof \RuntimeException) {
            $message = $exception->getMessage();
            // Для API-запросов возвращаем JSON с ошибкой
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 400);
            }
            // Для web-запросов — редирект с ошибкой
            return redirect()->back()->withInput()->with('error', $message);
        }
        // Для остальных исключений — стандартная обработка Laravel
        return parent::render($request, $exception);
    }
} 