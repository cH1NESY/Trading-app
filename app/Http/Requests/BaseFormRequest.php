<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Базовый FormRequest с глобальной обработкой ошибок валидации
 */
abstract class BaseFormRequest extends FormRequest
{
    /**
     * Разрешить выполнение запроса (по умолчанию true)
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Глобальная обработка ошибок валидации
     *
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        // Для API-запросов возвращаем JSON с ошибками
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422)
            );
        }
        // Для web-запросов — стандартное поведение
        parent::failedValidation($validator);
    }
} 