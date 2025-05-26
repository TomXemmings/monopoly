<?php

namespace App\Services;

use App\DTO\BarCodeModel;
use App\DTO\FuelPriceModel;
use App\DTO\FuelStationModel;
use App\DTO\FuelStationColumnStatusModel;
use App\DTO\OrderStatusModel;
use App\DTO\FuelStationDetailsModel;
use App\Enums\PaymentTypeEnum;
use App\Enums\ColumnStateEnum;
use App\Enums\FuelTypeEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\ServiceMethodEnum;
use App\Exceptions\MonopolyApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class MonopolyFuelService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.monopoly.api_key');
        $this->baseUrl = config('services.monopoly.base_url');
    }

    protected function client(): PendingRequest
    {
        return Http::withHeaders([
            'X-Api-Key' => $this->apiKey,
        ])->acceptJson()->baseUrl($this->baseUrl);
    }

    protected function handleRequest(callable $callback): array
    {
        $response = $callback();

        if ($response->successful()) {
            return $response->json();
        }

        $this->handleError($response);
    }

    protected function handleError($response): void
    {
        $code  = $response->status();
        $error = $response->json();

        match ($code) {
            400     => throw new MonopolyApiException('Некорректные данные запроса', 400, $error),
            401     => throw new MonopolyApiException('Не авторизован', 401, $error),
            402     => throw new MonopolyApiException('Цена и цена поставщика не совпадает', 402, $error),
            404     => throw new MonopolyApiException('Ресурс не найден', 404, $error),
            409     => throw new MonopolyApiException('Повторный заказ', 409, $error),
            422     => throw new MonopolyApiException('Ошибка при оформлении заказа', 422, $error),
            default => throw new MonopolyApiException('Неизвестная ошибка API', $code, $error),
        };
    }

    /**
     * Получение списка АЗС
     *
     * @param  array               $params
     * @return Collection
     * @throws ConnectionException
     */
    public function getStations(array $params = []): Collection
    {
        $data = $this->handleRequest(fn() => $this->client()->get('/api/v1/fuel-stations', $params));

        return collect($data)->map(fn($item) => new FuelStationModel(
            id:             $item['id'],
            name:           $item['name'],
            brand:          $item['brand'],
            isAvailable:    $item['isAvailable'],
            address:        $item['address'],
            longitude:      (float) $item['longitude'],
            lattitude:      (float) $item['lattitude'],
            serviceMethods: array_map(fn($method) => ServiceMethodEnum::from($method), $item['serviceMethods']),
        ));
    }

    /**
     * Получение данных по указанной АЗС
     *
     * @param  string                  $id
     * @return FuelStationDetailsModel
     * @throws ConnectionException
     */
    public function getStation(string $id): FuelStationDetailsModel
    {
        $data = $this->handleRequest(fn() => $this->client()->get("/api/v1/fuel-stations/{$id}"));

        return new FuelStationDetailsModel(
            id:             $data['id'],
            name:           $data['name'],
            brand:          $data['brand'],
            isAvailable:    $data['isAvailable'],
            address:        $data['address'],
            longitude:      (float) $data['longitude'],
            lattitude:      (float) $data['lattitude'],
            paymentType:    PaymentTypeEnum::from($data['paymentType']),
            columns:        $data['columns'] ?? null,
            serviceMethods: $data['serviceMethods'],
        );
    }

    /**
     * Получение цен на топливо на указанной АЗС
     *
     * @param  string              $id
     * @return Collection
     * @throws ConnectionException
     */
    public function getStationPrices(string $id): Collection
    {
        $data = $this->handleRequest(fn() => $this->client()->get("/api/v1/fuel-stations/{$id}/prices"));

        return collect($data)->map(fn($item) => new FuelPriceModel(
            stationId:     $item['stationId'],
            fuelType:      FuelTypeEnum::from($item['fuelType']),
            fuelPrice:     (float) $item['fuelPrice'],
            serviceMethod: ServiceMethodEnum::from($item['serviceMethod']),
        ));
    }

    /**
     * Получение цен на топливо
     *
     * @return Collection
     * @throws ConnectionException
     */
    public function getAllStationsPrices(): Collection
    {
        $data = $this->handleRequest(fn() => $this->client()->get("/api/v1/fuel-stations/*/prices"));

        return collect($data)->map(fn($item) => new FuelPriceModel(
            stationId:     $item['stationId'],
            fuelType:      FuelTypeEnum::from($item['fuelType']),
            fuelPrice:     (float) $item['fuelPrice'],
            serviceMethod: ServiceMethodEnum::from($item['serviceMethod']),
        ));
    }

    /**
     * Получение статуса колонки
     *
     * @param  string                       $stationId
     * @param  string                       $columnId
     * @return FuelStationColumnStatusModel
     * @throws ConnectionException
     */
    public function getColumnStatus(string $stationId, string $columnId): FuelStationColumnStatusModel
    {
        $data = $this->handleRequest(fn() => $this->client()->get("/api/v1/fuel-stations/{$stationId}/columns/{$columnId}"));

        return new FuelStationColumnStatusModel(
            state:          ColumnStateEnum::from($data['state']),
            refueledVolume: $data['refueledVolume'] ?? null,
            fuelType:       isset($data['fuelType']) ? FuelTypeEnum::from($data['fuelType']) : null,
        );
    }

    /**
     * Создание заказа на налив топлива
     *
     * @param  string              $orderId
     * @param  array               $payload
     * @return array
     * @throws ConnectionException
     */
    public function createOrder(string $orderId, array $payload): array
    {
        return $this->handleRequest(fn() => $this->client()->post("/api/v1/orders/{$orderId}", $payload));
    }

    /**
     * Получение статуса заказа
     *
     * @param  string              $orderId
     * @return OrderStatusModel
     * @throws ConnectionException
     */
    public function getOrderStatus(string $orderId): OrderStatusModel
    {
        $data = $this->handleRequest(fn() => $this->client()->get("/api/v1/orders/{$orderId}"));

        return new OrderStatusModel(
            status:         OrderStatusEnum::from($data['status']),
            refueledVolume: $data['refueledVolume'] ?? null,
            cost:           $data['cost'] ?? null,
            cancelReason:   $data['cancelReason'] ?? null,
            updatedAt:      $data['updatedAt'],
        );
    }

    /**
     * Создание штрих или QR кода для заправки
     *
     * @param  string              $orderId
     * @return BarCodeModel
     * @throws ConnectionException
     */
    public function generateBarCode(string $orderId): BarCodeModel
    {
        $data = $this->handleRequest(fn() => $this->client()->put("/api/v1/orders/{$orderId}/bar-codes"));

        return new BarCodeModel(
            expiredAt: $data['expiredAt'],
            content:   $data['content'],
            type:      $data['type'],
        );
    }
}
