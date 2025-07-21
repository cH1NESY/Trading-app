<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\DTO\OrderCreateDTO;
use App\DTO\OrderUpdateDTO;
use App\DTO\OrderFilterDTO;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Actions\Order\CreateOrderAction;
use App\Actions\Order\UpdateOrderAction;
use App\Actions\Order\CompleteOrderAction;
use App\Actions\Order\CancelOrderAction;
use App\Actions\Order\ResumeOrderAction;
use App\Actions\Order\GetOrdersAction;

class OrderController extends Controller
{
    /**
     * Получить список заказов с фильтрацией и пагинацией
     *
     * @param Request $request HTTP-запрос с фильтрами и пагинацией
     * @param GetOrdersAction $action Action для получения заказов
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, GetOrdersAction $action)
    {
        // Формируем DTO из параметров запроса
        $dto = new OrderFilterDTO($request->all());
        $perPage = $request->get('per_page', 15);
        $page = $request->get('page');
        // Получаем заказы через Action
        $orders = $action->execute($dto, $perPage, $page);
        return response()->json(['success' => true, 'data' => $orders]);
    }

    /**
     * Создать новый заказ
     *
     * @param OrderCreateRequest $request Валидированный запрос
     * @param CreateOrderAction $action Action для создания заказа
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OrderCreateRequest $request, CreateOrderAction $action)
    {
        // Формируем DTO из валидированных данных
        $dto = new OrderCreateDTO($request->validated());
        // Создаём заказ через Action
        $order = $action->execute($dto);
        return response()->json(['success' => true, 'data' => $order, 'message' => 'Заказ успешно создан'], 201);
    }

    /**
     * Получить детали заказа
     *
     * @param Order $order Заказ
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order)
    {
        // Подгружаем склад и товары
        $order->load(['warehouse', 'items.product']);
        return response()->json(['success' => true, 'data' => $order]);
    }

    /**
     * Обновить заказ
     *
     * @param OrderUpdateRequest $request Валидированный запрос
     * @param Order $order Заказ для обновления
     * @param UpdateOrderAction $action Action для обновления заказа
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(OrderUpdateRequest $request, Order $order, UpdateOrderAction $action)
    {
        // Формируем DTO из валидированных данных
        $dto = new OrderUpdateDTO($request->validated());
        // Обновляем заказ через Action
        $order = $action->execute($order, $dto);
        return response()->json(['success' => true, 'data' => $order, 'message' => 'Заказ успешно обновлен']);
    }

    /**
     * Завершить заказ
     *
     * @param Order $order Заказ для завершения
     * @param CompleteOrderAction $action Action для завершения заказа
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete(Order $order, CompleteOrderAction $action)
    {
        // Завершаем заказ через Action
        $order = $action->execute($order);
        return response()->json(['success' => true, 'data' => $order, 'message' => 'Заказ успешно завершен']);
    }

    /**
     * Отменить заказ
     *
     * @param Order $order Заказ для отмены
     * @param CancelOrderAction $action Action для отмены заказа
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Order $order, CancelOrderAction $action)
    {
        // Отменяем заказ через Action
        $order = $action->execute($order);
        return response()->json(['success' => true, 'data' => $order, 'message' => 'Заказ успешно отменен']);
    }

    /**
     * Возобновить отменённый заказ
     *
     * @param Order $order Заказ для возобновления
     * @param ResumeOrderAction $action Action для возобновления заказа
     * @return \Illuminate\Http\JsonResponse
     */
    public function resume(Order $order, ResumeOrderAction $action)
    {
        // Возобновляем заказ через Action
        $order = $action->execute($order);
        return response()->json(['success' => true, 'data' => $order, 'message' => 'Заказ успешно возобновлен']);
    }
}
