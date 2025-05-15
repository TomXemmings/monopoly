<?php

namespace App\Http\Controllers;

use App\DTO\OrderStatusModel;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;

class MonopolyController extends Controller
{
    use App\DTO\OrderStatusModel;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ExternalOrderController extends Controller
{
    public function updateOrderStatus(Request $request, string $orderId)
    {
        if (!Str::isUuid($orderId)) {
            throw new BadRequestHttpException('Некорректный UUID заказа');
        }

        $data = $request->validate([
            'status'         => 'required|string',
            'refueledVolume' => 'nullable|numeric',
            'cost'           => 'nullable|numeric',
            'cancelReason'   => 'nullable|string',
            'updatedAt'      => 'required|date_format:Y-m-d\TH:i:sP',
        ]);

        $orderStatus = new OrderStatusModel(
            status:         OrderStatusEnum::from($data['status']),
            refueledVolume: $data['refueledVolume'] ?? null,
            cost:           $data['cost'] ?? null,
            cancelReason:   $data['cancelReason'] ?? null,
            updatedAt:      $data['updatedAt'],
        );

        return response()->json(['message' => 'Статус заказа успешно обновлен'], 200);
    }
}


}
